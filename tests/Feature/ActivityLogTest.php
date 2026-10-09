<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'pengurus']);
    }

    public function test_login_dan_logout_tercatat(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['nim' => $user->nim, 'password' => 'password']);
        $this->assertDatabaseHas('activity_logs', ['activity' => 'login', 'user_id' => $user->id]);

        $this->actingAs($user)->post('/logout');
        $this->assertDatabaseHas('activity_logs', ['activity' => 'logout', 'user_id' => $user->id]);
    }

    public function test_crud_keuangan_tercatat_tanpa_data_sensitif(): void
    {
        $b = User::factory()->create(['role' => 'bendahara']);

        $this->actingAs($b)->post('/keuangan', [
            'tipe' => 'masuk',
            'kategori' => 'Kas Anggota',
            'keterangan' => 'Kas Januari',
            'jumlah' => 100000,
            'tanggal' => '2026-10-01',
        ])->assertRedirect('/keuangan');

        $log = ActivityLog::where('activity', 'create')->latest('id')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('Kas Anggota', $log->description);
        $this->assertStringNotContainsString('password', strtolower($log->description));
    }

    public function test_data_sensitif_disaring_dari_properties(): void
    {
        $admin = $this->admin();

        ActivityLog::record($admin, 'update', 'Uji saring', null, [
            'name' => 'Budi',
            'password' => 'rahasia123',
            'nested' => ['token' => 'abc', 'catatan' => 'ok'],
        ]);

        $log = ActivityLog::latest('id')->first();
        $this->assertSame('[disaring]', $log->properties['password']);
        $this->assertSame('[disaring]', $log->properties['nested']['token']);
        $this->assertSame('Budi', $log->properties['name']);
    }

    public function test_kegagalan_tulis_log_tidak_merusak_alur_utama(): void
    {
        // Properti tak valid untuk kolom json (resource) harus dibungkam.
        $log = ActivityLog::record(null, 'login', 'Uji tahan gagal', null, ['fd' => tmpfile() ?: 'x']);

        $this->assertNull($log);
    }

    public function test_hanya_admin_berakses_yang_bisa_melihat_log(): void
    {
        $mhs = User::factory()->create();
        $this->actingAs($mhs)->get('/activity-log')->assertForbidden();

        $this->actingAs($this->admin())->get('/activity-log')->assertOk();
    }

    public function test_halaman_log_menampilkan_25_per_halaman(): void
    {
        $admin = $this->admin();
        ActivityLog::factory()->count(30)->create(['user_id' => $admin->id]);

        $res = $this->actingAs($admin)->get('/activity-log');
        $res->assertOk();
        $page = $res->viewData('page')['props']['logs'];
        $this->assertSame(25, $page['per_page']);
        $this->assertSame(30, $page['total']);
    }

    public function test_perintah_prune_menghapus_log_lama(): void
    {
        $admin = $this->admin();
        ActivityLog::factory()->create(['user_id' => $admin->id, 'created_at' => now()->subDays(100)]);
        ActivityLog::factory()->create(['user_id' => $admin->id, 'created_at' => now()->subDays(10)]);

        $this->artisan('activity-log:prune')->assertSuccessful();
        $this->assertSame(1, ActivityLog::count());
    }
}
