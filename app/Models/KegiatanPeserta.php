<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KegiatanPeserta extends Model
{
    use HasFactory;

    protected $fillable = [
        'kegiatan_id',
        'user_id',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kehadiran(): HasOne
    {
        return $this->hasOne(Kehadiran::class);
    }
}
