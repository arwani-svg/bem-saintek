<?php

namespace App\Services;

use App\Models\User;

/**
 * Menyusun riwayat transparansi untuk halaman profil:
 * kegiatan yang diikuti (status kehadiran + poin) dan
 * tagihan iuran (sudah bayar vs sisa). Satu sumber agar
 * Profil Saya dan Admin User Show selalu konsisten.
 */
class ProfilRiwayat
{
    public static function untuk(User $user): array
    {
        // --- Kegiatan: user sebagai peserta ---
        $kegiatanPesertas = $user->kegiatanPesertas()
            ->with(['kegiatan:id,nama,tanggal,status,poin_alpa', 'kehadiran'])
            ->get()
            ->sortByDesc(fn ($p) => optional($p->kegiatan)->tanggal?->toDateString() ?? '')
            ->values();

        $riwayatKegiatan = $kegiatanPesertas->map(function ($p) {
            $k = $p->kegiatan;
            $status = $p->kehadiran?->status; // null = belum ditandai
            $poin = $status === 'alpa' ? (int) ($k?->poin_alpa ?? 0) : 0;

            return [
                'kegiatan_id' => $p->kegiatan_id,
                'nama' => $k?->nama ?? '(kegiatan dihapus)',
                'tanggal' => $k?->tanggal,
                'status_kegiatan' => $k?->status,
                'status_hadir' => $status, // hadir|izin|sakit|alpa|null(belum)
                'poin' => $poin,
            ];
        });

        // --- Iuran: user sebagai peserta ---
        $iuranPesertas = $user->iuranPesertas()
            ->with(['iuran:id,nama,status', 'pembayarans'])
            ->get()
            ->sortByDesc('id')
            ->values();

        $tagihanIuran = $iuranPesertas->map(function ($p) {
            $bayar = (int) $p->pembayarans->sum('jumlah');
            $wajib = (int) $p->nominal_wajib;

            return [
                'iuran_id' => $p->iuran_id,
                'nama' => $p->iuran?->nama ?? '(iuran dihapus)',
                'status_iuran' => $p->iuran?->status,
                'nominal_wajib' => $wajib,
                'total_bayar' => $bayar,
                'sisa' => max(0, $wajib - $bayar),
                'status_bayar' => $bayar <= 0 ? 'belum' : ($bayar < $wajib ? 'sebagian' : 'lunas'),
            ];
        });

        return [
            'riwayatKegiatan' => $riwayatKegiatan,
            'tagihanIuran' => $tagihanIuran,
            'totalPoin' => (int) $riwayatKegiatan->sum('poin'),
            'totalSisaIuran' => (int) $tagihanIuran->sum('sisa'),
        ];
    }
}
