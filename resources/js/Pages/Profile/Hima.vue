<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'
import { himas } from '@/data/hima.js'

// Data HIMA terpusat di resources/js/data/hima.js
// Selaras dengan User::PRODI di app/Models/User.php:
// Teknologi Informasi, Sistem Informasi, Ilmu Komputer,
// Arsitektur, Budidaya Perikanan, Teknologi Hasil Perikanan

const alurGabung = [
    { step: '01', judul: 'Pilih HIMA', deskripsi: 'Sesuaikan dengan prodi / minatmu. Setiap mahasiswa otomatis menjadi anggota muda HIMA prodinya.' },
    { step: '02', judul: 'Isi Formulir', deskripsi: 'Isi formulir pendataan anggota di sekretariat HIMA atau via link pendaftaran online.' },
    { step: '03', judul: 'Ikuti Pengkaderan', deskripsi: 'Ikuti LKMM / upgrading untuk resmi dilantik menjadi pengurus atau anggota aktif.' },
    { step: '04', judul: 'Berkontribusi', deskripsi: 'Pilih divisi favoritmu dan ikut berkontribusi di proker HIMA & BEM SAINTEK.' },
]
</script>

<template>
    <Head title="HIMA - Profil" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <PageHero
                label="PROFIL // HIMPUNAN MAHASISWA"
                description="Setiap prodi punya rumahnya sendiri. HIMA adalah ujung tombak pembinaan mahasiswa di tingkat prodi — sekaligus mitra strategis BEM SAINTEK."
                :crumbs="[{ label: 'BERANDA', href: '/' }, { label: 'PROFIL' }, { label: 'HIMA' }]"
            >
                <template #title>
                    Kenali <span class="gradient-text">6 HIMA</span> di Fakultas Saintek
                </template>
                <template #extra>
                    <div class="mt-8 flex flex-wrap gap-8">
                        <div class="flex items-center gap-3">
                            <div class="font-display text-2xl font-bold text-white">6</div>
                            <div class="font-mono text-[9px] tracking-[.25em] text-white/30">HIMA<br />AKTIF</div>
                        </div>
                        <div class="w-px bg-white/10"></div>
                        <div class="flex items-center gap-3">
                            <div class="font-display text-2xl font-bold text-white">525+</div>
                            <div class="font-mono text-[9px] tracking-[.25em] text-white/30">ANGGOTA<br />TERDATA</div>
                        </div>
                        <div class="w-px bg-white/10"></div>
                        <div class="flex items-center gap-3">
                            <div class="font-display text-2xl font-bold text-white">30+</div>
                            <div class="font-mono text-[9px] tracking-[.25em] text-white/30">PROKER /<br />TAHUN</div>
                        </div>
                    </div>
                </template>
            </PageHero>

            <!-- Grid HIMA -->
            <section class="relative py-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="hima in himas"
                            :id="hima.slug"
                            :key="hima.singkatan"
                            class="group scroll-mt-28 overflow-hidden rounded-2xl border border-white/10 bg-white/[0.025] transition-all duration-300 hover:-translate-y-1 hover:border-cyan-300/20"
                        >
                            <div :class="`bg-gradient-to-br ${hima.warna} relative overflow-hidden p-6 text-white`">
                                <div class="absolute -right-4 -top-4 select-none text-[80px] leading-none opacity-20">{{ hima.icon }}</div>
                                <span class="mb-3 inline-block rounded-full bg-white/20 px-3 py-1 font-mono text-[10px] backdrop-blur-sm">
                                    {{ hima.prodi }}
                                </span>
                                <h3 class="font-display text-2xl font-bold">{{ hima.singkatan }}</h3>
                                <p class="text-sm text-white/80">{{ hima.nama }}</p>
                            </div>
                            <div class="p-6">
                                <p class="mb-4 text-sm leading-relaxed text-white/40">{{ hima.deskripsi }}</p>
                                <div class="mb-4 text-sm text-white/30">
                                    Ketua: <span class="font-medium text-white">{{ hima.ketua }}</span>
                                </div>
                                <div class="mb-4 flex flex-wrap gap-2">
                                    <span v-for="p in hima.proker.slice(0, 3)" :key="p.nama" class="rounded-full border border-white/10 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/60">{{ p.nama }}</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-white/5 pt-4">
                                    <span class="font-mono text-[11px] text-white/30">{{ hima.anggota }} • {{ hima.instagram }}</span>
                                    <Link :href="`/profile/hima/${hima.slug}`" class="flex items-center gap-1 text-sm font-semibold text-cyan-300 transition-all hover:gap-2 hover:text-white">
                                        Kunjungi
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- Alur gabung -->
            <section class="relative border-y border-white/5 bg-white/[0.015] py-16">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-12 text-center">
                        <div class="mb-4 font-mono text-[10px] tracking-[.3em] text-cyan-300">01 / CARA BERGABUNG</div>
                        <h2 class="font-display text-3xl font-bold text-white">Mau Aktif di HIMA?</h2>
                        <p class="mx-auto mt-2 max-w-xl text-white/40">Empat langkah mudah untuk menjadi bagian dari HIMA prodimu.</p>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div v-for="a in alurGabung" :key="a.step" class="rounded-2xl border border-white/10 bg-white/[0.025] p-6 transition-colors hover:border-cyan-300/20">
                            <div class="font-display mb-3 text-4xl font-black text-white/10">{{ a.step }}</div>
                            <h3 class="font-display font-semibold text-white">{{ a.judul }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-white/40">{{ a.deskripsi }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="relative py-16">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="flex flex-col justify-between gap-6 rounded-2xl border border-cyan-300/20 bg-gradient-to-r from-cyan-300/10 to-violet-500/10 p-8 md:flex-row md:items-center md:p-12">
                        <div>
                            <h3 class="font-display text-2xl font-bold text-white">Bingung pilih HIMA / divisi?</h3>
                            <p class="mt-1 text-white/40">Hubungi Kesekretariatan BEM SAINTEK, kami arahkan ke HIMA yang paling cocok untukmu.</p>
                        </div>
                        <Link href="/kontak" class="shrink-0 rounded-full bg-white px-6 py-3 text-sm font-bold text-black transition hover:bg-cyan-300">Hubungi Kami</Link>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
