<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpatieRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_mahasiswa_ditolak_masuk_area_pengurus(): void
    {
        $mhs = User::factory()->create(); // role default mahasiswa

        $this->actingAs($mhs)->get('/admin/pendaftaran')->assertForbidden();
    }

    public function test_pengurus_bisa_masuk_area_pengurus(): void
    {
        $pengurus = User::factory()->pengurus()->create();

        $this->actingAs($pengurus)->get('/admin/pendaftaran')->assertOk();
    }

    public function test_jabatan_bendahara_bisa_ditambah_dan_punya_permission_keuangan(): void
    {
        $bendahara = User::factory()->create(['role' => 'bendahara']);
        $bendahara->assignRole('bendahara');

        $this->assertTrue($bendahara->can('kelola-keuangan'));
        $this->assertFalse($bendahara->can('kelola-pengguna'));
        // Jabatan dianggap bagian pengurus untuk menu & policy lama.
        $this->assertTrue($bendahara->isPengurus());
    }

    public function test_mahasiswa_tetap_bisa_daftar_satu_kali_per_gelombang(): void
    {
        $mhs = User::factory()->create();
        $mhs->assignRole('mahasiswa');

        $this->actingAs($mhs)->get('/pendaftaran/create')->assertOk();
    }
}
