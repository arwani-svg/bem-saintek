<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_tamu_ditolak_buka_profil(): void
    {
        $this->get('/profil')->assertRedirect('/login');
    }

    public function test_mahasiswa_bisa_lihat_dan_edit_data_diperbolehkan(): void
    {
        $mhs = User::factory()->create(['name' => 'Afandi Tes']);

        $this->actingAs($mhs)->get('/profil')->assertOk()->assertSee('Afandi Tes', false);
        $this->actingAs($mhs)->get('/profil/edit')->assertOk();

        $this->actingAs($mhs)->put('/profil', [
            'name' => 'Afandi Updated',
            'email' => 'afandi@contoh.com',
            'nomor_telepon' => '08123456789',
            'jenis_kelamin' => 'laki-laki',
            'tempat_lahir' => 'Demak',
            'tanggal_lahir' => '2003-05-01',
            'desa' => 'Bango',
            'kata_motivasi' => 'Semangat!',
            'privasi' => true,
        ])->assertRedirect('/profil');

        $fresh = $mhs->fresh();
        $this->assertEquals('Afandi Updated', $fresh->name);
        $this->assertEquals('afandi@contoh.com', $fresh->email);
        $this->assertEquals('08123456789', $fresh->nomor_telepon);
        $this->assertEquals('Demak', $fresh->tempat_lahir);
        $this->assertTrue((bool) $fresh->privasi);
    }

    public function test_mahasiswa_tidak_bisa_ubah_prestasi_nim_dan_role(): void
    {
        $mhs = User::factory()->create([
            'nim' => '2024000999',
            'prestasi' => null,
        ]);

        $this->actingAs($mhs)->put('/profil', [
            'name' => $mhs->name,
            'jenis_kelamin' => $mhs->jenis_kelamin,
            'nim' => '9999999999',
            'role' => 'pengurus',
            'prodi' => 'Arsitektur',
            'status' => 'lulus',
            'prestasi' => ['Juara Palsu 2024'],
        ])->assertRedirect('/profil');

        $fresh = $mhs->fresh();
        $this->assertEquals('2024000999', $fresh->nim);
        $this->assertEquals('mahasiswa', $fresh->role);
        $this->assertNotEquals('Arsitektur', $fresh->prodi);
        $this->assertNotEquals('lulus', $fresh->status);
        $this->assertNull($fresh->prestasi);
        $this->assertFalse($fresh->hasRole('pengurus'));
    }

    public function test_mahasiswa_bisa_ganti_password(): void
    {
        $mhs = User::factory()->create();

        $this->actingAs($mhs)->put('/profil', [
            'name' => $mhs->name,
            'jenis_kelamin' => $mhs->jenis_kelamin,
            'password' => 'rahasia-baru',
            'password_confirmation' => 'rahasia-baru',
        ])->assertRedirect('/profil');

        $this->assertTrue(Hash::check('rahasia-baru', $mhs->fresh()->password));
    }

    public function test_pengurus_bisa_ubah_prestasi_sendiri(): void
    {
        $pengurus = User::factory()->pengurus()->create();

        $this->actingAs($pengurus)->put('/profil', [
            'name' => $pengurus->name,
            'jenis_kelamin' => $pengurus->jenis_kelamin,
            'prestasi' => ['Juara 1 PKM 2024', 'Best Speaker LKMM'],
        ])->assertRedirect('/profil');

        $this->assertEquals(
            ['Juara 1 PKM 2024', 'Best Speaker LKMM'],
            $pengurus->fresh()->prestasi_list
        );
    }
}
