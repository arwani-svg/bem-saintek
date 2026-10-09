<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKeuangan extends Model
{
    use HasFactory;

    public const TIPE_MASUK = 'masuk';

    public const TIPE_KELUAR = 'keluar';

    public const TIPE = [self::TIPE_MASUK, self::TIPE_KELUAR];

    /**
     * Kategori bawaan — tambah baris baru di sini bila bendahara
     * butuh kategori lain. Tidak perlu ubah database (kolom string).
     * Di form, bendahara juga bisa ketik kategori sendiri ("Lainnya").
     */
    public const KATEGORI_MASUK = [
        'Kas Anggota',
        'Sponsorship',
        'Hibah',
        'Hasil Usaha',
        'Donasi',
        'Lainnya',
    ];

    public const KATEGORI_KELUAR = [
        'Konsumsi',
        'Transportasi',
        'ATK & Cetak',
        'Sewa Tempat & Alat',
        'Hadiah & Penghargaan',
        'Operasional',
        'Lainnya',
    ];

    protected $fillable = [
        'tipe',
        'kategori',
        'keterangan',
        'jumlah',
        'tanggal',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isMasuk(): bool
    {
        return $this->tipe === self::TIPE_MASUK;
    }

    public static function totalMasuk(): int
    {
        return (int) static::where('tipe', self::TIPE_MASUK)->sum('jumlah');
    }

    public static function totalKeluar(): int
    {
        return (int) static::where('tipe', self::TIPE_KELUAR)->sum('jumlah');
    }

    public static function saldo(): int
    {
        return static::totalMasuk() - static::totalKeluar();
    }
}
