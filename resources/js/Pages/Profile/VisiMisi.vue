
<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

const misi = [
    {
        judul: 'Advokasi Mahasiswa',
        deskripsi:
            'Menampung dan memperjuangkan aspirasi mahasiswa untuk menciptakan lingkungan akademik yang lebih baik.',
    },
    {
        judul: 'Pengembangan Akademik',
        deskripsi:
            'Mendorong budaya riset, inovasi, dan prestasi mahasiswa di tingkat fakultas maupun nasional.',
    },
    {
        judul: 'Kolaborasi Organisasi',
        deskripsi:
            'Memperkuat sinergi antara BEM, HIMA, dan seluruh elemen mahasiswa Sains dan Teknologi.',
    },
    {
        judul: 'Pengabdian Masyarakat',
        deskripsi:
            'Menerapkan ilmu pengetahuan dan teknologi melalui kegiatan yang memberikan manfaat bagi masyarakat.',
    },
    {
        judul: 'Pengembangan Sumber Daya Mahasiswa',
        deskripsi:
            'Membangun karakter, kepemimpinan, dan integritas mahasiswa melalui kegiatan yang berkelanjutan.',
    },
]

const nilai = [
    {
        nama: 'Integritas',
        deskripsi: 'Menjalankan amanah dengan jujur dan bertanggung jawab.',
    },
    {
        nama: 'Kolaborasi',
        deskripsi: 'Membangun kerja sama yang terbuka dan saling menguatkan.',
    },
    {
        nama: 'Inovasi',
        deskripsi: 'Mengembangkan gagasan dan solusi yang relevan.',
    },
    {
        nama: 'Profesionalitas',
        deskripsi: 'Bekerja secara terarah, disiplin, dan terukur.',
    },
]
</script>

<template>
    <Head title="Visi & Misi - BEM SAINTEK" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>

        <Navbar />

        <main>
            <PageHero
                label="PROFIL ORGANISASI"
                description="Landasan dan arah gerak BEM SAINTEK dalam menjalankan peran sebagai wadah aspirasi mahasiswa."
                :crumbs="[
                    { label: 'BERANDA', href: '/' },
                    { label: 'PROFIL' },
                    { label: 'VISI & MISI' },
                ]"
            >
                <template #title>
                    Visi & Misi
                    <span class="gradient-text">BEM SAINTEK</span>
                </template>
            </PageHero>

            <!-- Visi -->
            <section class="py-12 md:py-16">
                <div class="mx-auto max-w-5xl px-5 lg:px-8">
                    <div
                        class="rounded-2xl border border-white/10 bg-white/[0.025] p-7 md:p-12"
                    >
                        <p
                            class="mb-5 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300"
                        >
                            Visi
                        </p>

                        <blockquote
                            class="max-w-4xl text-xl font-semibold leading-relaxed text-white md:text-3xl"
                        >
                            Menjadikan BEM SAINTEK sebagai organisasi mahasiswa
                            yang aspiratif, progresif, dan kolaboratif dalam
                            mendorong kemajuan fakultas serta pengembangan
                            potensi mahasiswa.
                        </blockquote>
                    </div>
                </div>
            </section>

            <!-- Misi -->
            <section class="pb-14 md:pb-20">
                <div class="mx-auto max-w-5xl px-5 lg:px-8">
                    <div class="mb-8">
                        <p
                            class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300"
                        >
                            Arah Gerak Organisasi
                        </p>

                        <h2
                            class="text-2xl font-bold text-white md:text-3xl"
                        >
                            Misi Kami
                        </h2>

                        <p class="mt-3 max-w-2xl text-sm leading-7 text-white/50">
                            Langkah utama yang menjadi dasar pelaksanaan
                            program dan pelayanan kepada mahasiswa.
                        </p>
                    </div>

                    <div class="divide-y divide-white/10 border-y border-white/10">
                        <article
                            v-for="(item, index) in misi"
                            :key="item.judul"
                            class="grid gap-2 py-5 sm:grid-cols-[40px_200px_1fr] sm:gap-5"
                        >
                            <span
                                class="text-sm font-medium text-cyan-300"
                            >
                                {{ String(index + 1).padStart(2, '0') }}
                            </span>

                            <h3 class="font-semibold text-white">
                                {{ item.judul }}
                            </h3>

                            <p
                                class="text-sm leading-7 text-white/50"
                            >
                                {{ item.deskripsi }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- Nilai Organisasi -->
            <section
                class="border-y border-white/5 bg-white/[0.015] py-14 md:py-16"
            >
                <div class="mx-auto max-w-5xl px-5 lg:px-8">
                    <div class="mb-8">
                        <p
                            class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300"
                        >
                            Prinsip Organisasi
                        </p>

                        <h2 class="text-2xl font-bold text-white md:text-3xl">
                            Nilai yang Kami Pegang
                        </h2>
                    </div>

                    <div class="grid gap-x-10 sm:grid-cols-2">
                        <article
                            v-for="item in nilai"
                            :key="item.nama"
                            class="border-b border-white/10 py-5"
                        >
                            <h3 class="font-semibold text-white">
                                {{ item.nama }}
                            </h3>

                            <p class="mt-2 text-sm leading-7 text-white/50">
                                {{ item.deskripsi }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- Motto -->
            <section class="py-14 md:py-20">
                <div class="mx-auto max-w-5xl px-5 lg:px-8">
                    <div
                        class="flex flex-col gap-6 rounded-2xl border border-cyan-300/15 bg-cyan-300/[0.04] p-7 md:flex-row md:items-center md:justify-between md:p-10"
                    >
                        <div class="max-w-2xl">
                            <p
                                class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300"
                            >
                                BEM SAINTEK
                            </p>

                            <h2
                                class="text-xl font-semibold leading-relaxed text-white md:text-2xl"
                            >
                                Bersama Membangun, Bersama Berprestasi,
                                Bersama Menginspirasi.
                            </h2>
                        </div>

                        <div class="flex shrink-0 flex-wrap gap-3">
                            <Link
                                href="/profile/sejarah"
                                class="rounded-full border border-white/15 px-5 py-2.5 text-sm text-white/70 transition hover:border-cyan-300/40 hover:text-white"
                            >
                                Sejarah
                            </Link>

                            <Link
                                href="/profile/struktural"
                                class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-black transition hover:bg-cyan-300"
                            >
                                Struktur Pengurus
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
