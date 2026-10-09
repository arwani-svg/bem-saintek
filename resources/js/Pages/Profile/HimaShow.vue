<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'
import { himas, findHima } from '@/data/hima.js'

const props = defineProps({
    slug: { type: String, required: true },
})

const hima = computed(() => findHima(props.slug))
const lainnya = computed(() => himas.filter((h) => h.slug !== props.slug))
const indexAktif = computed(() => himas.findIndex((h) => h.slug === props.slug))
const prevHima = computed(() => (indexAktif.value > 0 ? himas[indexAktif.value - 1] : himas[himas.length - 1]))
const nextHima = computed(() =>
    indexAktif.value >= 0 && indexAktif.value < himas.length - 1 ? himas[indexAktif.value + 1] : himas[0],
)
</script>

<template>
    <Head :title="hima ? `${hima.singkatan} - HIMA` : 'HIMA Tidak Ditemukan'" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main v-if="hima">
            <PageHero
                :label="`HIMA // ${hima.prodi.toUpperCase()}`"
                :description="hima.deskripsi"
                :crumbs="[
                    { label: 'BERANDA', href: '/' },
                    { label: 'HIMA', href: '/profile/hima' },
                    { label: hima.singkatan },
                ]"
            >
                <template #title>
                    <span
                        class="mr-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-white/20 bg-white/10 text-4xl align-middle"
                    >
                        {{ hima.icon }}
                    </span>
                    {{ hima.singkatan }}
                    <span class="gradient-text mt-2 block text-xl font-medium">{{ hima.nama }}</span>
                </template>
                <template #extra>
                    <div class="mt-8 flex flex-wrap gap-8">
                        <div class="flex items-center gap-3">
                            <div class="font-display text-2xl font-bold text-white">{{ hima.anggota }}</div>
                            <div class="font-mono text-[9px] tracking-[.25em] text-white/30">ANGGOTA<br />TERDATA</div>
                        </div>
                        <div class="w-px bg-white/10"></div>
                        <div class="flex items-center gap-3">
                            <div class="font-display text-2xl font-bold text-cyan-300">{{ hima.proker.length }}</div>
                            <div class="font-mono text-[9px] tracking-[.25em] text-white/30">PROKER<br />UNGGULAN</div>
                        </div>
                        <div class="w-px bg-white/10"></div>
                        <div class="flex items-center gap-3">
                            <div class="font-display text-2xl font-bold text-white">{{ hima.divisi.length }}</div>
                            <div class="font-mono text-[9px] tracking-[.25em] text-white/30">DIVISI<br />AKTIF</div>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3 font-mono text-[11px]">
                        <span class="rounded-xl border border-white/10 bg-white/[0.05] px-4 py-2 text-white/60">📸 {{ hima.instagram }}</span>
                        <span class="rounded-xl border border-white/10 bg-white/[0.05] px-4 py-2 text-white/60">✉️ {{ hima.email }}</span>
                        <span class="rounded-xl border border-white/10 bg-white/[0.05] px-4 py-2 text-white/60">📍 {{ hima.sekretariat }}</span>
                    </div>
                </template>
            </PageHero>

            <!-- 01 Tentang -->
            <section class="relative py-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="glass rounded-2xl p-6 md:p-8">
                        <div class="mb-4 font-mono text-[10px] tracking-[.3em] text-cyan-300">01 / TENTANG {{ hima.singkatan }}</div>
                        <h2 class="font-display text-2xl font-bold text-white">Apa itu {{ hima.singkatan }}?</h2>
                        <p class="mt-4 max-w-3xl text-sm leading-relaxed text-white/50">{{ hima.tentang }}</p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span
                                v-for="d in hima.divisi"
                                :key="d"
                                class="rounded-full border border-cyan-300/20 bg-cyan-300/5 px-4 py-1.5 text-xs font-semibold text-cyan-300"
                            >
                                {{ d }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 02 Fungsi -->
            <section class="relative pb-4">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-6">
                        <div class="mb-3 font-mono text-[10px] tracking-[.3em] text-cyan-300">02 / FUNGSI & PERAN</div>
                        <h2 class="font-display text-2xl font-bold text-white">Kenapa harus aktif di {{ hima.singkatan }}?</h2>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="(f, i) in hima.fungsi"
                            :key="f.judul"
                            class="rounded-2xl border border-white/10 bg-white/[0.025] p-6 transition-colors hover:border-cyan-300/20"
                        >
                            <div class="font-display mb-3 text-4xl font-black text-white/10">{{ String(i + 1).padStart(2, '0') }}</div>
                            <h3 class="font-display font-semibold text-white">{{ f.judul }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-white/40">{{ f.deskripsi }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 03 Proker -->
            <section class="relative py-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-6 flex items-end justify-between">
                        <div>
                            <div class="mb-3 font-mono text-[10px] tracking-[.3em] text-cyan-300">03 / PROGRAM KERJA</div>
                            <h2 class="font-display text-2xl font-bold text-white">Proker unggulan {{ hima.singkatan }}</h2>
                        </div>
                        <Link href="/program-kerja" class="hidden text-sm font-semibold text-cyan-300 hover:text-white sm:inline-block">
                            Semua program →
                        </Link>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div
                            v-for="p in hima.proker"
                            :key="p.nama"
                            class="group rounded-2xl border border-white/10 bg-white/[0.025] p-6 transition-all hover:-translate-y-1 hover:border-cyan-300/20"
                        >
                            <span :class="`bg-gradient-to-br ${hima.warna} mb-4 inline-block rounded-xl px-3 py-1 font-mono text-[10px] font-bold text-white`">
                                {{ hima.singkatan }}
                            </span>
                            <h3 class="font-display text-lg font-bold text-white group-hover:text-cyan-300">{{ p.nama }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-white/40">{{ p.deskripsi }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 04 Pengurus -->
            <section class="relative pb-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-6">
                        <div class="mb-3 font-mono text-[10px] tracking-[.3em] text-cyan-300">04 / PENGURUS HARIAN</div>
                        <h2 class="font-display text-2xl font-bold text-white">Kenalan dengan pengurus {{ hima.singkatan }}</h2>
                        <p class="mt-2 text-sm text-white/40">Struktur inti periode berjalan. Ganti nama di <span class="font-mono text-cyan-300">resources/js/data/hima.js</span>.</p>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <article
                            v-for="p in hima.pengurus"
                            :key="p.jabatan"
                            class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.025] transition-all hover:-translate-y-1 hover:border-cyan-300/20"
                        >
                            <div :class="`bg-gradient-to-br ${hima.warna} relative h-20`">
                                <span class="absolute bottom-3 left-5 text-xs font-semibold uppercase tracking-wide text-white/90">{{ p.jabatan }}</span>
                            </div>
                            <div class="p-5">
                                <h3 class="font-display font-bold text-white">{{ p.nama }}</h3>
                                <p class="mt-1 text-xs text-white/30">{{ hima.prodi }}</p>
                                <p class="mt-3 text-sm leading-relaxed text-white/40">{{ p.tugas }}</p>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- Navigasi HIMA lain + CTA -->
            <section class="relative pb-20">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <h3 class="mb-4 font-mono text-[10px] tracking-[.3em] text-white/30">JELAJAHI HIMA LAINNYA</h3>
                    <div class="mb-8 flex flex-wrap gap-3">
                        <Link
                            v-for="h in lainnya"
                            :key="h.slug"
                            :href="`/profile/hima/${h.slug}`"
                            class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.025] px-4 py-2.5 text-sm font-medium text-white/60 transition-all hover:border-cyan-300/30 hover:text-cyan-300"
                        >
                            <span class="text-lg">{{ h.icon }}</span> {{ h.singkatan }}
                        </Link>
                    </div>

                    <div class="flex flex-col justify-between gap-6 rounded-2xl border border-white/10 bg-white/[0.025] p-6 md:flex-row md:items-center">
                        <Link
                            :href="`/profile/hima/${prevHima.slug}`"
                            class="text-sm text-white/40 transition hover:text-cyan-300"
                        >
                            ← {{ prevHima.singkatan }}
                        </Link>
                        <Link href="/profile/hima" class="text-sm font-bold text-white transition hover:text-cyan-300">
                            Semua HIMA
                        </Link>
                        <Link
                            :href="`/profile/hima/${nextHima.slug}`"
                            class="text-sm text-white/40 transition hover:text-cyan-300"
                        >
                            {{ nextHima.singkatan }} →
                        </Link>
                    </div>

                    <div class="mt-8 flex flex-col justify-between gap-6 rounded-2xl border border-cyan-300/20 bg-gradient-to-r from-cyan-300/10 to-violet-500/10 p-8 md:flex-row md:items-center md:p-12">
                        <div>
                            <h3 class="font-display text-2xl font-bold text-white">Mau gabung {{ hima.singkatan }}?</h3>
                            <p class="mt-1 text-white/40">Hubungi kesekretariatan {{ hima.sekretariat }} atau DM {{ hima.instagram }}.</p>
                        </div>
                        <div class="flex shrink-0 gap-3">
                            <Link href="/kontak" class="rounded-full bg-white px-6 py-3 text-sm font-bold text-black transition hover:bg-cyan-300">
                                Hubungi Kami
                            </Link>
                            <Link href="/join" class="rounded-full border border-white/15 px-6 py-3 text-sm font-bold text-white transition hover:border-cyan-300/40 hover:text-cyan-300">
                                Join BEM
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Fallback slug tidak dikenal -->
        <main v-else class="mx-auto max-w-3xl px-5 py-32 text-center">
            <div class="mb-4 text-6xl">🔍</div>
            <h1 class="font-display text-3xl font-bold text-white">HIMA "{{ slug }}" tidak ditemukan</h1>
            <p class="mt-3 text-white/40">Slug yang tersedia: {{ himas.map((h) => h.slug).join(', ') }}.</p>
            <Link href="/profile/hima" class="mt-8 inline-block rounded-full bg-white px-6 py-3 text-sm font-bold text-black transition hover:bg-cyan-300">
                Kembali ke Semua HIMA
            </Link>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
