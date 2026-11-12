<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
// import { router } from '@inertiajs/vue3'
import Layout from '@/components/dashboard/Layout.vue'
import Sparkline from '@/components/dashboard/Sparkline.vue'
import GreetingBanner from '@/components/dashboard/GreetingBanner.vue'
import DonutChart from '@/components/dashboard/DonutChart.vue'
import ProdiBarChart from '@/components/dashboard/ProdiBarChart.vue'
import HorizontalBarChart from '@/components/dashboard/HorizontalBarChart.vue'
import FinancialChart from '@/components/dashboard/FinancialChart.vue'


const props = defineProps({
    user: Object,
    stats: Object,
    trends: Object,
    trendLabels: Array,
    mahasiswaPerProdi: Array,
    statusDistribusi: Array,
    prokerPerDepartemen: Array,
    keuangan: {
    type: Object,
    default: null,
},
})

const changeKeuanganFilter = (key, value) => {
    if (!props.keuangan) return

    const params = {
        mode: props.keuangan.mode,
        tahun: props.keuangan.tahun,
        [key]: value,
    }

    router.get('/dashboard', params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const formatMoney = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value || 0)
}

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
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4 xl:gap-5">
            <div
                v-for="stat in statCards"
                :key="stat.label"
                class="dash-card p-4 transition-all duration-300 hover:-translate-y-1 hover:border-violet-500/30 xl:p-5"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="mb-1.5 text-[11px] text-white/40 xl:mb-2 xl:text-xs">{{ stat.label }}</p>
                        <h3 class="text-xl font-bold text-white xl:text-2xl">{{ stat.value }}</h3>
                        <div class="mt-1.5 flex items-center gap-1.5 xl:mt-2">
                            <span
                                class="text-[11px] font-medium xl:text-xs"
                                :class="deltaPct(stat.trend).up ? 'text-emerald-400' : 'text-rose-400'"
                            >
                                {{ deltaPct(stat.trend).up ? '↑' : '↓' }} {{ deltaPct(stat.trend).text }}
                            </span>
                            <span class="text-[10px] text-white/25 xl:text-[11px]">vs last month</span>
                        </div>
                    </div>
                    <div :class="[stat.iconBox, 'flex h-9 w-9 shrink-0 items-center justify-center rounded-xl xl:h-10 xl:w-10']">
                        <svg class="h-4 w-4 xl:h-5 xl:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="stat.icon" />
                    </div>
                </div>
                <div class="mt-3 h-12 xl:mt-4 xl:h-14">
                    <Sparkline :data="stat.trend" :stroke="stat.color" :width="200" :height="48" class="xl:hidden" />
                    <Sparkline :data="stat.trend" :stroke="stat.color" :width="220" :height="56" class="hidden xl:block" />
                </div>
            </div>
        </div>


        
<!-- FINANCIAL STATISTICS -->
<section v-if="keuangan" class="mt-4 space-y-4 xl:mt-5">

    <!-- Header + Filter -->
    <div class="dash-card p-4 xl:p-5">

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_10px_#34d399]"></span>
                    <h3 class="text-sm font-bold text-white xl:text-base">
                        Statistik Keuangan
                    </h3>
                </div>

                <p class="mt-1 text-[11px] text-white/35 xl:text-xs">
                    Monitoring pemasukan dan pengeluaran organisasi.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                <!-- Mode -->
                <select
                    :value="keuangan.mode"
                    @change="changeKeuanganFilter('mode', $event.target.value)"
                    class="min-w-[125px] rounded-xl border border-white/10 bg-[#171725] px-3 py-2 text-xs text-white outline-none transition focus:border-violet-400/50"
                >
                    <option value="bulanan">Bulanan</option>
                    <option value="tahunan">Tahunan</option>
                </select>

                <!-- Tahun: hanya untuk mode bulanan -->
                <select
                    v-if="keuangan.mode === 'bulanan'"
                    :value="keuangan.tahun"
                    @change="changeKeuanganFilter('tahun', $event.target.value)"
                    class="min-w-[100px] rounded-xl border border-white/10 bg-[#171725] px-3 py-2 text-xs text-white outline-none transition focus:border-violet-400/50"
                >
                    <option
                        v-for="tahun in keuangan.tahunTersedia"
                        :key="tahun"
                        :value="tahun"
                    >
                        {{ tahun }}
                    </option>
                </select>

            </div>
        </div>

        <!-- Summary Cards -->
        <div class="mt-5 grid grid-cols-2 gap-3 xl:grid-cols-4">

            <div class="rounded-xl border border-emerald-400/15 bg-emerald-400/[0.05] p-3 xl:p-4">
                <p class="text-[10px] text-emerald-300/60 xl:text-xs">
                    Total Pemasukan
                </p>
                <h4 class="mt-2 break-words text-sm font-bold text-emerald-400 xl:text-lg">
                    {{ formatMoney(keuangan.ringkasan.masuk) }}
                </h4>
            </div>

            <div class="rounded-xl border border-rose-400/15 bg-rose-400/[0.05] p-3 xl:p-4">
                <p class="text-[10px] text-rose-300/60 xl:text-xs">
                    Total Pengeluaran
                </p>
                <h4 class="mt-2 break-words text-sm font-bold text-rose-400 xl:text-lg">
                    {{ formatMoney(keuangan.ringkasan.keluar) }}
                </h4>
            </div>

            <div class="rounded-xl border border-cyan-400/15 bg-cyan-400/[0.05] p-3 xl:p-4">
                <p class="text-[10px] text-cyan-300/60 xl:text-xs">
                    Selisih Periode
                </p>
                <h4
                    class="mt-2 break-words text-sm font-bold xl:text-lg"
                    :class="keuangan.ringkasan.selisih >= 0 ? 'text-cyan-400' : 'text-rose-400'"
                >
                    {{ formatMoney(keuangan.ringkasan.selisih) }}
                </h4>
            </div>

            <div class="rounded-xl border border-violet-400/15 bg-violet-400/[0.05] p-3 xl:p-4">
                <p class="text-[10px] text-violet-300/60 xl:text-xs">
                    Saldo Keseluruhan
                </p>
                <h4
                    class="mt-2 break-words text-sm font-bold xl:text-lg"
                    :class="keuangan.ringkasan.saldo >= 0 ? 'text-violet-400' : 'text-rose-400'"
                >
                    {{ formatMoney(keuangan.ringkasan.saldo) }}
                </h4>
            </div>

        </div>

        <!-- Financial Chart -->
        <div class="mt-6">

            <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h4 class="text-xs font-bold text-white xl:text-sm">
                        Perbandingan Pemasukan & Pengeluaran
                    </h4>

                    <p class="mt-1 text-[10px] text-white/30">
                        {{ keuangan.mode === 'bulanan'
                            ? `Statistik bulanan tahun ${keuangan.tahun}`
                            : 'Statistik tahunan seluruh transaksi'
                        }}
                    </p>
                </div>

                <span class="rounded-lg border border-white/10 bg-white/[0.03] px-2.5 py-1 font-mono text-[10px] text-white/40">
                    {{ keuangan.mode === 'bulanan' ? '12 BULAN' : 'TAHUNAN' }}
                </span>
            </div>

            <FinancialChart :items="keuangan.chartData" />

        </div>

    </div>

</section>

        <!-- CHARTS: prodi (vertical bar) + status (donut) -->
        <div class="mt-4 grid grid-cols-1 gap-3 xl:mt-5 xl:grid-cols-3 xl:gap-5">
            <div class="dash-card p-4 xl:col-span-2 xl:p-5">
                <div class="mb-4 xl:mb-5">
                    <h3 class="text-sm font-bold text-white xl:text-base">Mahasiswa per Prodi</h3>
                    <p class="mt-1 text-[11px] text-white/35 xl:text-xs">Perbandingan angka antar prodi.</p>
                </div>
                <ProdiBarChart :items="mahasiswaPerProdi ?? []" />
            </div>

            <div class="dash-card p-4 xl:p-5">
                <div class="mb-4 xl:mb-5">
                    <h3 class="text-sm font-bold text-white xl:text-base">Status Mahasiswa</h3>
                    <p class="mt-1 text-[11px] text-white/35 xl:text-xs">Distribusi status mahasiswa.</p>
                </div>
                <DonutChart :segments="statusDistribusi ?? []" center-label="Mahasiswa" />
            </div>
        </div>

        <!-- LOWER: proker per departemen (horizontal bar) + aksi cepat -->
        <div class="mt-4 grid grid-cols-1 gap-3 xl:mt-5 xl:grid-cols-3 xl:gap-5">
            <div class="dash-card p-4 xl:col-span-2 xl:p-5">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2 xl:mb-5">
                    <div>
                        <h3 class="text-sm font-bold text-white xl:text-base">Program Kerja per Departemen</h3>
                        <p class="mt-1 text-[11px] text-white/35 xl:text-xs">Jumlah proker tiap departemen.</p>
                    </div>
                    <p v-if="trendLabels?.length" class="font-mono text-[9px] tracking-widest text-white/25 xl:text-[10px]">
                        {{ trendLabels[0] }} — {{ trendLabels[trendLabels.length - 1] }}
                    </p>
                </div>
                <HorizontalBarChart :items="prokerPerDepartemen ?? []" />
            </div>

            <div class="dash-card p-4 xl:p-5">
                <div class="mb-4 xl:mb-5">
                    <h3 class="text-sm font-bold text-white xl:text-base">Aksi Cepat</h3>
                    <p class="mt-1 text-[11px] text-white/35 xl:text-xs">Jalan pintas ke halaman yang paling sering dipakai.</p>
                </div>
                <div class="grid grid-cols-1 gap-2.5 xl:gap-3">
                    <Link
                        v-for="action in quickActions"
                        :key="action.href + action.title"
                        :href="action.href"
                        :class="[action.hover, 'group flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.02] p-3 transition-all hover:bg-white/[0.04] xl:gap-4 xl:p-3.5']"
                    >
                        <div :class="[action.glow, 'flex h-9 w-9 shrink-0 items-center justify-center rounded-xl xl:h-11 xl:w-11']">
                            <svg class="h-4 w-4 xl:h-5 xl:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="action.icon" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-white transition-colors group-hover:text-cyan-300 xl:text-sm">{{ action.title }}</p>
                            <p class="truncate text-[11px] text-white/35 xl:text-xs">{{ action.desc }}</p>
                        </div>
                        <span class="ml-auto shrink-0 text-white/20 transition-all group-hover:translate-x-1 group-hover:text-cyan-300">→</span>
                    </Link>
                </div>
            </div>
        </div>
    </Layout>
</template>
