<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kehadiran extends Model
{
    use HasFactory;

    public const HADIR = 'hadir';

    public const IZIN = 'izin';

    public const SAKIT = 'sakit';

    public const ALPA = 'alpa';

    public const STATUS = [self::HADIR, self::IZIN, self::SAKIT, self::ALPA];

    /** Status yang tidak menambah poin pelanggaran. */
    public const TANPA_POIN = [self::HADIR, self::IZIN, self::SAKIT];

    protected $fillable = [
        'kegiatan_peserta_id',
        'status',
        'keterangan',
        'dicatat_oleh',
    ];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(KegiatanPeserta::class, 'kegiatan_peserta_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    /** Poin pelanggaran baris ini (alpa = poin kegiatan, sisanya 0). */
    public function poin(int $poinAlpa): int
    {
        return $this->status === self::ALPA ? $poinAlpa : 0;
    }
}
