<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\GelombangPendaftaranController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\PendaftaranPengurusController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/program-kerja', [HomeController::class, 'program'])->name('program');

// Halaman publik Informasi (navbar mengarah ke /information)
Route::get('/information', [HomeController::class, 'informasi'])->name('information.index');
Route::get('/information/{slug}', [HomeController::class, 'informasiShow'])->name('information.show');


// ########################################################################
// NAVIGATION ROUTES FRONT OF USER
// ########################################################################


// NAVIGATION
Route::get('/about', fn () => inertia('About'));

// Profile Routes
Route::get('/profile/struktural', fn () => inertia('Profile/Struktural'));
Route::get('/profile/visi-misi', fn () => inertia('Profile/VisiMisi'));
Route::get('/profile/sejarah', fn () => inertia('Profile/Sejarah'));
Route::get('/profile/hima', fn () => inertia('Profile/Hima'))->name('hima.index');
Route::get('/profile/hima/{slug}', fn (string $slug) => inertia('Profile/HimaShow', ['slug' => $slug]))
    ->whereIn('slug', ['himatif', 'himasi', 'himakom', 'himars', 'himabp', 'himathp'])
    ->name('hima.show');
Route::get('/profile/laboratorium', fn () => inertia('Profile/Laboratorium'));

// Contact Route
Route::get('/kontak', fn () => inertia('Kontak'));

// Halaman publik Join With Us (tanpa sidebar/navbar).
// GET boleh dibuka semua orang; submit tetap wajib login mahasiswa.
Route::get('/join', [PendaftaranPengurusController::class, 'join'])->name('join');
Route::post('/join', [PendaftaranPengurusController::class, 'joinStore'])
    ->middleware(['auth', 'role:mahasiswa'])->name('join.store');
Route::get('/join/sukses/{pendaftaran}', [PendaftaranPengurusController::class, 'joinSukses'])
    ->middleware(['auth', 'role:mahasiswa'])->name('join.sukses');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil Saya — identitas diri sendiri (semua role yang login).
    Route::get('/profil', [ProfileController::class, 'show'])->name('profil.show');
    Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::match(['put', 'patch'], '/profil', [ProfileController::class, 'update'])->name('profil.update');

    // User, Role & Permission Management (Spatie)
    Route::middleware('permission:kelola-pengguna')->group(function () {
        Route::resource('users', UserController::class);

        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RolePermissionController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RolePermissionController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RolePermissionController::class, 'edit'])->name('roles.edit');
        Route::match(['put', 'patch'], '/roles/{role}', [RolePermissionController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroy'])->name('roles.destroy');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::match(['put', 'patch'], '/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // Activity Log — hanya admin ber-permission (append-only, tanpa tombol ubah/hapus).
    Route::middleware('permission:lihat-activity-log')->group(function () {
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });

    // Departemen & Program Kerja Management
    // NOTE: parameter resource 'departemen' dipaksa {departemen} karena
    // singular bawaan Laravel mengubahnya jadi {departeman} (aturan men->man),
    // yang merusak implicit binding Departemen $departemen di controller.
    Route::middleware('permission:kelola-departemen')->group(function () {
        Route::resource('departemen', DepartemenController::class)->except(['show'])->parameters(['departemen' => 'departemen']);
    });
    Route::middleware('permission:kelola-proker')->group(function () {
        Route::resource('proker', ProgramKerjaController::class)->except(['show']);
    });

    // Informasi Management (satu CRUD fleksibel: berita, infografis, penghargaan, lomba)
    Route::middleware('permission:kelola-informasi')->group(function () {
        Route::resource('informasi', InformasiController::class)->except(['show']);
    });

    // Bendahara — manajemen keuangan (uang masuk/keluar, saldo, rekap).
    Route::middleware('permission:kelola-keuangan')->group(function () {
        Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
        Route::get('/keuangan/create', [KeuanganController::class, 'create'])->name('keuangan.create');
        Route::post('/keuangan', [KeuanganController::class, 'store'])->name('keuangan.store');
        Route::get('/keuangan/rekap', [KeuanganController::class, 'rekap'])->name('keuangan.rekap');
        Route::get('/keuangan/{keuangan}/edit', [KeuanganController::class, 'edit'])->name('keuangan.edit');
        Route::match(['put', 'patch'], '/keuangan/{keuangan}', [KeuanganController::class, 'update'])->name('keuangan.update');
        Route::delete('/keuangan/{keuangan}', [KeuanganController::class, 'destroy'])->name('keuangan.destroy');

        // Bendahara — Manajemen Iuran (tagihan kas per anggota, boleh cicil,
        // tiap pembayaran otomatis masuk kas sebagai pemasukan).
        Route::get('/iuran/cari-user', [IuranController::class, 'cariUser'])->name('iuran.cari-user');
        Route::get('/iuran', [IuranController::class, 'index'])->name('iuran.index');
        Route::get('/iuran/create', [IuranController::class, 'create'])->name('iuran.create');
        Route::post('/iuran', [IuranController::class, 'store'])->name('iuran.store');
        Route::get('/iuran/{iuran}', [IuranController::class, 'show'])->name('iuran.show');
        Route::get('/iuran/{iuran}/edit', [IuranController::class, 'edit'])->name('iuran.edit');
        Route::match(['put', 'patch'], '/iuran/{iuran}', [IuranController::class, 'update'])->name('iuran.update');
        Route::delete('/iuran/{iuran}', [IuranController::class, 'destroy'])->name('iuran.destroy');
        Route::post('/iuran/{iuran}/peserta', [IuranController::class, 'tambahPeserta'])->name('iuran.peserta.store');
        Route::match(['put', 'patch'], '/iuran/{iuran}/peserta/{peserta}', [IuranController::class, 'updatePeserta'])->name('iuran.peserta.update');
        Route::delete('/iuran/{iuran}/peserta/{peserta}', [IuranController::class, 'hapusPeserta'])->name('iuran.peserta.destroy');
        Route::post('/iuran/{iuran}/bayar', [IuranController::class, 'bayar'])->name('iuran.bayar');
        Route::delete('/iuran/{iuran}/bayar/{pembayaran}', [IuranController::class, 'hapusBayar'])->name('iuran.bayar.destroy');
    });

    // Absensi — kegiatan + absen cepat keyboard + rekap skor pelanggaran.
    Route::middleware('permission:kelola-absensi')->group(function () {
        Route::get('/kegiatan/cari-user', [KegiatanController::class, 'cariUser'])->name('kegiatan.cari-user');
        Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
        Route::get('/kegiatan/create', [KegiatanController::class, 'create'])->name('kegiatan.create');
        Route::post('/kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
        Route::get('/absensi/rekap', [KegiatanController::class, 'rekap'])->name('absensi.rekap');
        Route::get('/kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('kegiatan.show');
        Route::get('/kegiatan/{kegiatan}/edit', [KegiatanController::class, 'edit'])->name('kegiatan.edit');
        Route::match(['put', 'patch'], '/kegiatan/{kegiatan}', [KegiatanController::class, 'update'])->name('kegiatan.update');
        Route::delete('/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');
        Route::get('/kegiatan/{kegiatan}/absen', [KegiatanController::class, 'absen'])->name('kegiatan.absen');
        Route::get('/kegiatan/{kegiatan}/cari-peserta', [KegiatanController::class, 'cariPeserta'])->name('kegiatan.cari-peserta');
        Route::post('/kegiatan/{kegiatan}/tandai', [KegiatanController::class, 'tandai'])->name('kegiatan.tandai');
        Route::post('/kegiatan/{kegiatan}/tandai-sisa-alpa', [KegiatanController::class, 'tandaiSisaAlpa'])->name('kegiatan.tandai-sisa-alpa');
        Route::post('/kegiatan/{kegiatan}/peserta', [KegiatanController::class, 'tambahPeserta'])->name('kegiatan.peserta.store');
        Route::delete('/kegiatan/{kegiatan}/peserta/{peserta}', [KegiatanController::class, 'hapusPeserta'])->name('kegiatan.peserta.destroy');
    });

    // Pendaftaran Pengurus BEM — form HANYA untuk user login (bukan umum).
    // Mahasiswa: daftar + lihat milik sendiri.
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/pendaftaran', [PendaftaranPengurusController::class, 'mine'])->name('pendaftaran.mine');
        Route::get('/pendaftaran/create', [PendaftaranPengurusController::class, 'create'])->name('pendaftaran.create');
        Route::post('/pendaftaran', [PendaftaranPengurusController::class, 'store'])->name('pendaftaran.store');
    });

    // Pengurus: kelola semua pendaftar (pakai permission, bukan role eksak,
    // agar jabatan seperti sekretaris yang punya permission-nya ikut bisa).
    Route::middleware('permission:kelola-pendaftaran')->group(function () {
        Route::get('/admin/pendaftaran', [PendaftaranPengurusController::class, 'index'])->name('pendaftaran.admin.index');
        Route::patch('/pendaftaran/{pendaftaran}/status', [PendaftaranPengurusController::class, 'updateStatus'])->name('pendaftaran.status');
    });

    // Periode pendaftaran (gelombang).
    Route::middleware('permission:kelola-gelombang')->group(function () {
        Route::get('/admin/gelombang/create', [GelombangPendaftaranController::class, 'create'])->name('gelombang.create');
        Route::post('/admin/gelombang', [GelombangPendaftaranController::class, 'store'])->name('gelombang.store');
        Route::get('/admin/gelombang/{gelombang}/edit', [GelombangPendaftaranController::class, 'edit'])->name('gelombang.edit');
        Route::match(['put', 'patch'], '/admin/gelombang/{gelombang}', [GelombangPendaftaranController::class, 'update'])->name('gelombang.update');
        Route::delete('/admin/gelombang/{gelombang}', [GelombangPendaftaranController::class, 'destroy'])->name('gelombang.destroy');
        Route::patch('/admin/gelombang/{gelombang}/toggle', [GelombangPendaftaranController::class, 'toggle'])->name('gelombang.toggle');
    });

    // Show / Edit / Hapus: mahasiswa (milik sendiri) + pengurus (semua).
    Route::middleware('role:mahasiswa|pengurus')->group(function () {
        Route::get('/pendaftaran/{pendaftaran}', [PendaftaranPengurusController::class, 'show'])->name('pendaftaran.show');
        Route::get('/pendaftaran/{pendaftaran}/edit', [PendaftaranPengurusController::class, 'edit'])->name('pendaftaran.edit');
        Route::match(['put', 'patch'], '/pendaftaran/{pendaftaran}', [PendaftaranPengurusController::class, 'update'])->name('pendaftaran.update');
        Route::delete('/pendaftaran/{pendaftaran}', [PendaftaranPengurusController::class, 'destroy'])->name('pendaftaran.destroy');
    });
});

// Halaman publik tiap departemen (di bawah agar tidak menabrak /departemen/create)
Route::get('/departemen/{departemen}', [HomeController::class, 'departemen'])->whereNumber('departemen')->name('departemen.show');
