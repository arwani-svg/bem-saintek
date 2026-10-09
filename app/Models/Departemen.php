<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departemen extends Model
{
    protected $fillable = [
        'nama',
        'kepanjangan',
        'deskripsi',
        'tujuan',
        'icon',
    ];

    public function programKerjas(): HasMany
    {
        return $this->hasMany(ProgramKerja::class);
    }
}
