<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

// === EDIT DI SINI: timeline sejarah, tinggal ubah tahun / judul / cerita ===
const timeline = [
    {
        tahun: '2015',
        judul: 'Fakultas Saintek Berdiri',
        cerita: 'Fakultas Sains dan Teknologi resmi dibentuk dengan 3 program studi awal: Matematika, Fisika, dan Kimia. Forum mahasiswa tingkat fakultas mulai dirintis.',
        tag: 'Awal Mula',
        warnaDot: 'bg-amber-300',
    },
    {
        tahun: '2017',
        judul: 'Deklarasi BEM SAINTEK',
        cerita: 'Musyawarah besar pertama menyepakati pembentukan BEM SAINTEK sebagai lembaga eksekutif mahasiswa tingkat fakultas. Kepengurusan perdana dilantik.',
        tag: 'Deklarasi',
        warnaDot: 'bg-cyan-300',
    },
    {
        tahun: '2019',
        judul: 'Ekspansi 6 Prodi & 6 HIMA',
        cerita: 'Informatika, Sistem Informasi, dan Biologi bergabung. Enam HIMA jurusan resmi diakui dan menjadi mitra resmi BEM dalam pembinaan mahasiswa.',
        tag: 'Ekspansi',
        warnaDot: 'bg-indigo-300',
    },
    {
        tahun: '2021',
        judul: 'Saintek Award & Riset Terapan',
        cerita: 'Era digitalisasi: BEM meluncurkan Saintek Award, pusat riset mahasiswa, dan 6 laboratorium terpadu mulai beroperasi penuh untuk PKM & penelitian.',
        tag: 'Inovasi',
        warnaDot: 'bg-violet-300',
    },
    {
        tahun: '2023',
        judul: 'Rebranding & Kolaborasi Nasional',
        cerita: 'Identitas visual baru, website terpadu, dan jejaring nasional antar-BEM Saintek se-Indonesia. Delegasi lolos PIMNAS & Gemastik.',
        tag: 'Nasional',
        warnaDot: 'bg-emerald-300',
    },
    {
        tahun: '2024/2025',
        judul: 'Menuju Saintek Unggul',
        cerita: 'Fokus pada pengabdian desa digital, inkubasi startup mahasiswa, dan advokasi kesejahteraan mahasiswa. Periode kepengurusan saat ini.',
        tag: 'Kini',
        warnaDot: 'bg-cyan-300',
    },
]

const tokoh = [
    { nama: '[Nama Ketua Pertama]', peran: 'Ketua Umum 2017–2018', pesan: '"Mulailah dari yang kecil: satu sekretariat, satu papan mading, dan mimpi yang besar."' },
    { nama: '[Nama Dekan Pendiri]', peran: 'Dekan Periode Pendirian', pesan: '"BEM Saintek lahir dari semangat saintis: kritis, jujur, dan bermanfaat."' },
]
</script>

<template>
    <Head title="Sejarah - Profil" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <PageHero
                label="PROFIL // PERJALANAN KAMI"
                description="Dari forum kecil antar-kelas hingga menjadi motor penggerak 500+ mahasiswa Saintek."
                :crumbs="[{ label: 'BERANDA', href: '/' }, { label: 'PROFIL' }, { label: 'SEJARAH' }]"
            >
                <template #title>
                    Satu Dekade <span class="gradient-text">BEM SAINTEK</span>
                </template>
            </PageHero>

            <!-- Timeline -->
            <section class="relative py-10">
                <div class="mx-auto max-w-4xl px-5 lg:px-8">
                    <div class="relative">
                        <div class="absolute bottom-0 left-5 top-0 w-0.5 bg-gradient-to-b from-amber-300 via-cyan-300 to-violet-400 md:left-1/2 md:-translate-x-1/2"></div>
                        <div class="space-y-8">
                            <div
                                v-for="(t, i) in timeline"
                                :key="t.tahun"
                                :class="`relative flex gap-6 md:gap-0 ${i % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'}`"
                            >
                                <div class="absolute left-5 flex -translate-x-1/2 items-center justify-center md:left-1/2">
                                    <span :class="`flex h-10 w-10 items-center justify-center rounded-full ${t.warnaDot} text-sm text-black shadow-lg ring-4 ring-[#0a0f1a]`">✦</span>
                                </div>
                                <div class="ml-14 md:ml-0 md:w-1/2" :class="i % 2 === 0 ? 'md:pr-12' : 'md:pl-12'">
                                    <div class="rounded-2xl border border-white/10 bg-white/[0.025] p-6 transition-colors hover:border-cyan-300/20">
                                        <div class="mb-2 flex items-center gap-3">
                                            <span class="font-display gradient-text text-2xl font-black">{{ t.tahun }}</span>
                                            <span class="rounded-full border border-white/10 bg-white/[0.05] px-2.5 py-1 font-mono text-[10px] text-white/50">{{ t.tag }}</span>
                                        </div>
                                        <h3 class="font-display font-bold text-white">{{ t.judul }}</h3>
                                        <p class="mt-2 text-sm leading-relaxed text-white/40">{{ t.cerita }}</p>
                                    </div>
                                </div>
                                <div class="hidden md:block md:w-1/2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Angka + tokoh -->
            <section class="relative border-y border-white/5 bg-white/[0.015] py-16">
                <div class="mx-auto grid max-w-7xl items-stretch gap-6 px-5 lg:grid-cols-2 lg:px-8">
                    <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.025] p-8 text-white md:p-10">
                        <div class="absolute -bottom-10 -right-10 select-none text-[160px] text-white/5">❝</div>
                        <span class="mb-2 block font-mono text-[10px] tracking-[.3em] text-cyan-300">SAMBUTAN PENDIRI</span>
                        <blockquote class="mb-8 text-xl font-medium italic leading-relaxed text-white/80 md:text-2xl">
                            "Saintek bukan sekadar fakultas — ia rumah bagi rasa ingin tahu. Tugas BEM adalah menjaga api itu tetap menyala."
                        </blockquote>
                        <div class="space-y-4">
                            <div v-for="tk in tokoh" :key="tk.nama" class="rounded-xl border border-white/10 bg-white/[0.03] p-4">
                                <p class="mb-2 text-sm italic text-white/60">{{ tk.pesan }}</p>
                                <p class="text-sm font-semibold text-white">{{ tk.nama }}</p>
                                <p class="text-xs text-cyan-300">{{ tk.peran }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-cyan-300/20 bg-cyan-300/5 p-6">
                            <div class="font-display text-3xl font-black text-cyan-300">10+</div>
                            <div class="mt-1 font-mono text-[10px] tracking-widest text-white/40">TAHUN<br />PERJALANAN</div>
                        </div>
                        <div class="rounded-2xl border border-indigo-300/20 bg-indigo-300/5 p-6">
                            <div class="font-display text-3xl font-black text-indigo-300">8</div>
                            <div class="mt-1 font-mono text-[10px] tracking-widest text-white/40">PERIODE<br />KEPENGURUSAN</div>
                        </div>
                        <div class="rounded-2xl border border-violet-300/20 bg-violet-300/5 p-6">
                            <div class="font-display text-3xl font-black text-violet-300">100+</div>
                            <div class="mt-1 font-mono text-[10px] tracking-widest text-white/40">ALUMNI<br />PENGURUS</div>
                        </div>
                        <div class="rounded-2xl border border-emerald-300/20 bg-emerald-300/5 p-6">
                            <div class="font-display text-3xl font-black text-emerald-300">50+</div>
                            <div class="mt-1 font-mono text-[10px] tracking-widest text-white/40">PENGHARGAAN<br />DIRAIH</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="relative py-16">
                <div class="mx-auto flex max-w-7xl flex-col justify-center gap-4 px-5 text-center md:flex-row lg:px-8">
                    <Link href="/profile/visi-misi" class="rounded-full bg-white px-6 py-3 text-sm font-bold text-black transition hover:bg-cyan-300">Lihat Visi & Misi →</Link>
                    <Link href="/profile/struktural" class="rounded-full border border-white/15 px-6 py-3 text-sm font-medium text-white/60 transition hover:border-cyan-300/30 hover:text-white">Lihat Struktur Organisasi</Link>
                </div>
            </section>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
