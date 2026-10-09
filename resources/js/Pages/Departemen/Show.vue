<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

const props = defineProps({
    departemen: Object,
    prokers: Array,
    lainnya: Array,
})

// Satu baris di kolom "tujuan" = satu poin
const tujuanList = computed(() =>
    (props.departemen.tujuan || '')
        .split('\n')
        .map((t) => t.trim())
        .filter(Boolean)
)

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
    <Head :title="`Departemen ${departemen.nama}`" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <PageHero
                label="DEPARTEMEN"
                :crumbs="[{ label: 'BERANDA', href: '/' }, { label: 'PROGRAM', href: '/program-kerja' }, { label: String(departemen.nama).toUpperCase() }]"
            >
                <template #title>
                    <span class="mr-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-cyan-300/20 bg-cyan-300/5 text-4xl align-middle">{{ departemen.icon }}</span>
                    {{ departemen.nama }}
                    <span v-if="departemen.kepanjangan" class="gradient-text mt-2 block text-xl font-medium">{{ departemen.kepanjangan }}</span>
                </template>
                <template #description>
                    {{ departemen.deskripsi }}
                </template>
                <template #extra>
                    <div class="mt-8 flex flex-wrap gap-8">
                        <div class="flex items-center gap-3">
                            <div class="font-display text-2xl font-bold text-white">{{ departemen.total_proker }}</div>
                            <div class="font-mono text-[9px] tracking-[.25em] text-white/30">TOTAL<br />PROKER</div>
                        </div>
                        <div class="w-px bg-white/10"></div>
                        <div class="flex items-center gap-3">
                            <div class="font-display text-2xl font-bold text-cyan-300">{{ departemen.proker_berlangsung }}</div>
                            <div class="font-mono text-[9px] tracking-[.25em] text-white/30">SEDANG<br />BERLANGSUNG</div>
                        </div>
                        <div class="w-px bg-white/10"></div>
                        <div class="flex items-center gap-3">
                            <div class="font-display text-2xl font-bold text-white">{{ departemen.proker_selesai }}</div>
                            <div class="font-mono text-[9px] tracking-[.25em] text-white/30">TELAH<br />SELESAI</div>
                        </div>
                    </div>
                </template>
            </PageHero>

            <!-- Tujuan -->
            <section class="relative py-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="glass rounded-2xl p-6 md:p-8">
                        <div class="mb-4 font-mono text-[10px] tracking-[.3em] text-cyan-300">01 / TUJUAN DEPARTEMEN</div>
                        <h2 class="font-display text-2xl font-bold text-white">Apa yang diperjuangkan {{ departemen.nama }}?</h2>
                        <div v-if="tujuanList.length" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div v-for="(t, i) in tujuanList" :key="i" class="flex gap-4 rounded-2xl border border-white/10 bg-white/[0.025] p-5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-cyan-300 font-bold text-black">{{ i + 1 }}</span>
                                <p class="pt-1.5 text-sm leading-relaxed text-white/50">{{ t }}</p>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-white/30">Tujuan departemen ini belum diisi pengurus. Hubungi sekretariat untuk info lebih lanjut.</p>
                    </div>
                </div>
            </section>

            <!-- Proker -->
            <section class="relative pb-20">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-6 flex items-end justify-between">
                        <div>
                            <div class="mb-3 font-mono text-[10px] tracking-[.3em] text-cyan-300">02 / PROGRAM KERJA</div>
                            <h2 class="font-display text-2xl font-bold text-white">Proker {{ departemen.nama }}</h2>
                        </div>
                        <Link href="/program-kerja" class="hidden text-sm font-semibold text-cyan-300 hover:text-white sm:inline-block">Semua program →</Link>
                    </div>

                    <div v-if="prokers.length" class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="program in prokers"
                            :key="program.id"
                            class="group rounded-2xl border border-white/10 bg-white/[0.025] p-6 transition-all duration-300 hover:-translate-y-1 hover:border-cyan-300/20"
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
                                <p v-if="program.lokasi">📍 {{ program.lokasi }}</p>
                                <p v-if="program.tanggal_mulai">🗓️ {{ program.tanggal_mulai }}<span v-if="program.tanggal_selesai"> — {{ program.tanggal_selesai }}</span></p>
                                <p v-if="program.penanggung_jawab">👤 {{ program.penanggung_jawab }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="rounded-2xl border border-white/10 bg-white/[0.025] py-16 text-center">
                        <div class="mb-4 text-5xl">📋</div>
                        <h3 class="font-display font-bold text-white">Belum ada proker</h3>
                        <p class="mt-2 text-sm text-white/30">Departemen ini belum mempublikasikan program kerja.</p>
                    </div>

                    <!-- Departemen lainnya -->
                    <div v-if="lainnya.length" class="mt-12">
                        <h3 class="mb-4 font-mono text-[10px] tracking-[.3em] text-white/30">JELAJAHI DEPARTEMEN LAINNYA</h3>
                        <div class="flex flex-wrap gap-3">
                            <Link
                                v-for="d in lainnya"
                                :key="d.id"
                                :href="`/departemen/${d.id}`"
                                class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.025] px-4 py-2.5 text-sm font-medium text-white/60 transition-all hover:border-cyan-300/30 hover:text-cyan-300"
                            >
                                <span class="text-lg">{{ d.icon }}</span> {{ d.nama }}
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
