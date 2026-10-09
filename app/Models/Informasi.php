<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Informasi extends Model
{
    protected $table = 'informasis';

    protected $fillable = [
        'jenis',
        'judul',
        'slug',
        'ringkasan',
        'konten',
        'gambar',
        'tanggal_publikasi',
        'is_published',
        'is_unggulan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_publikasi' => 'date',
            'is_published' => 'boolean',
            'is_unggulan' => 'boolean',
        ];
    }

    public const JENIS = [
        'berita' => 'Berita',
        'infografis' => 'Infografis',
        'penghargaan' => 'Penghargaan',
        'lomba' => 'Lomba',
    ];

    /** Jenis yang memakai narasi panjang (konten), sisanya gambar + caption singkat. */
    public const JENIS_ARTIKEL = ['berita', 'penghargaan'];

    public const JENIS_GAMBAR = ['infografis', 'lomba'];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOfJenis($query, ?string $jenis)
    {
        return $jenis ? $query->where('jenis', $jenis) : $query;
    }

    public function isArtikel(): bool
    {
        return in_array($this->jenis, self::JENIS_ARTIKEL, true);
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? Storage::disk('public')->url($this->gambar) : null;
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }
}
