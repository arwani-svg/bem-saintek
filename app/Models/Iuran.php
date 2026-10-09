<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Iuran extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUS = [self::STATUS_AKTIF, self::STATUS_SELESAI, self::STATUS_DIBATALKAN];

    protected $fillable = [
        'nama',
        'deskripsi',
        'nominal',
        'jatuh_tempo',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'jatuh_tempo' => 'date',
        ];
    }

    public function pesertas(): HasMany
    {
        return $this->hasMany(IuranPeserta::class);
    }

    public function pembayarans(): HasManyThrough
    {
        return $this->hasManyThrough(IuranPembayaran::class, IuranPeserta::class, 'iuran_id', 'iuran_peserta_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Total tagihan = sum nominal_wajib semua peserta. */
    public function totalTarget(): int
    {
        return (int) $this->pesertas()->sum('nominal_wajib');
    }

    /** Total terkumpul = sum semua pembayaran peserta. */
    public function totalTerkumpul(): int
    {
        return (int) $this->pembayarans()->sum('jumlah');
    }
}
