<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function pengurus(): User
    {
        $u = User::factory()->create(['role' => 'pengurus']);

        return $u;
    }

    public function test_pengurus_bisa_buka_keempat_halaman(): void
    {
        $pengurus = $this->pengurus();

        $this->actingAs($pengurus)->get('/roles')->assertOk();
        $this->actingAs($pengurus)->get('/roles/create')->assertOk();
        $this->actingAs($pengurus)->get('/permissions')->assertOk();
        $this->actingAs($pengurus)->get('/permissions/create')->assertOk();
    }

    public function test_mahasiswa_ditolak_buka_halaman_roles(): void
    {
        $mhs = User::factory()->create();

        $this->actingAs($mhs)->get('/roles')->assertForbidden();
        $this->actingAs($mhs)->get('/roles/create')->assertForbidden();
        $this->actingAs($mhs)->get('/permissions')->assertForbidden();
        $this->actingAs($mhs)->get('/permissions/create')->assertForbidden();
    }

    public function test_pengurus_bisa_buat_role_dengan_permission(): void
    {
        $this->actingAs($this->pengurus())->post('/roles', [
            'name' => 'kadep',
            'permissions' => ['kelola-absensi', 'kelola-arsip'],
        ])->assertRedirect('/roles');

        $role = Role::where('name', 'kadep')->first();
        $this->assertNotNull($role);
        $this->assertEqualsCanonicalizing(
            ['kelola-absensi', 'kelola-arsip'],
            $role->permissions->pluck('name')->toArray()
        );
    }

    public function test_role_sistem_tidak_bisa_dihapus(): void
    {
        $pengurus = Role::where('name', 'pengurus')->first();

        $this->actingAs($this->pengurus())
            ->delete("/roles/{$pengurus->id}")
            ->assertRedirect();

        $this->assertNotNull(Role::where('name', 'pengurus')->first());
    }

    public function test_hapus_role_custom_mengembalikan_user_ke_mahasiswa(): void
    {
        $role = Role::create(['name' => 'bendahara-tes', 'guard_name' => 'web']);
        $user = User::factory()->create(['role' => 'bendahara-tes']);
        $user->assignRole('bendahara-tes');

        $this->actingAs($this->pengurus())->delete("/roles/{$role->id}")->assertRedirect('/roles');

        $this->assertNull(Role::where('name', 'bendahara-tes')->first());
        $this->assertEquals('mahasiswa', $user->fresh()->role);
        $this->assertTrue($user->fresh()->hasRole('mahasiswa'));
    }

    public function test_deskripsi_permission_bisa_diubah_tanpa_ubah_nama(): void
    {
        $perm = Permission::where('name', 'kelola-arsip')->first();

        $this->actingAs($this->pengurus())->put("/permissions/{$perm->id}", [
            'description' => 'Deskripsi baru dari dashboard.',
        ])->assertRedirect('/permissions');

        $fresh = $perm->fresh();
        $this->assertEquals('kelola-arsip', $fresh->name);
        $this->assertEquals('Deskripsi baru dari dashboard.', $fresh->description);
    }

    public function test_permission_baru_bisa_ditambah_dan_dipasang_ke_role(): void
    {
        $this->actingAs($this->pengurus())->post('/permissions', [
            'name' => 'kelola-rapat',
        ])->assertRedirect('/permissions');

        $this->assertNotNull(Permission::where('name', 'kelola-rapat')->first());

        $role = Role::where('name', 'sekretaris')->first();
        $this->actingAs($this->pengurus())->put("/roles/{$role->id}", [
            'name' => 'sekretaris',
            'permissions' => ['lihat-dashboard', 'kelola-rapat'],
        ])->assertRedirect('/roles');

        $this->assertTrue($role->fresh()->hasPermissionTo('kelola-rapat'));
    }
}
