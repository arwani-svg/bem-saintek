<?php

namespace Tests\Feature;

use App\Models\Kegiatan;
use App\Models\KegiatanPeserta;
use App\Models\Kehadiran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function operator(): User
    {
        return User::factory()->create(['role' => 'pengurus']);
    }

    public function test_mahasiswa_ditolak_buka_absensi(): void
    {
        $mhs = User::factory()->create();

        $this->actingAs($mhs)->get('/kegiatan')->assertForbidden();
        $this->actingAs($mhs)->post('/kegiatan', [])->assertForbidden();
        $this->actingAs($mhs)->get('/absensi/rekap')->assertForbidden();
    }

    public function test_buat_kegiatan_dengan_peserta(): void
    {
        $op = $this->operator();
        $a = User::factory()->create();
        $b = User::factory()->create();

        $this->actingAs($op)->post('/kegiatan', [
            'nama' => 'Rapat Rutin',
            'tanggal' => '2026-10-09',
            'waktu_mulai' => '19:00',
            'lokasi' => 'Sekretariat',
            'poin_alpa' => 10,
            'peserta_ids' => [$a->id, $b->id],
        ])->assertRedirect();

        $k = Kegiatan::where('nama', 'Rapat Rutin')->first();
        $this->assertNotNull($k);
        $this->assertEquals(2, $k->pesertas()->count());
        $this->assertEquals('terjadwal', $k->status);

        $this->actingAs($op)->get('/kegiatan')->assertOk();
        $this->actingAs($op)->get("/kegiatan/{$k->id}")->assertOk();
        // Kunjungan pertama ke halaman absen mengubah status jadi berlangsung.
        $this->actingAs($op)->get("/kegiatan/{$k->id}/absen")->assertOk();
        $this->assertEquals('berlangsung', $k->fresh()->status);
    }

    public function test_tandai_hadir_dan_rekap_poin_benar(): void
    {
        $op = $this->operator();
        $hadir = User::factory()->create(['name' => 'Arwani Hadir']);
        $alpa = User::factory()->create(['name' => 'Budi Alpa']);

        $k = Kegiatan::create([
            'nama' => 'Makrab', 'tanggal' => '2026-10-09',
            'poin_alpa' => 15, 'status' => 'berlangsung', 'created_by' => $op->id,
        ]);
        $pHadir = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $hadir->id]);
        $pAlpa = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $alpa->id]);

        // Tandai via JSON seperti halaman absen cepat.
        $res = $this->actingAs($op)->postJson("/kegiatan/{$k->id}/tandai", [
            'peserta_id' => $pHadir->id, 'status' => 'hadir',
        ])->assertOk();
        $this->assertEquals('hadir', $res->json('status'));
        $this->assertEquals(1, $res->json('ringkasan.hadir'));

        $this->actingAs($op)->postJson("/kegiatan/{$k->id}/tandai", [
            'peserta_id' => $pAlpa->id, 'status' => 'alpa',
        ])->assertOk();

        // Ubah status (koreksi salah ketik) — upsert, tidak dobel.
        $this->actingAs($op)->postJson("/kegiatan/{$k->id}/tandai", [
            'peserta_id' => $pHadir->id, 'status' => 'izin', 'keterangan' => 'Surat masuk',
        ])->assertOk();
        $this->assertEquals(1, Kehadiran::where('kegiatan_peserta_id', $pHadir->id)->count());
        $this->assertEquals('izin', Kehadiran::where('kegiatan_peserta_id', $pHadir->id)->first()->status);

        // Status ngawur ditolak.
        $this->actingAs($op)->postJson("/kegiatan/{$k->id}/tandai", [
            'peserta_id' => $pHadir->id, 'status' => 'ngawur',
        ])->assertUnprocessable();

        // Rekap: Budi 15 poin, Arwani 0.
        $res = $this->actingAs($op)->get('/absensi/rekap');
        $res->assertOk();
        $rows = $res->viewData('page')['props']['rows'] ?? collect();
        $byName = collect($rows)->keyBy('nama');
        $this->assertEquals(15, $byName['Budi Alpa']['poin']);
        $this->assertEquals(1, $byName['Budi Alpa']['alpa']);
        $this->assertEquals(0, $byName['Arwani Hadir']['poin']);
    }

    public function test_tandai_sisa_alpa(): void
    {
        $op = $this->operator();
        $a = User::factory()->create();
        $b = User::factory()->create();

        $k = Kegiatan::create([
            'nama' => 'Upacara', 'tanggal' => '2026-10-09',
            'poin_alpa' => 10, 'status' => 'berlangsung', 'created_by' => $op->id,
        ]);
        $pA = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $a->id]);
        KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $b->id]);

        $this->actingAs($op)->postJson("/kegiatan/{$k->id}/tandai", [
            'peserta_id' => $pA->id, 'status' => 'hadir',
        ])->assertOk();

        $res = $this->actingAs($op)->postJson("/kegiatan/{$k->id}/tandai-sisa-alpa")->assertOk();
        $this->assertEquals(1, $res->json('ditandai'));
        $this->assertEquals(1, $res->json('ringkasan.alpa'));
        $this->assertEquals(0, $res->json('ringkasan.belum'));
    }

    public function test_cari_peserta_limit_dan_belum_dulu(): void
    {
        $op = $this->operator();
        $k = Kegiatan::create([
            'nama' => 'Rapat Akbar', 'tanggal' => '2026-10-09',
            'poin_alpa' => 10, 'status' => 'berlangsung', 'created_by' => $op->id,
        ]);
        $users = User::factory()->count(10)->create();
        foreach ($users as $u) {
            KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $u->id]);
        }
        // Tandai 1 orang hadir — dia harus tersingkir ke belakang antrian.
        $pertama = KegiatanPeserta::where('kegiatan_id', $k->id)->first();
        Kehadiran::create(['kegiatan_peserta_id' => $pertama->id, 'status' => 'hadir']);

        $res = $this->actingAs($op)->getJson("/kegiatan/{$k->id}/cari-peserta");
        $res->assertOk();
        $rows = $res->json();
        // Max 8 baris walau peserta 10 (RAM aman).
        $this->assertCount(8, $rows);
        // Semua yang dikembalikan belum ditandai.
        foreach ($rows as $r) {
            $this->assertNull($r['status']);
        }

        // Cari by nama tetap jalan.
        $nama = $users[3]->name;
        $res = $this->actingAs($op)->getJson("/kegiatan/{$k->id}/cari-peserta?q=".urlencode(mb_substr($nama, 0, 4)));
        $res->assertOk();
        $this->assertNotEmpty($res->json());
    }

    public function test_export_import_berbasis_nim_tanpa_bentrok_id(): void
    {
        $op = $this->operator();
        $a = User::factory()->create();
        $b = User::factory()->create();

        $k = Kegiatan::create([
            'nama' => 'Makrab Offline', 'tanggal' => '2026-10-09',
            'poin_alpa' => 10, 'status' => 'berlangsung', 'created_by' => $op->id,
        ]);
        $pA = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $a->id]);
        $pB = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $b->id]);
        Kehadiran::create(['kegiatan_peserta_id' => $pA->id, 'status' => 'hadir']);
        Kehadiran::create(['kegiatan_peserta_id' => $pB->id, 'status' => 'alpa']);

        // Export dari "laptop lokal".
        $file = tempnam(sys_get_temp_dir(), 'absen').'.json';
        $this->artisan('absensi:export', ['kegiatan' => $k->id, '--file' => $file])->assertSuccessful();
        $payload = json_decode(file_get_contents($file), true);
        $this->assertEquals('bem-saintek-absensi', $payload['app']);
        $this->assertCount(2, $payload['kehadiran']);
        // Payload tidak mengandung ID database sama sekali.
        $this->assertArrayNotHasKey('id', $payload['kehadiran'][0]);

        // Simulasi database online kosong → import dengan --buat-kegiatan.
        Kehadiran::query()->delete();
        KegiatanPeserta::query()->delete();
        $k->delete();

        $this->artisan('absensi:import', ['file' => $file, '--buat-kegiatan' => true])->assertSuccessful();

        $baru = Kegiatan::where('nama', 'Makrab Offline')->first();
        $this->assertNotNull($baru);
        $this->assertEquals(2, $baru->kehadirans()->count());
        // Status pulih berbasis NIM walau ID-nya beda.
        $statusA = Kehadiran::whereHas('peserta', fn ($q) => $q->where('kegiatan_id', $baru->id)->where('user_id', $a->id))->first()->status;
        $this->assertEquals('hadir', $statusA);

        // Import kedua dari operator lain: Budi dikoreksi jadi izin (file menang).
        $payload['kehadiran'][1]['status'] = 'izin';
        file_put_contents($file, json_encode($payload));
        $this->artisan('absensi:import', ['file' => $file, '--kegiatan' => $baru->id])->assertSuccessful();
        $statusB = Kehadiran::whereHas('peserta', fn ($q) => $q->where('kegiatan_id', $baru->id)->where('user_id', $b->id))->first()->status;
        $this->assertEquals('izin', $statusB);

        unlink($file);
    }

    public function test_profil_tampilkan_riwayat_kegiatan_dan_iuran(): void
    {
        $op = $this->operator();
        $mhs = User::factory()->create(['name' => 'Citra Rajin']);

        $k = Kegiatan::create([
            'nama' => 'Seminar', 'tanggal' => '2026-10-01',
            'poin_alpa' => 10, 'status' => 'selesai', 'created_by' => $op->id,
        ]);
        $p = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $mhs->id]);
        Kehadiran::create(['kegiatan_peserta_id' => $p->id, 'status' => 'alpa']);

        $iuran = \App\Models\Iuran::create([
            'nama' => 'Kas Okt', 'nominal' => 50000, 'status' => 'aktif', 'created_by' => $op->id,
        ]);
        \App\Models\IuranPeserta::create(['iuran_id' => $iuran->id, 'user_id' => $mhs->id, 'nominal_wajib' => 50000]);

        // Profil sendiri.
        $res = $this->actingAs($mhs)->get('/profil');
        $res->assertOk();
        $page = $res->viewData('page')['props'];
        $this->assertEquals(10, $page['totalPoin']);
        $this->assertEquals(50000, $page['totalSisaIuran']);
        $this->assertEquals('alpa', $page['riwayatKegiatan'][0]['status_hadir']);
        $this->assertEquals('belum', $page['tagihanIuran'][0]['status_bayar']);

        // Admin melihat profil user.
        $admin = User::factory()->create(['role' => 'pengurus']);
        $res = $this->actingAs($admin)->get("/users/{$mhs->id}");
        $res->assertOk();
        $this->assertEquals(10, $res->viewData('page')['props']['totalPoin']);
    }

    public function test_filter_semester_di_picker_dan_rekap(): void
    {
        $op = $this->operator();
        $smtA = User::hitungSemester(2024);
        $smtB = User::hitungSemester(2023);
        $this->assertNotEquals($smtA, $smtB);

        $a = User::factory()->create(['name' => 'Smt A', 'angkatan' => 2024]);
        $b = User::factory()->create(['name' => 'Smt B', 'angkatan' => 2023]);

        // Picker peserta: hanya semester A.
        $res = $this->actingAs($op)->getJson("/kegiatan/cari-user?semester={$smtA}&limit=50");
        $res->assertOk();
        $ids = collect($res->json())->pluck('id')->all();
        $this->assertContains($a->id, $ids);
        $this->assertNotContains($b->id, $ids);
        // Semester ikut terkirim di payload.
        $rowA = collect($res->json())->firstWhere('id', $a->id);
        $this->assertEquals($smtA, $rowA['semester']);

        // Rekap: filter semester yang sama.
        $k = Kegiatan::create([
            'nama' => 'Kegiatan Smt', 'tanggal' => '2026-10-09',
            'poin_alpa' => 10, 'status' => 'selesai', 'created_by' => $op->id,
        ]);
        $pA = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $a->id]);
        $pB = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $b->id]);
        Kehadiran::create(['kegiatan_peserta_id' => $pA->id, 'status' => 'alpa']);
        Kehadiran::create(['kegiatan_peserta_id' => $pB->id, 'status' => 'hadir']);

        $res = $this->actingAs($op)->get("/absensi/rekap?semester={$smtA}");
        $res->assertOk();
        $rows = collect($res->viewData('page')['props']['rows']);
        $this->assertTrue($rows->contains(fn ($r) => $r['user_id'] === $a->id));
        $this->assertFalse($rows->contains(fn ($r) => $r['user_id'] === $b->id));

        // Tanpa filter semester: keduanya muncul.
        $res = $this->actingAs($op)->get('/absensi/rekap');
        $rows = collect($res->viewData('page')['props']['rows']);
        $this->assertTrue($rows->contains(fn ($r) => $r['user_id'] === $a->id));
        $this->assertTrue($rows->contains(fn ($r) => $r['user_id'] === $b->id));
    }

    public function test_rekap_detail_per_orang(): void
    {
        $op = $this->operator();
        $mhs = User::factory()->create(['name' => 'Detail Sari']);

        $k1 = Kegiatan::create([
            'nama' => 'Rapat', 'tanggal' => '2026-10-01',
            'poin_alpa' => 10, 'status' => 'selesai', 'created_by' => $op->id,
        ]);
        $k2 = Kegiatan::create([
            'nama' => 'Makrab', 'tanggal' => '2026-10-05',
            'poin_alpa' => 20, 'status' => 'selesai', 'created_by' => $op->id,
        ]);
        $p1 = KegiatanPeserta::create(['kegiatan_id' => $k1->id, 'user_id' => $mhs->id]);
        $p2 = KegiatanPeserta::create(['kegiatan_id' => $k2->id, 'user_id' => $mhs->id]);
        Kehadiran::create(['kegiatan_peserta_id' => $p1->id, 'status' => 'hadir']);
        Kehadiran::create(['kegiatan_peserta_id' => $p2->id, 'status' => 'alpa']);

        // Mahasiswa biasa ditolak.
        $this->actingAs(User::factory()->create())->get("/absensi/rekap/{$mhs->id}")->assertForbidden();

        $res = $this->actingAs($op)->get("/absensi/rekap/{$mhs->id}");
        $res->assertOk();
        $page = $res->viewData('page')['props'];
        $this->assertEquals('Detail Sari', $page['detailUser']['nama']);
        $this->assertEquals(1, $page['ringkasan']['hadir']);
        $this->assertEquals(1, $page['ringkasan']['alpa']);
        $this->assertEquals(20, $page['ringkasan']['poin']);
        $this->assertCount(2, $page['rows']);

        // Filter tanggal: hanya Makrab.
        $res = $this->actingAs($op)->get("/absensi/rekap/{$mhs->id}?dari=2026-10-03&sampai=2026-10-09");
        $page = $res->viewData('page')['props'];
        $this->assertCount(1, $page['rows']);
        $this->assertEquals('Makrab', $page['rows'][0]['nama']);
    }

    public function test_rekap_export_csv_memakai_filter_aktif(): void
    {
        $op = $this->operator();
        $ikut = User::factory()->create(['name' => 'Ekspor Ikut', 'angkatan' => 2024]);
        $lain = User::factory()->create(['name' => 'Ekspor Lain', 'angkatan' => 2023]);

        $k = Kegiatan::create([
            'nama' => 'Baksti', 'tanggal' => '2026-10-09',
            'poin_alpa' => 10, 'status' => 'selesai', 'created_by' => $op->id,
        ]);
        $pI = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $ikut->id]);
        $pL = KegiatanPeserta::create(['kegiatan_id' => $k->id, 'user_id' => $lain->id]);
        Kehadiran::create(['kegiatan_peserta_id' => $pI->id, 'status' => 'alpa']);
        Kehadiran::create(['kegiatan_peserta_id' => $pL->id, 'status' => 'hadir']);

        $res = $this->actingAs($op)->get('/absensi/rekap/export');
        $res->assertOk();
        $this->assertStringContainsString('text/csv', $res->headers->get('Content-Type'));
        $isi = $res->streamedContent();
        $this->assertStringContainsString('Semester', $isi);
        $this->assertStringContainsString('Ekspor Ikut', $isi);
        $this->assertStringContainsString('Ekspor Lain', $isi);

        // Dengan filter semester: hanya yang cocok.
        $smt = User::hitungSemester(2024);
        $res = $this->actingAs($op)->get("/absensi/rekap/export?semester={$smt}");
        $isi = $res->streamedContent();
        $this->assertStringContainsString('Ekspor Ikut', $isi);
        $this->assertStringNotContainsString('Ekspor Lain', $isi);

        $this->actingAs(User::factory()->create())->get('/absensi/rekap/export')->assertForbidden();
    }
}
