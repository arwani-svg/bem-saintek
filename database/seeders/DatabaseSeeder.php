<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Admin/Pengurus
        User::create([
            'name' => 'Admin BEM',
            'nim' => '2021001',
            'password' => Hash::make('password'),
            'role' => 'pengurus',
            'jenis_kelamin' => 'laki-laki',
            'prodi' => 'Teknologi Informasi',
            'angkatan' => 2021,
            'kelas' => 'A1',
            'status' => 'aktif',
            'privasi' => true,
        ])->assignRole('pengurus');

        // Mahasiswa
        $statuses = ['aktif', 'aktif', 'aktif', 'cuti', 'lulus'];

        for ($i = 1; $i <= 15; $i++) {
            User::create([
                'name' => "Mahasiswa $i",
                'nim' => '2024' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'jenis_kelamin' => $i % 2 === 0 ? 'perempuan' : 'laki-laki',
                'prodi' => User::PRODI[array_rand(User::PRODI)],
                'angkatan' => rand(2021, 2025),
                'kelas' => User::KELAS[array_rand(User::KELAS)],
                'status' => $statuses[array_rand($statuses)],
                'desa' => 'Desa Contoh',
                'kecamatan' => 'Kecamatan Contoh',
                'kabupaten' => 'Kabupaten Contoh',
                'provinsi' => 'Provinsi Contoh',
                'tempat_lahir' => 'Kota Contoh',
                'tanggal_lahir' => '2003-01-01',
                'email' => "mahasiswa{$i}@example.com",
                'nomor_telepon' => '081234567' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'kata_motivasi' => 'Terus semangat!',
                'prestasi' => null,
                'privasi' => (bool) rand(0, 1),
            ])->assignRole('mahasiswa');
        }

        $this->call(DepartemenSeeder::class);
    }
}
