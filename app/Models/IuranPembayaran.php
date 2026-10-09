<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IuranPembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'iuran_peserta_id',
        'jumlah',
        'tanggal',
        'keterangan',
        'transaksi_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(IuranPeserta::class, 'iuran_peserta_id');
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiKeuangan::class, 'transaksi_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
