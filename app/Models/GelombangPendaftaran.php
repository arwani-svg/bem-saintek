<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GelombangPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'gelombang_pendaftaran';

    protected $fillable = [
        'nama',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_open' => 'boolean',
        ];
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(PendaftaranPengurus::class, 'gelombang_id');
    }

    /** Sedang dibuka: flag buka + hari ini masuk rentang tanggal. */
    public function sedangDibuka(): bool
    {
        if (! $this->is_open) {
            return false;
        }
        $today = Carbon::today();
        $mulai = $this->tanggal_mulai ? Carbon::parse($this->tanggal_mulai)->startOfDay() : null;
        $selesai = $this->tanggal_selesai ? Carbon::parse($this->tanggal_selesai)->endOfDay() : null;

        return (! $mulai || $today->gte($mulai)) && (! $selesai || Carbon::now()->lte($selesai));
    }

    public function statusLabel(): string
    {
        if (! $this->is_open) {
            return 'Ditutup';
        }
        $today = Carbon::today();
        if ($this->tanggal_mulai && $today->lt(Carbon::parse($this->tanggal_mulai)->startOfDay())) {
            return 'Terjadwal';
        }
        if ($this->tanggal_selesai && $today->gt(Carbon::parse($this->tanggal_selesai)->endOfDay())) {
            return 'Berakhir';
        }

        return 'Dibuka';
    }

    public static function yangDibuka(): ?self
    {
        return static::where('is_open', true)->get()->first(fn ($g) => $g->sedangDibuka());
    }
}
