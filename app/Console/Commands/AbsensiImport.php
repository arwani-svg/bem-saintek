<?php

namespace App\Console\Commands;

use App\Models\Kegiatan;
use App\Models\KegiatanPeserta;
use App\Models\Kehadiran;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

/**
 * Import hasil absensi offline (file dari absensi:export).
 * Penggabungan berbasis NIM + nama/tanggal kegiatan — ID auto-increment
 * lokal TIDAK PERNAH dipakai, jadi file dari 2+ laptop berbeda
 * bisa diimport berurutan tanpa bentrok.
 *
 * Aturan timpa: file yang diimport belakangan menang per orang
 * (dilaporkan sebagai "ditimpa" agar admin bisa verifikasi).
 *
 * Contoh: php artisan absensi:import /tmp/absen-makrab.json --buat-kegiatan
 */
class AbsensiImport extends Command
{
    protected $signature = 'absensi:import
        {file : Path file JSON hasil absensi:export}
        {--kegiatan= : ID kegiatan tujuan (default: cari by nama+tanggal)}
        {--buat-kegiatan : Buat kegiatan bila belum ada (peserta dibuat otomatis dari NIM)}';

    protected $description = 'Import absensi offline berbasis NIM (aman untuk multi-operator)';

    public function handle(): int
    {
        $path = $this->argument('file');
        if (! is_file($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);
        if (! is_array($data) || ($data['app'] ?? null) !== 'bem-saintek-absensi' || ! isset($data['kegiatan'], $data['kehadiran'])) {
            $this->error('Format file tidak valid (bukan hasil absensi:export).');

            return self::FAILURE;
        }

        $kegiatan = $this->option('kegiatan')
            ? Kegiatan::find($this->option('kegiatan'))
            : Kegiatan::where('nama', $data['kegiatan']['nama'] ?? '')
                ->whereDate('tanggal', $data['kegiatan']['tanggal'] ?? '1970-01-01')
                ->first();

        if (! $kegiatan && $this->option('buat-kegiatan')) {
            $kegiatan = Kegiatan::create([
                'nama' => $data['kegiatan']['nama'],
                'tanggal' => $data['kegiatan']['tanggal'],
                'status' => Kegiatan::STATUS_SELESAI,
                'created_by' => Auth::id(), // null saat CLI — kolom nullable
            ]);
            $this->info("Kegiatan baru dibuat: {$kegiatan->nama} (ID {$kegiatan->id}).");
        }

        if (! $kegiatan) {
            $this->error('Kegiatan tujuan tidak ditemukan. Pakai --kegiatan=ID atau --buat-kegiatan.');

            return self::FAILURE;
        }

        $baru = 0;
        $ditimpa = 0;
        $sama = 0;
        $takDikenal = [];

        foreach ($data['kehadiran'] as $baris) {
            if (! in_array($baris['status'] ?? null, Kehadiran::STATUS, true)) {
                $takDikenal[] = ($baris['nim'] ?? '?').' (status tidak valid)';
                continue;
            }

            $user = User::where('nim', $baris['nim'] ?? null)->first();
            if (! $user) {
                $takDikenal[] = ($baris['nim'] ?? '?').' ('.($baris['nama'] ?? '?').')';
                continue;
            }

            $peserta = KegiatanPeserta::firstOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'user_id' => $user->id]
            );

            $lama = Kehadiran::where('kegiatan_peserta_id', $peserta->id)->first();
            if ($lama && $lama->status === $baris['status']) {
                $sama++;
                continue;
            }

            Kehadiran::updateOrCreate(
                ['kegiatan_peserta_id' => $peserta->id],
                ['status' => $baris['status'], 'keterangan' => $baris['keterangan'] ?? null, 'dicatat_oleh' => Auth::id()]
            );

            $lama ? $ditimpa++ : $baru++;
        }

        $this->info("Import ke '{$kegiatan->nama}': {$baru} baru, {$ditimpa} ditimpa (file menang), {$sama} sudah sama.");
        if ($takDikenal !== []) {
            $this->warn('NIM tidak dikenal ('.count($takDikenal).'), dilewati:');
            foreach ($takDikenal as $nim) {
                $this->line("  - {$nim}");
            }
        }

        return self::SUCCESS;
    }
}
