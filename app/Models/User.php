<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public const PRODI = [
        'Teknologi Informasi',
        'Sistem Informasi',
        'Ilmu Komputer',
        'Arsitektur',
        'Budidaya Perikanan',
        'Teknologi Hasil Perikanan',
    ];

    public const STATUS = ['aktif', 'cuti', 'DO', 'keluar', 'lulus'];

    public const KELAS = ['A1', 'B1', 'C1', 'C2'];

    protected $fillable = [
        'name',
        'nim',
        'password',
        'role',
        'jenis_kelamin',
        'prodi',
        'angkatan',
        'kelas',
        'status',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'tempat_lahir',
        'tanggal_lahir',
        'email',
        'nomor_telepon',
        'kata_motivasi',
        'prestasi',
        'privasi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['semester', 'prestasi_list'];

    protected static function booted(): void
    {
        // Jaga konsistensi: setiap user baru otomatis dapat Spatie role
        // sesuai kolom `role` (fallback mahasiswa). Berlaku untuk semua
        // jalur pembuatan (form admin, factory, tinker, register).
        static::created(function (User $user) {
            $role = $user->getAttribute('role') ?: 'mahasiswa';
            try {
                if (! $user->hasAnyRole(Role::pluck('name')->toArray())) {
                    $user->assignRole(
                        Role::where('name', $role)->exists() ? $role : 'mahasiswa'
                    );
                }
            } catch (\Throwable) {
                // Tabel roles belum ada (mis. saat migrate fresh) — abaikan.
            }
        });
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'angkatan' => 'integer',
            'tanggal_lahir' => 'date',
            'privasi' => 'boolean',
        ];
    }

    public function isPengurus(): bool
    {
        // Cek Spatie dulu, fallback ke kolom legacy selama masa transisi.
        return $this->hasRole('pengurus') || $this->role === 'pengurus'
            || $this->hasAnyRole(['ketua-bem', 'wakil-ketua', 'sekretaris', 'bendahara']);
    }

    public function isMahasiswa(): bool
    {
        return $this->hasRole('mahasiswa') || $this->role === 'mahasiswa';
    }

    /** Tagihan iuran di mana user ini terdaftar sebagai peserta. */
    public function iuranPesertas(): HasMany
    {
        return $this->hasMany(IuranPeserta::class);
    }

    /** Kegiatan di mana user ini terdaftar sebagai peserta. */
    public function kegiatanPesertas(): HasMany
    {
        return $this->hasMany(KegiatanPeserta::class);
    }

    /**
     * Hitung semester berjalan dari angkatan.
     *
     * Patokan perkuliahan:
     * - Semester Ganjil: September - Februari
     * - Semester Genap: Maret - Agustus
     *
     * Rumus:
     * - Sep-Des tahun Y     => (Y - angkatan) * 2 + 1
     * - Jan-Feb tahun Y     => ((Y-1) - angkatan) * 2 + 1
     * - Mar-Agu tahun Y     => ((Y-1) - angkatan) * 2 + 2
     */
    protected function semester(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $angkatan = $attributes['angkatan'] ?? $this->angkatan ?? null;

                if (is_null($angkatan)) {
                    return null;
                }

                return self::hitungSemester((int) $angkatan, Carbon::now());
            },
        );
    }

    public static function hitungSemester(int $angkatan, ?Carbon $tanggal = null): int
    {
        $tanggal ??= Carbon::now();
        $bulan = (int) $tanggal->month;
        $tahun = (int) $tanggal->year;

        if ($bulan >= 9) {
            // Sep-Des: Ganjil tahun ajaran tahun/tahun+1
            $semester = ($tahun - $angkatan) * 2 + 1;
        } elseif ($bulan <= 2) {
            // Jan-Feb: masih Ganjil tahun ajaran (tahun-1)/tahun
            $semester = (($tahun - 1) - $angkatan) * 2 + 1;
        } else {
            // Mar-Agu: Genap tahun ajaran (tahun-1)/tahun
            $semester = (($tahun - 1) - $angkatan) * 2 + 2;
        }

        return max(1, $semester);
    }

    /**
     * Prestasi disimpan sebagai TEXT (satu point per baris).
     * Accessor ini mengembalikan array bersih agar kompatibel
     * dengan data lama (string biasa) maupun input baru (array).
     */
    protected function prestasiList(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $raw = $attributes['prestasi'] ?? null;

                if (is_array($raw)) {
                    return array_values(array_filter(array_map(fn ($v) => trim((string) $v), $raw)));
                }

                if (is_null($raw) || trim((string) $raw) === '') {
                    return [];
                }

                $trimmed = trim((string) $raw);

                // Dukung data yang terlanjur tersimpan sebagai JSON array.
                if (str_starts_with($trimmed, '[')) {
                    $decoded = json_decode($trimmed, true);
                    if (is_array($decoded)) {
                        return array_values(array_filter(array_map(fn ($v) => trim((string) $v), $decoded)));
                    }
                }

                $parts = preg_split('/\r\n|\r|\n/', $trimmed) ?: [];
                $parts = array_map(fn ($v) => trim((string) $v), $parts);

                return array_values(array_filter($parts));
            },
        );
    }

    /**
     * Normalisasi input prestasi (array/string/null) menjadi
     * string siap simpan (satu point per baris) atau null.
     */
    public static function normalizePrestasi(mixed $input): ?string
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
                if (is_array($decoded)) {
                    $input = $decoded;
                } else {
                    $input = preg_split('/\r\n|\r|\n/', $trimmed) ?: [];
                }
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
            if (count($cleaned) >= 50) {
                break;
            }
        }

        return $cleaned === [] ? null : implode("\n", $cleaned);
    }
}
