<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

// === EDIT DI SINI: daftar laboratorium ===
const labs = [
    {
        nama: 'Lab Komputasi & AI',
        kode: 'LAB-01',
        gedung: 'Gedung B Lt. 2',
        icon: '💻',
        warna: 'from-blue-500 to-indigo-600',
        deskripsi: 'Praktikum pemrograman, basis data, jaringan, dan riset kecerdasan artifisial.',
        fasilitas: ['40 Unit PC', 'GPU Server', 'Proyektor', 'AC + WiFi'],
        kepala: '[Nama Kepala Lab]',
        jam: 'Senin–Jumat, 08.00–16.00',
        kapasitas: '40 orang',
    },
    {
        nama: 'Lab Fisika Dasar',
        kode: 'LAB-02',
        gedung: 'Gedung A Lt. 1',
        icon: '⚛️',
        warna: 'from-amber-500 to-orange-600',
        deskripsi: 'Eksperimen mekanika, listrik-magnet, optik, dan fisika modern.',
        fasilitas: ['Kit Mekanika', 'Osiloskop', 'Spektrometer', 'Teleskop'],
        kepala: '[Nama Kepala Lab]',
        jam: 'Senin–Jumat, 08.00–16.00',
        kapasitas: '30 orang',
    },
    {
        nama: 'Lab Kimia Dasar & Analitik',
        kode: 'LAB-03',
        gedung: 'Gedung A Lt. 2',
        icon: '🧪',
        warna: 'from-emerald-500 to-teal-600',
        deskripsi: 'Praktikum kimia dasar, analitik, organik, dan instrumentasi.',
        fasilitas: ['Lemari Asam', 'Spektrofotometer', 'Neraca Analitik', 'Reagen Lengkap'],
        kepala: '[Nama Kepala Lab]',
        jam: 'Senin–Jumat, 08.00–15.00',
        kapasitas: '25 orang',
    },
    {
        nama: 'Lab Biologi & Mikrobiologi',
        kode: 'LAB-04',
        gedung: 'Gedung C Lt. 1',
        icon: '🔬',
        warna: 'from-green-500 to-lime-600',
        deskripsi: 'Praktikum botani, zoologi, genetika, dan kultur mikroba.',
        fasilitas: ['Mikroskop Stereo', 'Inkubator', 'Autoklaf', 'Herbarium'],
        kepala: '[Nama Kepala Lab]',
        jam: 'Senin–Jumat, 08.00–16.00',
        kapasitas: '25 orang',
    },
    {
        nama: 'Lab Elektronika & Instrumentasi',
        kode: 'LAB-05',
        gedung: 'Gedung B Lt. 1',
        icon: '🔌',
        warna: 'from-purple-500 to-violet-600',
        deskripsi: 'Rangkaian digital, mikrokontroler, IoT, dan robotika.',
        fasilitas: ['Arduino Kit', '3D Printer', 'Solder Station', 'Power Supply'],
        kepala: '[Nama Kepala Lab]',
        jam: 'Senin–Sabtu, 08.00–17.00',
        kapasitas: '30 orang',
    },
    {
        nama: 'Lab Matematika & Data',
        kode: 'LAB-06',
        gedung: 'Gedung B Lt. 3',
        icon: '📐',
        warna: 'from-pink-500 to-rose-600',
        deskripsi: 'Komputasi statistik, pemodelan matematika, dan riset sains data.',
        fasilitas: ['Workstation', 'Software R / MATLAB', 'Smartboard', 'Ruang Diskusi'],
        kepala: '[Nama Kepala Lab]',
        jam: 'Senin–Jumat, 08.00–16.00',
        kapasitas: '35 orang',
    },
]

const alur = [
    { judul: 'Ajukan Surat', deskripsi: 'Isi form peminjaman + surat dari HIMA / dosen pembimbing.', icon: '📝' },
    { judul: 'Verifikasi', deskripsi: 'Laboran memeriksa jadwal & ketersediaan alat (maks. 1×24 jam).', icon: '🔍' },
    { judul: 'Briefing K3', deskripsi: 'Wajib mengikuti briefing keselamatan lab sebelum praktikum.', icon: '🦺' },
    { judul: 'Pelaksanaan', deskripsi: 'Gunakan lab sesuai SOP, didampingi asisten lab.', icon: '⚗️' },
]

const tataTertib = [
    'Wajib memakai jas lab, sepatu tertutup, dan APD sesuai jenis praktikum.',
    'Dilarang makan, minum, dan merokok di area laboratorium.',
    'Segala kerusakan / kehilangan alat menjadi tanggung jawab peminjam.',
    'Bersihkan meja kerja dan kembalikan alat setelah selesai digunakan.',
    'Limbah B3 wajib dibuang pada wadah khusus, tidak ke wastafel umum.',
]
</script>

<template>
    <Head title="Laboratorium - Profil" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <PageHero
                label="PROFIL // FASILITAS RISET"
                description="Mendukung praktikum, penelitian dosen-mahasiswa, dan PKM. Semua lab terbuka untuk mahasiswa Saintek dengan izin peminjaman resmi."
                :crumbs="[{ label: 'BERANDA', href: '/' }, { label: 'PROFIL' }, { label: 'LABORATORIUM' }]"
            >
                <template #title>
                    <span class="gradient-text">6 Laboratorium</span> Modern Saintek
                </template>
                <template #extra>
                    <div class="mt-8 grid max-w-lg grid-cols-3 gap-4">
                        <div class="rounded-2xl border border-white/10 bg-white/[0.025] p-5 text-center">
                            <div class="font-display text-2xl font-bold text-white">6</div>
                            <div class="font-mono text-[9px] tracking-widest text-white/30">UNIT LAB</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[0.025] p-5 text-center">
                            <div class="font-display text-2xl font-bold text-white">185</div>
                            <div class="font-mono text-[9px] tracking-widest text-white/30">KAPASITAS</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[0.025] p-5 text-center">
                            <div class="font-display text-2xl font-bold text-white">12+</div>
                            <div class="font-mono text-[9px] tracking-widest text-white/30">ASISTEN</div>
                        </div>
                    </div>
                </template>
            </PageHero>

            <!-- Daftar Lab -->
            <section class="relative py-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="lab in labs"
                            :key="lab.kode"
                            class="flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.025] transition-all duration-300 hover:-translate-y-1 hover:border-cyan-300/20"
                        >
                            <div :class="`bg-gradient-to-br ${lab.warna} p-6 text-white`">
                                <div class="flex items-start justify-between">
                                    <div class="text-4xl">{{ lab.icon }}</div>
                                    <span class="rounded-full bg-white/20 px-3 py-1 font-mono text-[10px] font-bold backdrop-blur-sm">{{ lab.kode }}</span>
                                </div>
                                <h3 class="font-display mt-4 text-xl font-bold">{{ lab.nama }}</h3>
                                <p class="text-sm text-white/80">{{ lab.gedung }} • Kap. {{ lab.kapasitas }}</p>
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <p class="mb-4 text-sm leading-relaxed text-white/40">{{ lab.deskripsi }}</p>
                                <div class="mb-4 flex flex-wrap gap-2">
                                    <span v-for="f in lab.fasilitas" :key="f" class="rounded-lg border border-white/10 bg-white/[0.05] px-2.5 py-1 text-xs font-medium text-white/60">{{ f }}</span>
                                </div>
                                <div class="mt-auto space-y-1.5 border-t border-white/5 pt-4 font-mono text-[11px] text-white/30">
                                    <p>👤 {{ lab.kepala }}</p>
                                    <p>🕗 {{ lab.jam }}</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- Alur peminjaman -->
            <section class="relative border-y border-white/5 bg-white/[0.015] py-16">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-12 text-center">
                        <div class="mb-4 font-mono text-[10px] tracking-[.3em] text-cyan-300">01 / PEMINJAMAN LAB</div>
                        <h2 class="font-display text-3xl font-bold text-white">Alur Pinjam Laboratorium</h2>
                        <p class="mx-auto mt-2 max-w-xl text-white/40">Proses cepat, maksimal H+1 sudah dapat kepastian jadwal.</p>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div v-for="(s, i) in alur" :key="s.judul" class="rounded-2xl border border-white/10 bg-white/[0.025] p-6">
                            <div class="mb-3 text-3xl">{{ s.icon }}</div>
                            <div class="mb-1 font-mono text-[10px] tracking-widest text-cyan-300">LANGKAH {{ i + 1 }}</div>
                            <h3 class="font-display font-semibold text-white">{{ s.judul }}</h3>
                            <p class="mt-2 text-sm text-white/40">{{ s.deskripsi }}</p>
                        </div>
                    </div>
                    <div class="mt-8 grid gap-5 md:grid-cols-2">
                        <div class="rounded-2xl border border-amber-300/20 bg-amber-300/5 p-6 md:p-8">
                            <h3 class="mb-4 flex items-center gap-2 font-display font-bold text-amber-300">⚠️ Tata Tertib Lab</h3>
                            <ul class="space-y-2.5">
                                <li v-for="t in tataTertib" :key="t" class="flex gap-2 text-sm text-amber-200/70">
                                    <span class="shrink-0">•</span><span>{{ t }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="flex flex-col justify-center rounded-2xl border border-cyan-300/20 bg-gradient-to-br from-cyan-300/10 to-violet-500/10 p-6 md:p-8">
                            <h3 class="font-display text-xl font-bold text-white">Butuh surat peminjaman?</h3>
                            <p class="mb-6 mt-1 text-sm text-white/40">Unduh template surat & form checklist alat, atau hubungi laboran via WhatsApp sekretariat.</p>
                            <div class="flex flex-wrap gap-3">
                                <a href="#" class="rounded-full bg-white px-5 py-2.5 text-sm font-bold text-black transition hover:bg-cyan-300">Unduh Form</a>
                                <Link href="/kontak" class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-white/70 transition hover:border-cyan-300/30 hover:text-white">Hubungi Laboran</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
