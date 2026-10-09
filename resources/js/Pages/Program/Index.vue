<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, watch } from 'vue'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

const props = defineProps({
    programs: Object,
    filters: Object,
    departemens: Array,
    statusList: Object,
})

const selectedDepartemen = computed(() =>
    props.departemens.find((d) => String(d.id) === String(filters.departemen_id))
)

const filters = reactive({
    search: props.filters.search || '',
    departemen_id: props.filters.departemen_id || '',
    status: props.filters.status || '',
})

const search = () => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.departemen_id) params.append('departemen_id', filters.departemen_id)
    if (filters.status) params.append('status', filters.status)

    router.visit('/program-kerja' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}

let debounceTimer = null
watch(() => filters.search, () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(search, 300)
})

const categoryClass = (color) => ({
    'bg-cyan-300/10 text-cyan-300': color === 'blue',
    'bg-emerald-300/10 text-emerald-300': color === 'green',
    'bg-violet-300/10 text-violet-300': color === 'purple',
    'bg-orange-300/10 text-orange-300': color === 'orange',
    'bg-pink-300/10 text-pink-300': color === 'pink',
    'bg-indigo-300/10 text-indigo-300': color === 'indigo',
})

const statusClass = (status) => ({
    'bg-amber-300/10 text-amber-300': status === 'Berlangsung',
    'bg-white/10 text-white/50': status === 'Selesai',
    'bg-cyan-300/10 text-cyan-300': status === 'Akan Datang',
})
</script>

<template>
    <Head title="Program Kerja" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <PageHero
                label="PROGRAM KERJA"
                :crumbs="[{ label: 'BERANDA', href: '/' }, { label: 'PROGRAM' }]"
            >
                <template #title>
                    Semua <span class="gradient-text">Program Kami</span>
                </template>
                <template #description>
                    Jelajahi seluruh program kerja BEM SAINTEK dari setiap departemen.
                </template>
            </PageHero>

            <section class="relative pb-20">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="glass grid grid-cols-1 gap-4 rounded-2xl p-5 md:grid-cols-3">
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Cari judul atau kategori..."
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/25 outline-none transition focus:border-cyan-300/40 focus:ring-2 focus:ring-cyan-300/20"
                        />
                        <select
                            v-model="filters.departemen_id"
                            @change="search"
                            class="w-full cursor-pointer rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none transition focus:border-cyan-300/40 [&>option]:bg-[#0a0f1a]"
                        >
                            <option value="">Semua Departemen</option>
                            <option v-for="d in departemens" :key="d.id" :value="d.id">{{ d.nama }}</option>
                        </select>
                        <select
                            v-model="filters.status"
                            @change="search"
                            class="w-full cursor-pointer rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none transition focus:border-cyan-300/40 [&>option]:bg-[#0a0f1a]"
                        >
                            <option value="">Semua Status</option>
                            <option v-for="(label, key) in statusList" :key="key" :value="key">{{ label }}</option>
                        </select>
                    </div>

                    <div
                        v-if="selectedDepartemen"
                        class="mt-6 flex flex-col gap-3 rounded-2xl border border-cyan-300/20 bg-cyan-300/5 p-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p class="font-mono text-[11px] tracking-wide text-white/50">
                            MENAMPILKAN PROKER <span class="font-bold text-cyan-300">{{ selectedDepartemen.nama }}</span>
                        </p>
                        <Link :href="`/departemen/${selectedDepartemen.id}`" class="shrink-0 rounded-full bg-white px-5 py-2.5 text-center text-xs font-bold text-black transition hover:bg-cyan-300">
                            Halaman {{ selectedDepartemen.nama }} →
                        </Link>
                    </div>

                    <div v-if="programs.data.length" class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="program in programs.data"
                            :key="program.id"
                            class="group rounded-2xl border border-white/10 bg-white/[0.025] p-6 transition-all duration-300 hover:-translate-y-1 hover:border-cyan-300/20 hover:bg-white/[0.04]"
                        >
                            <div class="mb-4 flex items-start justify-between gap-2">
                                <span
                                    class="rounded-full px-3 py-1 font-mono text-[10px] tracking-wider"
                                    :class="categoryClass(program.color)"
                                >
                                    {{ program.category }}
                                </span>
                                <span
                                    class="rounded px-2 py-1 font-mono text-[10px] tracking-wider"
                                    :class="statusClass(program.status)"
                                >
                                    {{ program.status }}
                                </span>
                            </div>

                            <h3 class="font-display text-lg font-bold text-white transition-colors group-hover:text-cyan-300">
                                {{ program.title }}
                            </h3>
                            <p class="mt-2 text-sm leading-relaxed text-white/40">
                                {{ program.description || 'Belum ada deskripsi untuk program ini.' }}
                            </p>

                            <div class="mt-4 space-y-1 border-t border-white/5 pt-4 font-mono text-[11px] text-white/30">
                                <p>🏛️ Departemen {{ program.departemen }}</p>
                                <p v-if="program.lokasi">📍 {{ program.lokasi }}</p>
                                <p v-if="program.tanggal_mulai">🗓️ {{ program.tanggal_mulai }}<span v-if="program.tanggal_selesai"> — {{ program.tanggal_selesai }}</span></p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="mt-8 rounded-2xl border border-white/10 bg-white/[0.025] py-20 text-center">
                        <div class="mb-4 text-5xl">📋</div>
                        <h3 class="font-display font-bold text-white">Belum ada program kerja</h3>
                        <p class="mt-2 text-sm text-white/30">Coba ubah kata kunci atau filter yang dipilih.</p>
                    </div>

                    <div v-if="programs.last_page > 1" class="mt-8 flex items-center justify-center gap-2">
                        <Link
                            v-for="page in programs.last_page"
                            :key="page"
                            :href="programs.path + '?page=' + page"
                            :class="[
                                page === programs.current_page
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
