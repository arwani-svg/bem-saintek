<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

// === EDIT DI SINI: visi, misi, nilai ===
const misi = [
    { icon: '🎓', judul: 'Advokasi Akademik', deskripsi: 'Memperjuangkan aspirasi akademik mahasiswa: kurikulum, fasilitas lab, beasiswa, dan transparansi kebijakan fakultas.' },
    { icon: '🔬', judul: 'Riset & Inovasi', deskripsi: 'Mendorong budaya riset melalui PKM, Gemastik, PIMNAS, dan inkubasi karya ilmiah mahasiswa Saintek.' },
    { icon: '🤝', judul: 'Sinergi HIMA', deskripsi: 'Mengorkestrasi kolaborasi 6 HIMA jurusan agar proker berjalan selaras dan saling menguatkan.' },
    { icon: '🌍', judul: 'Pengabdian Masyarakat', deskripsi: 'Menghadirkan sains yang membumi: desa digital, sekolah binaan, dan aksi lingkungan.' },
    { icon: '💎', judul: 'Pembinaan Karakter', deskripsi: 'Membentuk kader saintis yang berintegritas lewat LKMM, upgrading, dan mentoring berjenjang.' },
]

const nilai = [
    { icon: '🛡️', nama: 'Integritas', deskripsi: 'Jujur dalam data, amanah dalam anggaran.' },
    { icon: '🤲', nama: 'Solidaritas', deskripsi: 'Satu Saintek, saling menopang.' },
    { icon: '💡', nama: 'Inovatif', deskripsi: 'Berani mencoba, cepat belajar.' },
    { icon: '⚙️', nama: 'Profesional', deskripsi: 'Tuntas, tepat waktu, terukur.' },
]

const tujuan = [
    { target: '80% aspirasi mahasiswa terfasilitasi & terdokumentasi', progres: 80 },
    { target: '15+ tim lolos pendanaan PKM / kompetisi nasional', progres: 65 },
    { target: '6 HIMA aktif berkolaborasi dalam 4 proker bersama', progres: 90 },
    { target: '3 desa / sekolah binaan berjalan berkelanjutan', progres: 55 },
]
</script>

<template>
    <Head title="Visi & Misi - Profil" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <PageHero
                label="PROFIL // ARAH PERJUANGAN"
                description="Kompas yang menuntun setiap program kerja periode 2024/2025."
                :crumbs="[{ label: 'BERANDA', href: '/' }, { label: 'PROFIL' }, { label: 'VISI & MISI' }]"
            >
                <template #title>
                    Visi & Misi <span class="gradient-text">BEM SAINTEK</span>
                </template>
            </PageHero>

            <!-- Visi -->
            <section class="relative py-10">
                <div class="mx-auto max-w-5xl px-5 lg:px-8">
                    <div class="relative overflow-hidden rounded-3xl border border-cyan-300/20 bg-gradient-to-r from-cyan-300/10 via-white/[0.03] to-violet-500/10 p-8 md:p-14">
                        <div class="absolute -top-8 -right-4 select-none text-[180px] leading-none text-white/5">”</div>
                        <span class="mb-6 inline-block rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-1 font-mono text-[10px] tracking-[.25em] text-cyan-300">VISI</span>
                        <blockquote class="font-display text-2xl font-bold leading-snug text-white md:text-3xl">
                            "Terwujudnya BEM SAINTEK sebagai motor penggerak mahasiswa yang unggul, aspiratif, dan responsif dalam mewujudkan fakultas yang inovatif dan berdaya saing global."
                        </blockquote>
                        <p class="mt-4 font-mono text-[11px] text-white/30">— DISAHKAN PADA MUSYAWARAH BESAR 2024</p>
                    </div>
                </div>
            </section>

            <!-- Misi -->
            <section class="relative py-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-10 text-center">
                        <div class="mb-4 font-mono text-[10px] tracking-[.3em] text-cyan-300">01 / MISI KAMI</div>
                        <h2 class="font-display text-3xl font-bold text-white">5 Pilar Pengabdian</h2>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="(m, i) in misi"
                            :key="m.judul"
                            class="group rounded-2xl border border-white/10 bg-white/[0.025] p-7 transition-all hover:border-cyan-300/20 hover:bg-white/[0.04]"
                        >
                            <div class="mb-4 flex items-center justify-between">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-white/10 bg-white/[0.05] text-2xl transition-transform group-hover:scale-110">{{ m.icon }}</div>
                                <span class="font-display text-4xl font-black text-white/10">0{{ i + 1 }}</span>
                            </div>
                            <h3 class="font-display font-bold text-white">{{ m.judul }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-white/40">{{ m.deskripsi }}</p>
                        </div>
                        <div class="flex flex-col justify-center rounded-2xl border border-cyan-300/20 bg-cyan-300/5 p-7">
                            <h3 class="font-display text-lg font-bold text-white">Misi diwujudkan lewat proker nyata.</h3>
                            <p class="mb-4 mt-1 text-sm text-white/40">Lihat bagaimana misi ini diterjemahkan ke program kerja.</p>
                            <Link href="/program-kerja" class="text-sm font-semibold text-cyan-300 hover:text-white">Lihat Program Kerja →</Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Nilai + Tujuan -->
            <section class="relative border-y border-white/5 bg-white/[0.015] py-16">
                <div class="mx-auto grid max-w-7xl gap-10 px-5 lg:grid-cols-2 lg:px-8">
                    <div>
                        <div class="mb-4 font-mono text-[10px] tracking-[.3em] text-emerald-300">02 / NILAI DASAR</div>
                        <h2 class="font-display mb-6 text-2xl font-bold text-white">Yang Kami Pegang Teguh</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div v-for="n in nilai" :key="n.nama" class="rounded-2xl border border-white/10 bg-white/[0.025] p-5">
                                <div class="mb-2 text-3xl">{{ n.icon }}</div>
                                <div class="font-display font-bold text-white">{{ n.nama }}</div>
                                <div class="text-sm text-white/40">{{ n.deskripsi }}</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 font-mono text-[10px] tracking-[.3em] text-amber-300">03 / TARGET STRATEGIS 2024/2025</div>
                        <h2 class="font-display mb-6 text-2xl font-bold text-white">Indikator Keberhasilan</h2>
                        <div class="space-y-4">
                            <div v-for="t in tujuan" :key="t.target" class="rounded-2xl border border-white/10 bg-white/[0.025] p-5">
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <p class="text-sm font-medium text-white/70">{{ t.target }}</p>
                                    <span class="font-mono text-sm font-bold text-cyan-300">{{ t.progres }}%</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-white/10">
                                    <div class="h-full rounded-full bg-gradient-to-r from-cyan-300 to-violet-400" :style="`width: ${t.progres}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Motto -->
            <section class="relative py-16">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="rounded-2xl border border-cyan-300/20 bg-gradient-to-r from-cyan-300/10 to-violet-500/10 p-8 text-center md:p-12">
                        <p class="mb-2 font-mono text-[10px] tracking-[.3em] text-cyan-300">MOTTO KAMI</p>
                        <p class="font-display text-2xl font-bold italic text-white md:text-3xl">"Bersama Membangun, Bersama Berprestasi, Bersama Menginspirasi"</p>
                        <div class="mt-8 flex flex-wrap justify-center gap-3">
                            <Link href="/profile/sejarah" class="rounded-full border border-white/15 px-6 py-2.5 text-sm font-medium text-white/60 transition hover:border-cyan-300/30 hover:text-white">← Sejarah Kami</Link>
                            <Link href="/profile/struktural" class="rounded-full bg-white px-6 py-2.5 text-sm font-bold text-black transition hover:bg-cyan-300">Kenali Pengurus →</Link>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
