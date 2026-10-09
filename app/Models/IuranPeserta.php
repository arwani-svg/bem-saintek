<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IuranPeserta extends Model
{
    use HasFactory;

    protected $fillable = [
        'iuran_id',
        'user_id',
        'nominal_wajib',
    ];

    protected function casts(): array
    {
        return ['nominal_wajib' => 'integer'];
    }

    public function iuran(): BelongsTo
    {
        return $this->belongsTo(Iuran::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(IuranPembayaran::class);
    }

    /** Total sudah dibayar peserta ini (mendukung cicilan). */
    public function totalBayar(): int
    {
        return (int) $this->pembayarans()->sum('jumlah');
    }

    public function sisa(): int
    {
        return max(0, (int) $this->nominal_wajib - $this->totalBayar());
    }

    /** belum | sebagian | lunas */
    public function statusBayar(): string
    {
        $bayar = $this->totalBayar();
        if ($bayar <= 0) {
            return 'belum';
        }
        if ($bayar < (int) $this->nominal_wajib) {
            return 'sebagian';
        }

        return 'lunas';
    }
}
