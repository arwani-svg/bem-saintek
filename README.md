# BEM Saintek — Sistem Informasi Organisasi Kampus

> Web profil publik + dashboard admin untuk BEM Fakultas Sains & Teknologi: kelola departemen, proker, informasi, pendaftaran pengurus, keuangan, iuran, absensi kegiatan, user & role.

![Laravel](https://img.shields.io/badge/Laravel-13-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php)
![Inertia](https://img.shields.io/badge/Inertia.js-v3-purple)
![Vue](https://img.shields.io/badge/Vue-3-green?logo=vue.js)
![Tailwind](https://img.shields.io/badge/Tailwind-4-38bdf8?logo=tailwindcss)
![Status](https://img.shields.io/badge/status-in%20progress-yellow)

**Status:** 🚧 In Progress — fitur inti jalan, pengembangan bertahap. Lihat [Roadmap](#roadmap).

## Kenapa repo ini ada?

BEM butuh satu pintu: pengunjung bisa lihat profil, HIMA, lab, proker & info lomba — pengurus bisa kerja admin (rekrutmen, kas, absensi) tanpa spreadsheet terpisah. Project ini saya bangun sebagai portfolio fullstack.

## Fitur

### 🌐 Publik (tanpa login)
- Landing modern: hero, statistik, departemen, proker, testimoni, CTA Join Us
- Profil: struktural, visi-misi, sejarah, laboratorium
- HIMA per prodi: Himatif, Himasi, Himakom, Himars, Himabp, Himathp
- Informasi: berita, infografis, penghargaan, lomba (`/information/{slug}`)
- Program kerja & detail departemen
- Halaman Join With Us + kontak

### 🔐 Dashboard (login)
- Dashboard analitik: chart kehadiran, rekap prodi, riwayat user
- **Pendaftaran pengurus:** form mahasiswa, verifikasi pengurus, gelombang/periode, status seleksi
- **Keuangan bendahara:** uang masuk/keluar, saldo, halaman rekap
- **Iuran/kas:** tagihan per anggota, bisa cicil, tiap bayar otomatis masuk kas
- **Kegiatan & absensi:** absen cepat pakai keyboard, tandai alpa massal, rekap skor pelanggaran, export/import
- **Master data:** departemen, program kerja, informasi
- **RBAC (Spatie):** user, role, permission (`kelola-pengguna`, `kelola-keuangan`, `kelola-absensi`, dll)
- **Activity Log:** append-only, siapa ngapain kapan (tanpa tombol edit/hapus)
- Profil saya: edit identitas sendiri

Role bawaan: `admin` / `pengurus` / `mahasiswa` (lihat `database/seeders/RolesAndPermissionsSeeder.php`).

## Tech Stack

- **Backend:** Laravel 13, PHP 8.3, Spatie Permission, Eloquent ORM
- **Frontend:** Inertia.js v3 + Vue 3 + Vite 6 + Tailwind CSS 4
- **DB:** MySQL (dev bisa SQLite) — migrasi di `database/migrations/`
- **Test:** PHPUnit — `tests/Feature/` (Absensi, Keuangan, Departemen, Role, ActivityLog)

## Cara Jalanin Lokal

```bash
# 1. Clone
git clone https://github.com/arwani-svg/bem-saintek.git
cd bem-saintek

# 2. Setup sekali jalan (install, .env, key, migrate, build)
composer setup

# 3. Jalanin dev (server + vite)
composer dev
# atau manual:
# php artisan serve
# npm run dev

# 4. (opsional) Seeder awal
php artisan migrate --seed

# 5. Test
php artisan test
```

Butuh `.env` sendiri: copy dari `.env.example`, isi `DB_*`. File `.env` tidak di-commit.

## Struktur Singkat

```
app/Http/Controllers/  -> Auth, Dashboard, Departemen, Proker, Informasi,
                           Keuangan, Iuran, Kegiatan(absensi), Pendaftaran, RolePermission
resources/js/Pages/    -> Publik (Home, Profile, Join) + Admin/* + Auth + Dashboard
resources/js/components/ -> sections (Hero, Navbar, Footer...), dashboard (Sidebar, Charts...), ui
routes/web.php         -> rute publik + grup auth/permission/role
database/              -> migrations + seeders (departemen, role-permission)
tests/                 -> feature test per modul
desain/                -> mockup html awal (dashboard, profile, popup)
```

## Roadmap

- [x] Auth + RBAC + dashboard base
- [x] CRUD departemen, proker, informasi
- [x] Pendaftaran + gelombang seleksi
- [x] Keuangan + iuran cicilan
- [x] Kegiatan + absensi cepat + rekap
- [ ] Upload bukti bayar / QR absensi
- [ ] Notifikasi WA/email status seleksi
- [ ] Screenshot demo + deploy (VPS/Vercel backend)
- [ ] Perapihan test coverage >80%

## Screenshot

> Taruh di sini setelah UI stabil. Mockup awal ada di folder `desain/` (`dashboard.html`, `profile.html`).
> Saran: `docs/screenshot-home.png`, `docs/screenshot-dashboard.png`, `docs/screenshot-absensi.png`

## Dibangun Oleh

**Arwani** — Fullstack (Laravel + Vue) — [@arwani-svg](https://github.com/arwani-svg)
Portfolio project. Kritik/saran via Issues sangat welcome.
