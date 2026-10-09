<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'nim' => fake()->unique()->numerify('2024#####'),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'mahasiswa',
            'jenis_kelamin' => fake()->randomElement(['laki-laki', 'perempuan']),
            'prodi' => fake()->randomElement(User::PRODI),
            'angkatan' => fake()->randomElement([2022, 2023, 2024, 2025]),
            'kelas' => fake()->optional()->randomElement(User::KELAS),
            'status' => 'aktif',
            'desa' => fake()->optional()->word(),
            'kecamatan' => fake()->optional()->word(),
            'kabupaten' => fake()->optional()->word(),
            'provinsi' => fake()->optional()->word(),
            'tempat_lahir' => fake()->optional()->city(),
            'tanggal_lahir' => fake()->optional()->date(),
            'email' => fake()->boolean() ? fake()->unique()->safeEmail() : null,
            'nomor_telepon' => fake()->optional()->phoneNumber(),
            'kata_motivasi' => fake()->optional()->sentence(),
            'prestasi' => fake()->optional()->sentence(),
            'privasi' => fake()->boolean(),
            'remember_token' => Str::random(10),
        ];
    }

    public function pengurus(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'pengurus',
        ]);
    }
}
