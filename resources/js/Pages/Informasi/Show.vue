<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

const props = defineProps({
    item: Object,
    terkait: Array,
    jenisList: Object,
})

const jenisStyle = (jenis) => ({
    berita: 'bg-cyan-300/10 text-cyan-300',
    infografis: 'bg-violet-300/10 text-violet-300',
    penghargaan: 'bg-amber-300/10 text-amber-300',
    lomba: 'bg-emerald-300/10 text-emerald-300',
}[jenis] || 'bg-white/10 text-white/50')

const jenisIcon = (jenis) => ({
    berita: '📰',
    infografis: '📊',
    penghargaan: '🏆',
    lomba: '🎯',
}[jenis] || '📌')
</script>

<template>
    <Head :title="item.judul" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <section class="relative overflow-hidden pb-10 pt-32">
                <div class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div class="grid-bg absolute inset-0 opacity-[0.11]"></div>
                    <div class="absolute -top-40 left-1/2 h-[380px] w-[680px] -translate-x-1/2 rounded-full bg-cyan-400/[0.06] blur-[120px]"></div>
                </div>

                <div class="relative mx-auto max-w-4xl px-5 lg:px-8">
                    <nav class="mb-6 flex flex-wrap items-center gap-2 font-mono text-[10px] tracking-[.2em] text-white/30">
                        <Link href="/" class="transition hover:text-cyan-300">BERANDA</Link>
                        <span class="text-white/15">/</span>
                        <Link href="/information" class="transition hover:text-cyan-300">INFORMASI</Link>
                        <span class="text-white/15">/</span>
                        <button
                            type="button"
                            @click="$inertia.visit('/information?jenis=' + item.jenis)"
                            class="cursor-pointer transition hover:text-cyan-300"
                        >
                            {{ item.jenis_label.toUpperCase() }}
                        </button>
                    </nav>

                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-3 py-1 font-mono text-[10px] tracking-wider" :class="jenisStyle(item.jenis)">
                            {{ jenisIcon(item.jenis) }} {{ item.jenis_label }}
                        </span>
                        <span v-if="item.tanggal" class="font-mono text-[11px] text-white/30">🗓️ {{ item.tanggal }}</span>
                    </div>

                    <h1 class="font-display text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl">
                        {{ item.judul }}
                    </h1>

                    <p v-if="item.ringkasan" class="mt-4 border-l-2 border-cyan-300/40 pl-4 text-sm leading-relaxed text-white/50 sm:text-base">
                        {{ item.ringkasan }}
                    </p>
                </div>
            </section>

            <section class="relative pb-20">
                <div class="mx-auto max-w-4xl px-5 lg:px-8">
                    <!-- Gambar utama -->
                    <div v-if="item.gambar_url" class="overflow-hidden rounded-2xl border border-white/10">
                        <img :src="item.gambar_url" :alt="item.judul" class="w-full object-cover" :class="item.is_artikel ? 'aspect-video' : 'max-h-[80vh]'" />
                    </div>

                    <!-- Narasi panjang untuk berita & penghargaan -->
                    <article
                        v-if="item.is_artikel && item.konten"
                        class="mt-8 rounded-2xl border border-white/10 bg-white/[0.025] p-6 sm:p-8"
                    >
                        <p class="whitespace-pre-line text-[15px] leading-[1.9] text-white/70">
                            {{ item.konten }}
                        </p>
                    </article>

                    <!-- Untuk infografis & lomba: gambar + caption sudah cukup, beri tombol aksi kembali -->
                    <div v-else-if="!item.is_artikel" class="mt-6 rounded-2xl border border-white/10 bg-white/[0.025] p-6 text-center">
                        <p class="text-sm text-white/40">
                            "{{ item.judul }}" — pantau terus halaman informasi untuk kabar terbaru {{ item.jenis_label.toLowerCase() }} lainnya.
                        </p>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <Link
                            href="/information"
                            class="rounded-full border border-white/10 bg-white/[0.025] px-5 py-2.5 text-xs font-bold text-white/60 transition hover:border-cyan-300/30 hover:text-white"
                        >
                            ← Semua Informasi
                        </Link>
                        <button
                            type="button"
                            @click="$inertia.visit('/information?jenis=' + item.jenis)"
                            class="cursor-pointer rounded-full border border-white/10 bg-white/[0.025] px-5 py-2.5 text-xs font-bold text-white/60 transition hover:border-cyan-300/30 hover:text-white"
                        >
                            {{ jenisIcon(item.jenis) }} {{ item.jenis_label }} lainnya
                        </button>
                    </div>

                    <!-- Terkait -->
                    <div v-if="terkait.length" class="mt-12">
                        <p class="mb-4 font-mono text-[11px] tracking-[.25em] text-white/30">✨ TERKAIT</p>
                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <Link
                                v-for="rel in terkait"
                                :key="rel.id"
                                :href="`/information/${rel.slug}`"
                                class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.025] transition-all duration-300 hover:-translate-y-1 hover:border-cyan-300/20"
                            >
                                <div v-if="rel.gambar_url" class="aspect-video overflow-hidden">
                                    <img :src="rel.gambar_url" :alt="rel.judul" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                </div>
                                <div class="p-5">
                                    <span class="mb-2 inline-block rounded-full px-2.5 py-1 font-mono text-[10px] tracking-wider" :class="jenisStyle(rel.jenis)">
                                        {{ jenisIcon(rel.jenis) }} {{ rel.jenis_label }}
                                    </span>
                                    <h3 class="font-display text-sm font-bold leading-snug text-white transition-colors group-hover:text-cyan-300">
                                        {{ rel.judul }}
                                    </h3>
                                    <p v-if="rel.tanggal" class="mt-2 font-mono text-[11px] text-white/30">🗓️ {{ rel.tanggal }}</p>
                                </div>
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
