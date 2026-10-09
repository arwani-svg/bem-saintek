<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import Sparkline from '@/components/dashboard/Sparkline.vue'
import GreetingBanner from '@/components/dashboard/GreetingBanner.vue'
import DonutChart from '@/components/dashboard/DonutChart.vue'
import ProdiBarChart from '@/components/dashboard/ProdiBarChart.vue'
import HorizontalBarChart from '@/components/dashboard/HorizontalBarChart.vue'

const props = defineProps({
    user: Object,
    stats: Object,
    trends: Object,
    trendLabels: Array,
    mahasiswaPerProdi: Array,
    statusDistribusi: Array,
    prokerPerDepartemen: Array,
})

const spark = (key, fallback = []) => props.trends?.[key] ?? fallback

const deltaPct = (arr) => {
    if (!arr || arr.length < 2) return { text: '0.0%', up: true }
    const first = arr[0]
    const last = arr[arr.length - 1]
    let pct = 0
    if (first > 0) pct = ((last - first) / first) * 100
    else if (last > 0) pct = 100
    return { text: `${Math.abs(pct).toFixed(1)}%`, up: pct >= 0 }
}

const statCards = computed(() => [
    {
        label: 'Total Mahasiswa',
        value: props.stats.total_mahasiswa ?? 0,
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />',
        iconBox: 'bg-violet-500/10 text-violet-400',
        trend: spark('mahasiswa'),
        color: '#8b5cf6',
    },
    {
        label: 'Total Pengurus',
        value: props.stats.total_pengurus ?? 0,
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
        iconBox: 'bg-cyan-500/10 text-cyan-400',
        trend: spark('pengurus'),
        color: '#22d3ee',
    },
    {
        label: 'Total Departemen',
        value: props.stats.total_departemen ?? 0,
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
        iconBox: 'bg-emerald-500/10 text-emerald-400',
        trend: spark('departemen'),
        color: '#34d399',
    },
    {
        label: 'Total Prodi',
        value: props.stats.total_prodi ?? 0,
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
        iconBox: 'bg-orange-500/10 text-orange-400',
        trend: spark('prodi'),
        color: '#fb923c',
    },
])

const quickActions = [
    {
        href: '/users',
        title: 'Manajemen User',
        desc: 'Kelola data mahasiswa dan pengurus',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
        glow: 'bg-cyan-300/10 text-cyan-300',
        hover: 'hover:border-cyan-300/30',
    },
    {
        href: '/users/create',
        title: 'Tambah User',
        desc: 'Buat akun mahasiswa atau pengurus',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />',
        glow: 'bg-emerald-300/10 text-emerald-300',
        hover: 'hover:border-emerald-300/30',
    },
    {
        href: '/departemen',
        title: 'Departemen',
        desc: 'Kelola departemen BEM SAINTEK',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
        glow: 'bg-indigo-300/10 text-indigo-300',
        hover: 'hover:border-indigo-300/30',
    },
    {
        href: '/proker/create',
        title: 'Tambah Proker',
        desc: 'Buat program kerja baru departemen',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
        glow: 'bg-orange-300/10 text-orange-300',
        hover: 'hover:border-orange-300/30',
    },
    {
        href: '/informasi',
        title: 'Informasi',
        desc: 'Kelola berita, lomba & lainnya',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />',
        glow: 'bg-pink-300/10 text-pink-300',
        hover: 'hover:border-pink-300/30',
    },
    {
        href: '/informasi/create',
        title: 'Tambah Informasi',
        desc: 'Publikasikan kabar terbaru',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />',
        glow: 'bg-violet-300/10 text-violet-300',
        hover: 'hover:border-violet-300/30',
    },
]
</script>

<template>
    <Head title="Dashboard" />

    <Layout
        title="Mission Control"
        :subtitle="`Selamat datang kembali, ${user.name}`"
        eyebrow="SAINTEK.OS // DASHBOARD"
        activeMenu="dashboard"
        :user="user"
        :showBack="false"
    >
        <GreetingBanner :userName="user.name" />

        <!-- STAT CARDS (desain/dashboard.html) -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div
                v-for="stat in statCards"
                :key="stat.label"
                class="dash-card p-5 transition-all duration-300 hover:-translate-y-1 hover:border-violet-500/30"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="mb-2 text-xs text-white/40">{{ stat.label }}</p>
                        <h3 class="text-2xl font-bold text-white">{{ stat.value }}</h3>
                        <div class="mt-2 flex items-center gap-1.5">
                            <span
                                class="text-xs font-medium"
                                :class="deltaPct(stat.trend).up ? 'text-emerald-400' : 'text-rose-400'"
                            >
                                {{ deltaPct(stat.trend).up ? '↑' : '↓' }} {{ deltaPct(stat.trend).text }}
                            </span>
                            <span class="text-[11px] text-white/25">vs last month</span>
                        </div>
                    </div>
                    <div :class="[stat.iconBox, 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl']">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="stat.icon" />
                    </div>
                </div>
                <div class="mt-4 h-14">
                    <Sparkline :data="stat.trend" :stroke="stat.color" :width="220" :height="56" />
                </div>
            </div>
        </div>

        <!-- CHARTS: prodi (vertical bar) + status (donut) -->
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="dash-card mt-5 p-5 xl:col-span-2">
                <div class="mb-5">
                    <h3 class="font-bold text-white">Mahasiswa per Prodi</h3>
                    <p class="mt-1 text-xs text-white/35">Perbandingan angka antar prodi.</p>
                </div>
                <ProdiBarChart :items="mahasiswaPerProdi ?? []" />
            </div>

            <div class="dash-card mt-5 p-5">
                <div class="mb-5">
                    <h3 class="font-bold text-white">Status Mahasiswa</h3>
                    <p class="mt-1 text-xs text-white/35">Distribusi status mahasiswa.</p>
                </div>
                <DonutChart :segments="statusDistribusi ?? []" center-label="Mahasiswa" />
            </div>
        </div>

        <!-- LOWER: proker per departemen (horizontal bar) + aksi cepat -->
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="dash-card mt-5 p-5 xl:col-span-2">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-white">Program Kerja per Departemen</h3>
                        <p class="mt-1 text-xs text-white/35">Jumlah proker tiap departemen.</p>
                    </div>
                    <p v-if="trendLabels?.length" class="font-mono text-[10px] tracking-widest text-white/25">
                        {{ trendLabels[0] }} — {{ trendLabels[trendLabels.length - 1] }}
                    </p>
                </div>
                <HorizontalBarChart :items="prokerPerDepartemen ?? []" />
            </div>

            <div class="dash-card mt-5 p-5">
                <div class="mb-5">
                    <h3 class="font-bold text-white">Aksi Cepat</h3>
                    <p class="mt-1 text-xs text-white/35">Jalan pintas ke halaman yang paling sering dipakai.</p>
                </div>
                <div class="grid grid-cols-1 gap-3">
                    <Link
                        v-for="action in quickActions"
                        :key="action.href + action.title"
                        :href="action.href"
                        :class="[action.hover, 'group flex items-center gap-4 rounded-xl border border-white/10 bg-white/[0.02] p-3.5 transition-all hover:bg-white/[0.04]']"
                    >
                        <div :class="[action.glow, 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl']">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="action.icon" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white transition-colors group-hover:text-cyan-300">{{ action.title }}</p>
                            <p class="truncate text-xs text-white/35">{{ action.desc }}</p>
                        </div>
                        <span class="ml-auto shrink-0 text-white/20 transition-all group-hover:translate-x-1 group-hover:text-cyan-300">→</span>
                    </Link>
                </div>
            </div>
        </div>
    </Layout>
</template>
