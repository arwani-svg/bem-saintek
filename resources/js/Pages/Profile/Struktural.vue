<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

// === EDIT DI SINI: ganti nama / jabatan / foto ===
const bph = [
    { jabatan: 'Ketua Umum', nama: '[Nama Ketua]', nim: 'NIM • Informatika', inisial: 'KU', warna: 'from-blue-600 to-indigo-600', tugas: 'Penanggung jawab umum & representasi eksternal.' },
    { jabatan: 'Wakil Ketua', nama: '[Nama Wakil]', nim: 'NIM • Sistem Informasi', inisial: 'WK', warna: 'from-indigo-500 to-purple-600', tugas: 'Koordinasi internal & supervisi departemen.' },
    { jabatan: 'Sekretaris Jenderal', nama: '[Nama Sekjen]', nim: 'NIM • Matematika', inisial: 'SJ', warna: 'from-emerald-500 to-teal-600', tugas: 'Administrasi, surat-menyurat & arsip.' },
    { jabatan: 'Bendahara Umum', nama: '[Nama Bendum]', nim: 'NIM • Fisika', inisial: 'BU', warna: 'from-amber-500 to-orange-600', tugas: 'Keuangan, budgeting & laporan kas.' },
]

// === EDIT DI SINI: daftar departemen (5 departemen) ===
const departemen = [
    { nama: 'Kaderisasi dan Organisasi', singkat: 'Kaderisasi', icon: '🌱', ketua: '[Nama Kadep]', anggota: 12, proker: 'Pengkaderan, Upgrading', warna: 'bg-emerald-300/10 text-emerald-300' },
    { nama: 'Pengembangan Nalar dan Bakat', singkat: 'Nalar Bakat', icon: '💡', ketua: '[Nama Kadep]', anggota: 14, proker: 'Riset, Kompetisi', warna: 'bg-violet-300/10 text-violet-300' },
    { nama: 'Informasi dan Komunikasi', singkat: 'Infokom', icon: '📣', ketua: '[Nama Kadep]', anggota: 9, proker: 'Medsos, Publikasi', warna: 'bg-cyan-300/10 text-cyan-300' },
    { nama: 'Ekonomi', singkat: 'Ekonomi', icon: '💰', ketua: '[Nama Kadep]', anggota: 10, proker: 'Kewirausahaan', warna: 'bg-amber-300/10 text-amber-300' },
    { nama: 'Relasi Mahasiswa', singkat: 'Relasi', icon: '🤝', ketua: '[Nama Kadep]', anggota: 11, proker: 'Aspirasi, Jejaring', warna: 'bg-pink-300/10 text-pink-300' },
]
</script>

<template>
    <Head title="Struktural - Profil" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <PageHero
                label="PROFIL // STRUKTURAL"
                description="4 Badan Pengurus Harian + 6 Departemen + 69 pengurus aktif."
                :crumbs="[{ label: 'BERANDA', href: '/' }, { label: 'PROFIL' }, { label: 'STRUKTURAL' }]"
            >
                <template #title>
                    Struktur Organisasi <span class="gradient-text">BEM SAINTEK</span>
                </template>
                <template #extra>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <span class="rounded-xl border border-white/10 bg-white/[0.025] px-4 py-2 font-mono text-[11px] text-white/50">👥 73 PENGURUS</span>
                        <span class="rounded-xl bg-cyan-300 px-4 py-2 font-mono text-[11px] font-bold text-black">● AKTIF</span>
                    </div>
                </template>
            </PageHero>

            <!-- Bagan mini -->
            <section class="relative py-10">
                <div class="mx-auto max-w-5xl px-5 lg:px-8">
                    <div class="glass rounded-2xl p-6 md:p-8">
                        <h2 class="mb-6 text-center font-mono text-[10px] tracking-[.3em] text-white/30">BAGAN ORGANISASI</h2>
                        <div class="flex flex-col items-center">
                            <div class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg">KETUA UMUM</div>
                            <div class="h-6 w-px bg-white/15"></div>
                            <div class="rounded-xl border border-white/10 bg-white/[0.05] px-6 py-2.5 text-sm font-semibold text-white">WAKIL KETUA</div>
                            <div class="h-6 w-px bg-white/15"></div>
                            <div class="flex gap-4">
                                <div class="rounded-xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-2 text-sm font-semibold text-emerald-300">SEKJEN</div>
                                <div class="rounded-xl border border-amber-300/20 bg-amber-300/10 px-5 py-2 text-sm font-semibold text-amber-300">BENDUM</div>
                            </div>
                            <div class="mt-6 grid w-full grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                                <div v-for="d in departemen" :key="d.nama" class="rounded-xl border border-white/10 bg-white/[0.025] px-3 py-2.5 text-center">
                                    <div class="text-lg">{{ d.icon }}</div>
                                    <div class="text-xs font-bold text-white">{{ d.singkat }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- BPH -->
            <section class="relative py-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-6 font-mono text-[10px] tracking-[.3em] text-cyan-300">01 / BADAN PENGURUS HARIAN</div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <article v-for="p in bph" :key="p.jabatan" class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.025] transition-all hover:-translate-y-1 hover:border-cyan-300/20">
                            <div :class="`bg-gradient-to-br ${p.warna} relative h-24`">
                                <span class="absolute bottom-3 left-5 text-xs font-semibold uppercase tracking-wide text-white/90">{{ p.jabatan }}</span>
                            </div>
                            <div class="relative p-5 pt-0">
                                <div :class="`bg-gradient-to-br ${p.warna} -mt-8 mb-3 flex h-16 w-16 items-center justify-center rounded-2xl text-xl font-black text-white shadow-lg ring-4 ring-[#0a0f1a]`">{{ p.inisial }}</div>
                                <h3 class="font-display font-bold text-white">{{ p.nama }}</h3>
                                <p class="mb-3 text-xs text-white/30">{{ p.nim }}</p>
                                <p class="text-sm text-white/40">{{ p.tugas }}</p>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- Departemen -->
            <section class="relative pb-20 pt-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-6 font-mono text-[10px] tracking-[.3em] text-cyan-300">02 / KEPALA DEPARTEMEN</div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <article v-for="d in departemen" :key="d.nama" class="rounded-2xl border border-white/10 bg-white/[0.025] p-6 transition-all hover:border-cyan-300/20">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-white/10 bg-white/[0.05] text-2xl">{{ d.icon }}</div>
                                <span :class="`rounded-full px-3 py-1 text-xs font-bold ${d.warna}`">{{ d.anggota }} Anggota</span>
                            </div>
                            <h3 class="font-display mb-3 font-bold text-white">Departemen {{ d.nama }}</h3>
                            <p class="text-sm text-white/40">Ketua: <span class="font-medium text-white">{{ d.ketua }}</span></p>
                            <p class="text-sm text-white/30">Fokus: {{ d.proker }}</p>
                        </article>
                    </div>

                    <div class="mt-10 flex flex-col justify-between gap-6 rounded-2xl border border-cyan-300/20 bg-gradient-to-r from-cyan-300/10 to-violet-500/10 p-8 md:flex-row md:items-center md:p-10">
                        <div>
                            <h3 class="font-display text-xl font-bold text-white">Tertarik jadi pengurus selanjutnya?</h3>
                            <p class="mt-1 text-sm text-white/40">Open Recruitment dibuka tiap awal periode. Pantau Instagram @bemsaintek.</p>
                        </div>
                        <Link href="/kontak" class="shrink-0 rounded-full bg-white px-6 py-3 text-sm font-bold text-black transition hover:bg-cyan-300">Daftar OPREC</Link>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
