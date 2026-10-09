<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import Reveal from '@/components/ui/Reveal.vue'

const props = defineProps({
    informasis: {
        type: Array,
        default: () => [],
    },
})

const list = computed(() => (props.informasis ?? []).slice(0, 5))
const utama = computed(() => list.value[0] ?? null)
const lainnya = computed(() => list.value.slice(1, 5))

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
    <section id="sekilas-informasi" class="relative border-y border-white/5 bg-white/[.015] py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <Reveal variant="up">
                <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                    <div>
                        <div class="font-mono text-[10px] tracking-[.3em] text-cyan-300">08 / SEKILAS INFORMASI</div>
                        <h2 class="font-display mt-5 text-4xl font-bold tracking-[-.04em] text-white md:text-6xl">
                            KABAR <span class="text-white/20">TERKINI.</span>
                        </h2>
                        <p class="mt-4 max-w-xl text-sm leading-relaxed text-white/30">
                            5 informasi terbaru dari BEM SAINTEK — berita, infografis, penghargaan, dan lomba.
                        </p>
                    </div>
                    <Link
                        href="/information"
                        class="inline-flex shrink-0 items-center gap-2 rounded-full border border-white/10 bg-white/[.025] px-5 py-2.5 text-xs font-bold text-white/60 transition hover:border-cyan-300/30 hover:text-white"
                    >
                        Semua Informasi
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </Reveal>

            <!-- Ada data: 1 sorotan besar + 4 daftar ringkas -->
            <div v-if="list.length" class="mt-12 grid gap-5 lg:grid-cols-2">
                <!-- Kartu utama -->
                <Reveal v-if="utama" variant="up">
                    <Link
                        :href="`/information/${utama.slug}`"
                        class="group relative flex h-full min-h-[420px] flex-col justify-end overflow-hidden rounded-[1.5rem] border border-white/10 transition-all duration-300 hover:-translate-y-1 hover:border-cyan-300/30"
                    >
                        <img
                            v-if="utama.gambar_url"
                            :src="utama.gambar_url"
                            :alt="utama.judul"
                            loading="lazy"
                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div v-else class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-cyan-500/20 via-transparent to-violet-500/20 text-7xl">
                            {{ jenisIcon(utama.jenis) }}
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#03050a] via-[#03050a]/60 to-transparent"></div>
                        <div class="relative p-7 sm:p-8">
                            <div class="mb-3 flex flex-wrap items-center gap-2">
                                <span class="rounded-full px-3 py-1 font-mono text-[10px] tracking-wider backdrop-blur-md" :class="jenisStyle(utama.jenis)">
                                    {{ jenisIcon(utama.jenis) }} {{ utama.jenis_label }}
                                </span>
                                <span v-if="utama.is_unggulan" class="rounded-full bg-amber-300/15 px-3 py-1 font-mono text-[10px] tracking-wider text-amber-300">
                                    ⭐ SOROTAN
                                </span>
                                <span v-if="utama.tanggal" class="font-mono text-[10px] text-white/40">🗓️ {{ utama.tanggal }}</span>
                            </div>
                            <h3 class="font-display text-2xl font-bold leading-tight text-white transition-colors group-hover:text-cyan-300 sm:text-3xl">
                                {{ utama.judul }}
                            </h3>
                            <p v-if="utama.ringkasan" class="mt-3 line-clamp-2 text-sm leading-relaxed text-white/50">
                                {{ utama.ringkasan }}
                            </p>
                            <span class="mt-4 inline-block text-xs font-bold text-cyan-300 opacity-0 transition-opacity group-hover:opacity-100">
                                {{ utama.is_artikel ? 'Baca selengkapnya →' : 'Lihat detail →' }}
                            </span>
                        </div>
                    </Link>
                </Reveal>

                <!-- 4 daftar ringkas -->
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                    <Reveal
                        v-for="(item, i) in lainnya"
                        :key="item.id"
                        variant="up"
                        :delay="i * 100"
                    >
                        <Link
                            :href="`/information/${item.slug}`"
                            class="group flex h-full flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.025] transition-all duration-300 hover:-translate-y-1 hover:border-cyan-300/20 hover:bg-white/[0.04]"
                        >
                            <div v-if="item.gambar_url" class="aspect-video overflow-hidden">
                                <img :src="item.gambar_url" :alt="item.judul" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            </div>
                            <div v-else class="flex items-center justify-center border-b border-white/5 bg-white/[0.02] py-8 text-4xl">
                                {{ jenisIcon(item.jenis) }}
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                <span class="mb-2 inline-block w-fit rounded-full px-2.5 py-1 font-mono text-[10px] tracking-wider" :class="jenisStyle(item.jenis)">
                                    {{ jenisIcon(item.jenis) }} {{ item.jenis_label }}
                                </span>
                                <h3 class="font-display line-clamp-2 text-[15px] font-bold leading-snug text-white transition-colors group-hover:text-cyan-300">
                                    {{ item.judul }}
                                </h3>
                                <p v-if="item.tanggal" class="mt-2 font-mono text-[11px] text-white/30">🗓️ {{ item.tanggal }}</p>
                            </div>
                        </Link>
                    </Reveal>

                    <!-- Jika data < 5, isi slot kosong dengan CTA -->
                    <Reveal v-if="list.length < 5" variant="up" :delay="300">
                        <Link
                            href="/information"
                            class="group flex h-full min-h-[220px] flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-cyan-300/20 bg-cyan-300/[0.03] p-6 text-center transition hover:border-cyan-300/40 hover:bg-cyan-300/[0.06]"
                        >
                            <div class="text-4xl">📭</div>
                            <p class="text-sm font-bold text-white/70">Jelajahi arsip informasi</p>
                            <span class="text-xs font-bold text-cyan-300">Lihat semua →</span>
                        </Link>
                    </Reveal>
                </div>
            </div>

            <!-- Kosong: ajak ke halaman informasi -->
            <Reveal v-else variant="up">
                <div class="mt-12 rounded-2xl border border-white/10 bg-white/[0.025] px-6 py-16 text-center">
                    <div class="mb-4 text-5xl">📭</div>
                    <h3 class="font-display font-bold text-white">Belum ada informasi terbaru</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm text-white/30">
                        Informasi yang dipublish akan otomatis muncul di sini (maksimal 5).
                    </p>
                    <Link
                        href="/information"
                        class="mt-6 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[.025] px-5 py-2.5 text-xs font-bold text-white/60 transition hover:border-cyan-300/30 hover:text-white"
                    >
                        Ke Halaman Informasi →
                    </Link>
                </div>
            </Reveal>
        </div>
    </section>
</template>
