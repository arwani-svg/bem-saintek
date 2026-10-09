<?php

namespace Tests\Feature;

use App\Models\TransaksiKeuangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function bendahara(): User
    {
        $u = User::factory()->create(['role' => 'bendahara']);

        return $u;
    }

    public function test_mahasiswa_ditolak_buka_keuangan(): void
    {
        $mhs = User::factory()->create();

        $this->actingAs($mhs)->get('/keuangan')->assertForbidden();
        $this->actingAs($mhs)->post('/keuangan', [])->assertForbidden();
    }

    public function test_bendahara_crud_transaksi_dan_saldo_benar(): void
    {
        $b = $this->bendahara();

        $this->actingAs($b)->get('/keuangan')->assertOk();
        $this->actingAs($b)->get('/keuangan/create')->assertOk();

        // Uang masuk 100rb + 50rb, keluar 30rb.
        $this->actingAs($b)->post('/keuangan', [
            'tipe' => 'masuk',
            'kategori' => 'Kas Anggota',
            'keterangan' => 'Kas Januari',
            'jumlah' => 100000,
            'tanggal' => '2026-10-01',
        ])->assertRedirect('/keuangan');

        $this->actingAs($b)->post('/keuangan', [
            'tipe' => 'masuk',
            'kategori' => 'Lainnya',
            'kategori_lain' => 'Iuran Bazar',
            'jumlah' => 50000,
            'tanggal' => '2026-10-02',
        ])->assertRedirect('/keuangan');

        $this->actingAs($b)->post('/keuangan', [
            'tipe' => 'keluar',
            'kategori' => 'Konsumsi',
            'keterangan' => 'Snack rapat',
            'jumlah' => 30000,
            'tanggal' => '2026-10-03',
        ])->assertRedirect('/keuangan');

        // Kategori "Lainnya" tersimpan sebagai ketikan bebas.
        $this->assertDatabaseHas('transaksi_keuangans', ['kategori' => 'Iuran Bazar']);

        $this->assertEquals(150000, TransaksiKeuangan::totalMasuk());
        $this->assertEquals(30000, TransaksiKeuangan::totalKeluar());
        $this->assertEquals(120000, TransaksiKeuangan::saldo());

        // Update salah satu transaksi.
        $t = TransaksiKeuangan::where('kategori', 'Konsumsi')->first();
        $this->actingAs($b)->put("/keuangan/{$t->id}", [
            'tipe' => 'keluar',
            'kategori' => 'Konsumsi',
            'keterangan' => 'Snack rapat revisi',
            'jumlah' => 20000,
            'tanggal' => '2026-10-03',
        ])->assertRedirect('/keuangan');

        $this->assertEquals(130000, TransaksiKeuangan::saldo());

        // Delete.
        $this->actingAs($b)->delete("/keuangan/{$t->id}")->assertRedirect('/keuangan');
        $this->assertEquals(150000, TransaksiKeuangan::saldo());
    }

    public function test_validasi_jumlah_dan_tanggal(): void
    {
        $b = $this->bendahara();

        $this->actingAs($b)->post('/keuangan', [
            'tipe' => 'masuk',
            'kategori' => 'Kas Anggota',
            'jumlah' => 0,
            'tanggal' => '2026-10-01',
        ])->assertSessionHasErrors('jumlah');

        $this->actingAs($b)->post('/keuangan', [
            'tipe' => 'ngawur',
            'kategori' => 'Kas Anggota',
            'jumlah' => 10000,
            'tanggal' => '2026-10-01',
        ])->assertSessionHasErrors('tipe');

        $this->assertEquals(0, TransaksiKeuangan::count());
    }

    public function test_rekap_bisa_filter_bulan(): void
    {
        $b = $this->bendahara();

        TransaksiKeuangan::create([
            'tipe' => 'masuk', 'kategori' => 'Kas Anggota',
            'jumlah' => 100000, 'tanggal' => '2026-09-05', 'created_by' => $b->id,
        ]);
        TransaksiKeuangan::create([
            'tipe' => 'masuk', 'kategori' => 'Sponsorship',
            'jumlah' => 200000, 'tanggal' => '2026-10-05', 'created_by' => $b->id,
        ]);

        $res = $this->actingAs($b)->get('/keuangan/rekap?bulan=2026-10');
        $res->assertOk();
        // Hanya transaksi Oktober yang masuk ringkasan.
        $this->assertEquals(200000, $res->viewData('page')['props']['ringkasan']['masuk'] ?? null);
    }
}
