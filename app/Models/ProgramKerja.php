<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramKerja extends Model
{
    protected $table = 'program_kerjas';

    protected $fillable = [
        'departemen_id',
        'judul',
        'deskripsi',
        'kategori',
        'warna',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'penanggung_jawab',
        'is_unggulan',
    ];

    protected function casts(): array
    {
        return [
            'is_unggulan' => 'boolean',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public const STATUS = [
        'akan_datang' => 'Akan Datang',
        'berlangsung' => 'Berlangsung',
        'selesai' => 'Selesai',
    ];

    public const WARNA = ['blue', 'green', 'purple', 'orange', 'pink', 'indigo'];

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }
}
