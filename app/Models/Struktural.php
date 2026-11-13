<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Struktural extends Model
{
    protected $fillable = [
        'user_id',
        'departemen_id',
        'jabatan',
        'periode',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function scopePeriode($query, string $periode)
    {
        return $query->where('periode', $periode);
    }

    public function isPengurusHarian(): bool
    {
        return is_null($this->departemen_id);
    }
}
