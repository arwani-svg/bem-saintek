<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartemenCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_edit_menampilkan_data_sebelumnya_update_dan_delete_berfungsi(): void
    {
        $pengurus = User::factory()->create(['role' => 'pengurus']);
        $departemen = Departemen::create([
            'nama' => 'PSDM',
            'kepanjangan' => 'Pengembangan Sumber Daya Manusia',
            'deskripsi' => 'Deskripsi awal',
            'icon' => '🧠',
        ]);

        // 1. Halaman edit harus memuat data sebelumnya (bukan form kosong).
        $this->actingAs($pengurus)
            ->get("/departemen/{$departemen->id}/edit")
            ->assertOk()
            ->assertSee('PSDM', false)
            ->assertSee('Pengembangan Sumber Daya Manusia', false);

        // 2. Update harus benar-benar mengubah data.
        $this->actingAs($pengurus)
            ->put("/departemen/{$departemen->id}", [
                'nama' => 'PSDM Updated',
                'kepanjangan' => 'Pengembangan Sumber Daya Manusia',
                'deskripsi' => 'Deskripsi baru',
                'tujuan' => null,
                'icon' => '🧠',
            ])
            ->assertRedirect('/departemen');

        $this->assertDatabaseHas('departemens', [
            'id' => $departemen->id,
            'nama' => 'PSDM Updated',
            'deskripsi' => 'Deskripsi baru',
        ]);

        // 3. Delete harus benar-benar menghapus data.
        $this->actingAs($pengurus)
            ->delete("/departemen/{$departemen->id}")
            ->assertRedirect('/departemen');

        $this->assertDatabaseMissing('departemens', ['id' => $departemen->id]);
    }
}
