<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Daftar permission mengikuti fitur yang ada + rencana
     * (absensi, arsip, bendahara). Tambah permission baru cukup
     * tambah string di sini lalu jalankan seeder ulang.
     */
    public const PERMISSIONS = [
        'lihat-dashboard',
        'kelola-pengguna',
        'kelola-departemen',
        'kelola-proker',
        'kelola-informasi',
        'kelola-pendaftaran',
        'kelola-gelombang',
        'kelola-absensi',
        'kelola-arsip',
        'kelola-keuangan',
        'lihat-activity-log',
    ];

    /**
     * Deskripsi tiap permission untuk halaman Permissions.
     * Hanya diisi bila masih kosong — edit via dashboard tidak tertimpa.
     */
    public const PERMISSION_DESCRIPTIONS = [
        'lihat-dashboard' => 'Akses masuk ke halaman dashboard.',
        'kelola-pengguna' => 'Buat, ubah, dan hapus akun user serta atur role.',
        'kelola-departemen' => 'Kelola data departemen BEM.',
        'kelola-proker' => 'Kelola program kerja tiap departemen.',
        'kelola-informasi' => 'Kelola berita, infografis, dan pengumuman.',
        'kelola-pendaftaran' => 'Kelola pendaftar pengurus dan hasil seleksi.',
        'kelola-gelombang' => 'Buka/tutup periode pendaftaran pengurus.',
        'kelola-absensi' => 'Kelola kegiatan dan rekap kehadiran.',
        'kelola-arsip' => 'Kelola arsip surat dan dokumen.',
        'kelola-keuangan' => 'Kelola kas, pemasukan, dan pengeluaran bendahara.',
        'lihat-activity-log' => 'Melihat log aktivitas penting user (read-only).',
    ];

    /**
     * Role dasar + contoh jabatan. Jabatan baru tinggal tambah
     * entri di sini (nama role => daftar permission-nya).
     */
    public const ROLE_PERMISSIONS = [
        // Role legacy — dipertahankan agar 2 role sekarang tetap jalan.
        'mahasiswa' => [
            'lihat-dashboard',
        ],
        // Pengurus legacy = akses penuh (perilaku lama: semua halaman admin).
        'pengurus' => [
            'lihat-dashboard',
            'kelola-pengguna',
            'kelola-departemen',
            'kelola-proker',
            'kelola-informasi',
            'kelola-pendaftaran',
            'kelola-gelombang',
            'kelola-absensi',
            'kelola-arsip',
            'kelola-keuangan',
            'lihat-activity-log',
        ],
        // Contoh jabatan — siap dipakai / dimodifikasi sesuai struktur BEM.
        'ketua-bem' => [
            'lihat-dashboard',
            'kelola-pengguna',
            'kelola-departemen',
            'kelola-proker',
            'kelola-informasi',
            'kelola-pendaftaran',
            'kelola-gelombang',
            'kelola-absensi',
            'kelola-arsip',
            'kelola-keuangan',
            'lihat-activity-log',
        ],
        'sekretaris' => [
            'lihat-dashboard',
            'kelola-arsip',
            'kelola-informasi',
            'kelola-pendaftaran',
            'kelola-gelombang',
            'kelola-absensi',
        ],
        'bendahara' => [
            'lihat-dashboard',
            'kelola-keuangan',
            'kelola-arsip',
        ],
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Isi deskripsi yang masih kosong saja.
        foreach (self::PERMISSION_DESCRIPTIONS as $name => $description) {
            Permission::where('name', $name)->whereNull('description')->update(['description' => $description]);
        }

        foreach (self::ROLE_PERMISSIONS as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            // Hanya sinkron saat role baru dibuat — edit via halaman
            // dashboard tidak boleh tertimpa saat seeder dijalankan ulang.
            if ($role->wasRecentlyCreated) {
                $role->syncPermissions($perms);
            }
        }

        // Permission baru untuk role lama: seeder di atas hanya sync saat
        // role baru dibuat, jadi grant eksplisit agar DB lama ikut dapat.
        foreach (['pengurus', 'ketua-bem'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role && ! $role->hasPermissionTo('lihat-activity-log')) {
                $role->givePermissionTo('lihat-activity-log');
            }
        }

        // Migrasi data lama: kolom `role` (string) -> Spatie roles.
        // Idempotent: hanya assign bila user belum punya role Spatie.
        foreach (User::whereDoesntHave('roles')->cursor() as $user) {
            $legacy = $user->getAttribute('role');
            if ($legacy && Role::where('name', $legacy)->exists()) {
                $user->assignRole($legacy);
            } else {
                $user->assignRole('mahasiswa');
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
