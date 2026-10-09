<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Kegiatan extends Model
{
    use HasFactory;

    public const STATUS_TERJADWAL = 'terjadwal';

    public const STATUS_BERLANGSUNG = 'berlangsung';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUS = [self::STATUS_TERJADWAL, self::STATUS_BERLANGSUNG, self::STATUS_SELESAI, self::STATUS_DIBATALKAN];

    protected $fillable = [
        'nama',
        'deskripsi',
        'tanggal',
        'waktu_mulai',
        'lokasi',
        'poin_alpa',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'poin_alpa' => 'integer',
        ];
    }

    public function pesertas(): HasMany
    {
        return $this->hasMany(KegiatanPeserta::class);
    }

    public function kehadirans(): HasManyThrough
    {
        return $this->hasManyThrough(Kehadiran::class, KegiatanPeserta::class, 'kegiatan_id', 'kegiatan_peserta_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
