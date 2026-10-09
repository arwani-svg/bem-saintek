<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendaftaranPengurus extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_pengurus';

    public const STATUS = ['pending', 'diterima', 'ditolak'];

    protected $fillable = [
        'user_id',
        'gelombang_id',
        'nama',
        'nim',
        'prodi',
        'semester',
        'tempat_lahir',
        'tanggal_lahir',
        'asrama',
        'email',
        'tujuan',
        'riwayat_sekolah',
        'riwayat_madrasah',
        'riwayat_organisasi',
        'status',
        'catatan_seleksi',
    ];

    protected $appends = [
        'riwayat_sekolah_list',
        'riwayat_madrasah_list',
        'riwayat_organisasi_list',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected function casts(): array
    {
        return [
            'semester' => 'integer',
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gelombang(): BelongsTo
    {
        return $this->belongsTo(GelombangPendaftaran::class, 'gelombang_id');
    }

    protected function riwayatSekolahList(): Attribute
    {
        return Attribute::make(get: fn ($v, $attrs) => self::toList($attrs['riwayat_sekolah'] ?? null));
    }

    protected function riwayatMadrasahList(): Attribute
    {
        return Attribute::make(get: fn ($v, $attrs) => self::toList($attrs['riwayat_madrasah'] ?? null));
    }

    protected function riwayatOrganisasiList(): Attribute
    {
        return Attribute::make(get: fn ($v, $attrs) => self::toList($attrs['riwayat_organisasi'] ?? null));
    }

    public static function toList(mixed $raw): array
    {
        if (is_array($raw)) {
            return array_values(array_filter(array_map(fn ($v) => trim((string) $v), $raw)));
        }
        if (is_null($raw) || trim((string) $raw) === '') {
            return [];
        }
        $trimmed = trim((string) $raw);
        if (str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map(fn ($v) => trim((string) $v), $decoded)));
            }
        }
        $parts = preg_split('/\r\n|\r|\n/', $trimmed) ?: [];

        return array_values(array_filter(array_map(fn ($v) => trim((string) $v), $parts)));
    }

    public static function normalizeList(mixed $input, int $max = 30): ?string
    {
        if (is_null($input)) {
            return null;
        }
        if (is_string($input)) {
            $trimmed = trim($input);
            if ($trimmed === '') {
                return null;
            }
            if (str_starts_with($trimmed, '[')) {
                $decoded = json_decode($trimmed, true);
                $input = is_array($decoded) ? $decoded : (preg_split('/\r\n|\r|\n/', $trimmed) ?: []);
            } else {
                $input = preg_split('/\r\n|\r|\n/', $trimmed) ?: [];
            }
        }
        if (! is_array($input)) {
            return null;
        }
        $cleaned = [];
        foreach ($input as $item) {
            $point = trim((string) $item);
            if ($point !== '') {
                $cleaned[] = mb_substr($point, 0, 255);
            }
            if (count($cleaned) >= $max) {
                break;
            }
        }

        return $cleaned === [] ? null : implode("\n", $cleaned);
    }
}
