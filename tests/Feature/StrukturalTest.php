<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Struktural;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StrukturalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function pengurus(): User
    {
        return User::factory()->pengurus()->create();
    }

    private function departemen(): Departemen
    {
        return Departemen::create([
            'nama' => 'Informasi dan Komunikasi',
            'icon' => '📣',
        ]);
    }

    public function test_pengurus_bisa_membuka_manajemen_struktural(): void
    {
        $this->actingAs($this->pengurus())
            ->get('/struktural')
            ->assertOk();
    }

    public function test_mahasiswa_ditolak_membuka_manajemen_struktural(): void
    {
        $mahasiswa = User::factory()->create();

        $this->actingAs($mahasiswa)
            ->get('/struktural')
            ->assertForbidden();
    }

    public function test_pengurus_bisa_membuat_memperbarui_dan_menghapus_struktural(): void
    {
        $pengurus = $this->pengurus();
        $user = User::factory()->create(['name' => 'Arwani']);
        $departemen = $this->departemen();

        $this->actingAs($pengurus)->post('/struktural', [
            'user_id' => $user->id,
            'departemen_id' => $departemen->id,
            'jabatan' => 'Kepala Departemen',
            'periode' => '2026/2027',
            'urutan' => 2,
        ])->assertRedirect('/struktural');

        $struktural = Struktural::firstOrFail();
        $this->assertSame('Arwani', $struktural->user->name);
        $this->assertSame('Informasi dan Komunikasi', $struktural->departemen->nama);

        $this->actingAs($pengurus)->put("/struktural/{$struktural->id}", [
            'user_id' => $user->id,
            'departemen_id' => null,
            'jabatan' => 'Sekretaris',
            'periode' => '2026/2027',
            'urutan' => 1,
        ])->assertRedirect('/struktural');

        $struktural->refresh();
        $this->assertNull($struktural->departemen_id);
        $this->assertSame('Sekretaris', $struktural->jabatan);

        $this->actingAs($pengurus)
            ->delete("/struktural/{$struktural->id}")
            ->assertRedirect('/struktural');

        $this->assertDatabaseCount('strukturals', 0);
    }

    public function test_halaman_publik_menampilkan_struktur_dari_database(): void
    {
        $departemen = $this->departemen();
        $ketua = User::factory()->create([
            'name' => 'Arwani',
            'prodi' => 'Teknologi Informasi',
            'nim' => '2024001',
        ]);
        $kadep = User::factory()->create([
            'name' => 'Budi',
            'prodi' => 'Sistem Informasi',
            'nim' => '2024002',
        ]);

        Struktural::create([
            'user_id' => $ketua->id,
            'departemen_id' => null,
            'jabatan' => 'Ketua BEM',
            'periode' => '2026/2027',
            'urutan' => 1,
        ]);

        Struktural::create([
            'user_id' => $kadep->id,
            'departemen_id' => $departemen->id,
            'jabatan' => 'Kepala Departemen',
            'periode' => '2026/2027',
            'urutan' => 1,
        ]);

        $this->get('/profile/struktural')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/Struktural')
                ->where('selectedPeriode', '2026/2027')
                ->where('totalPengurus', 2)
                ->where('pengurusHarian.0.user.name', 'Arwani')
                ->where('pengurusHarian.0.user.prodi', 'Teknologi Informasi')
                ->has('departemens', 1)
                ->where('departemens.0.nama', 'Informasi dan Komunikasi')
                ->where('departemens.0.pengurus.0.user.name', 'Budi')
                ->where('departemens.0.pengurus.0.jabatan', 'Kepala Departemen')
            );
    }
}
