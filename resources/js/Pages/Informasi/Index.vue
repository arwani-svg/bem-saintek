<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, watch } from 'vue'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

const props = defineProps({
    items: Object,
    unggulan: Array,
    filters: Object,
    jenisList: Object,
    counts: Object,
})

const filters = reactive({
    search: props.filters.search || '',
    jenis: props.filters.jenis || '',
})

const go = (extra = {}) => {
    const params = new URLSearchParams()
    const search = extra.search ?? filters.search
    const jenis = extra.jenis ?? filters.jenis
    if (search) params.append('search', search)
    if (jenis) params.append('jenis', jenis)

    router.visit('/information' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}

let debounceTimer = null
watch(() => filters.search, () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => go(), 300)
})

const setJenis = (jenis) => {
    filters.jenis = jenis
    go({ jenis })
}

const isFiltering = computed(() => !!(filters.search || filters.jenis))

const tabs = computed(() => [
    { key: '', label: 'Semua', count: props.counts.semua ?? 0 },
    { key: 'berita', label: 'Berita', count: props.counts.berita ?? 0 },
    { key: 'infografis', label: 'Infografis', count: props.counts.infografis ?? 0 },
    { key: 'penghargaan', label: 'Penghargaan', count: props.counts.penghargaan ?? 0 },
    { key: 'lomba', label: 'Lomba', count: props.counts.lomba ?? 0 },
])

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
    <Head title="Informasi" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <PageHero
                label="INFORMASI"
                :crumbs="[{ label: 'BERANDA', href: '/' }, { label: 'INFORMASI' }]"
            >
                <template #title>
                    Kabar & <span class="gradient-text">Informasi Terkini</span>
                </template>
                <template #description>
                    Berita, infografis, penghargaan, dan info lomba dari BEM SAINTEK — semua dalam satu tempat.
                </template>
            </PageHero>

            <section class="relative pb-20">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <!-- Tabs jenis -->
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            type="button"
                            @click="setJenis(tab.key)"
                            :class="[
                                filters.jenis === tab.key
                                    ? 'border-cyan-300/50 bg-cyan-300/10 text-white'
                                    : 'border-white/10 bg-white/[0.025] text-white/50 hover:border-cyan-300/30 hover:text-white',
                                'flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 text-xs font-bold transition'
                            ]"
                        >
                            {{ tab.label }}
                            <span
                                :class="[
                                    filters.jenis === tab.key ? 'bg-cyan-300 text-black' : 'bg-white/10 text-white/50',
                                    'rounded-full px-2 py-0.5 font-mono text-[10px]'
                                ]"
                            >
                                {{ tab.count }}
                            </span>
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="glass mt-4 rounded-2xl p-5">
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Cari judul atau keterangan..."
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/25 outline-none transition focus:border-cyan-300/40 focus:ring-2 focus:ring-cyan-300/20"
                        />
                    </div>

                    <!-- Sorotan unggulan (tampil saat tidak memfilter) -->
                    <div v-if="unggulan.length && !isFiltering" class="mt-8">
                        <p class="mb-4 font-mono text-[11px] tracking-[.25em] text-white/30">⭐ SOROTAN</p>
                        <div class="grid gap-5" :class="unggulan.length > 1 ? 'md:grid-cols-2' : ''">
                            <Link
                                v-for="item in unggulan.slice(0, 2)"
                                :key="'u-' + item.id"
                                :href="`/information/${item.slug}`"
                                class="group relative overflow-hidden rounded-2xl border border-cyan-300/20 bg-white/[0.03] transition-all duration-300 hover:-translate-y-1 hover:border-cyan-300/40"
                            >
                                <div v-if="item.gambar_url" class="aspect-[16/8] overflow-hidden">
                                    <img :src="item.gambar_url" :alt="item.judul" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                </div>
                                <div class="p-6">
                                    <div class="mb-3 flex items-center gap-2">
                                        <span class="rounded-full px-3 py-1 font-mono text-[10px] tracking-wider" :class="jenisStyle(item.jenis)">
                                            {{ jenisIcon(item.jenis) }} {{ item.jenis_label }}
                                        </span>
                                        <span v-if="item.tanggal" class="font-mono text-[10px] text-white/30">🗓️ {{ item.tanggal }}</span>
                                    </div>
                                    <h3 class="font-display text-xl font-bold text-white transition-colors group-hover:text-cyan-300">
                                        {{ item.judul }}
                                    </h3>
                                    <p v-if="item.ringkasan" class="mt-2 line-clamp-2 text-sm leading-relaxed text-white/40">
                                        {{ item.ringkasan }}
                                    </p>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Grid daftar -->
                    <div v-if="items.data.length" class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="item in items.data"
                            :key="item.id"
                            :href="`/information/${item.slug}`"
                            class="group flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.025] transition-all duration-300 hover:-translate-y-1 hover:border-cyan-300/20 hover:bg-white/[0.04]"
                        >
                            <div v-if="item.gambar_url" class="relative overflow-hidden" :class="item.is_artikel ? 'aspect-video' : 'aspect-square'">
                                <img :src="item.gambar_url" :alt="item.judul" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                <span class="absolute left-3 top-3 rounded-full px-3 py-1 font-mono text-[10px] tracking-wider backdrop-blur-md" :class="jenisStyle(item.jenis)">
                                    {{ jenisIcon(item.jenis) }} {{ item.jenis_label }}
                                </span>
                            </div>
                            <div v-else class="flex items-center justify-center border-b border-white/5 bg-white/[0.02] py-10 text-5xl">
                                {{ jenisIcon(item.jenis) }}
                            </div>

                            <div class="flex flex-1 flex-col p-6">
                                <div v-if="!item.gambar_url" class="mb-3">
                                    <span class="rounded-full px-3 py-1 font-mono text-[10px] tracking-wider" :class="jenisStyle(item.jenis)">
                                        {{ jenisIcon(item.jenis) }} {{ item.jenis_label }}
                                    </span>
                                </div>
                                <h3 class="font-display text-lg font-bold leading-snug text-white transition-colors group-hover:text-cyan-300">
                                    {{ item.judul }}
                                </h3>
                                <p v-if="item.ringkasan" class="mt-2 line-clamp-3 flex-1 text-sm leading-relaxed text-white/40">
                                    {{ item.ringkasan }}
                                </p>
                                <div class="mt-4 flex items-center justify-between border-t border-white/5 pt-4">
                                    <span v-if="item.tanggal" class="font-mono text-[11px] text-white/30">🗓️ {{ item.tanggal }}</span>
                                    <span v-else></span>
                                    <span class="text-xs font-bold text-cyan-300 opacity-0 transition-opacity group-hover:opacity-100">
                                        {{ item.is_artikel ? 'Baca selengkapnya →' : 'Lihat detail →' }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div v-else class="mt-8 rounded-2xl border border-white/10 bg-white/[0.025] py-20 text-center">
                        <div class="mb-4 text-5xl">📭</div>
                        <h3 class="font-display font-bold text-white">Belum ada informasi</h3>
                        <p class="mt-2 text-sm text-white/30">Coba ubah kata kunci atau jenis yang dipilih.</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="items.last_page > 1" class="mt-8 flex items-center justify-center gap-2">
                        <Link
                            v-for="page in items.last_page"
                            :key="page"
                            :href="items.path + '?page=' + page"
                            :class="[
                                page === items.current_page
                                    ? 'border-cyan-300/40 bg-cyan-300 text-black'
                                    : 'border-white/10 bg-white/[0.025] text-white/50 hover:border-cyan-300/30 hover:text-white',
                                'rounded-xl border px-4 py-2 text-sm font-bold transition'
                            ]"
                        >
                            {{ page }}
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
