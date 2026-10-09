<?php

namespace App\Console\Commands;

use App\Models\Kegiatan;
use Illuminate\Console\Command;

/**
 * Export hasil absensi satu kegiatan ke JSON berbasis NIM
 * (bukan ID database) agar bisa diimport ke database lain
 * tanpa bentrok ID — untuk skenario absen offline di lokal.
 *
 * Contoh: php artisan absensi:export 12 --file=/tmp/absen-makrab.json
 */
class AbsensiExport extends Command
{
    protected $signature = 'absensi:export
        {kegiatan : ID kegiatan yang diexport}
        {--file= : Tulis ke file (default: cetak ke layar)}';

    protected $description = 'Export hasil absensi ke JSON berbasis NIM (siap import lintas database)';

    public function handle(): int
    {
        $kegiatan = Kegiatan::with(['pesertas.user:id,name,nim', 'pesertas.kehadiran'])->find($this->argument('kegiatan'));

        if (! $kegiatan) {
            $this->error('Kegiatan tidak ditemukan.');

            return self::FAILURE;
        }

        $payload = [
            'app' => 'bem-saintek-absensi',
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'kegiatan' => [
                'nama' => $kegiatan->nama,
                'tanggal' => $kegiatan->tanggal->toDateString(),
            ],
            // Hanya yang sudah ditandai — yang belum tidak ikut campur saat import.
            'kehadiran' => $kegiatan->pesertas
                ->filter(fn ($p) => $p->kehadiran && $p->user)
                ->map(fn ($p) => [
                    'nim' => $p->user->nim,
                    'nama' => $p->user->name,
                    'status' => $p->kehadiran->status,
                    'keterangan' => $p->kehadiran->keterangan,
                ])
                ->values()
                ->all(),
        ];

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($file = $this->option('file')) {
            file_put_contents($file, $json);
            $this->info('Diexport '.count($payload['kehadiran'])." baris ke {$file}");
        } else {
            $this->line($json);
        }

        return self::SUCCESS;
    }
}
