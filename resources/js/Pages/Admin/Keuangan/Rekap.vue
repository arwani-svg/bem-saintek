<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'

const props = defineProps({
    items: Array,
    perKategori: Array,
    filters: Object,
    ringkasan: Object,
    user: Object,
})

const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID')
const tanggalIndo = (iso) => {
    if (!iso) return '-'
    const d = new Date(String(iso).substring(0, 10) + 'T00:00:00')
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
const hariIni = () => new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })

const filters = reactive({
    tipe: props.filters.tipe || '',
    bulan: props.filters.bulan || '',
    dari: props.filters.dari || '',
    sampai: props.filters.sampai || '',
})

const terapkan = () => {
    const params = new URLSearchParams()
    for (const [k, v] of Object.entries(filters)) {
        if (v) params.append(k, v)
    }
    router.visit('/keuangan/rekap' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}

const reset = () => {
    Object.keys(filters).forEach((k) => (filters[k] = ''))
    terapkan()
}

const cetak = () => window.print()
</script>

<template>
    <Head title="Rekap Keuangan" />

    <Layout
        title="Rekap Keuangan"
        subtitle="Ringkasan per kategori + rincian, siap cetak"
        eyebrow="SAINTEK.OS // KEUANGAN"
        activeMenu="keuangan-rekap"
        :user="user"
        backHref="/keuangan"
    >
        <!-- ===== Tampilan layar ===== -->
        <div class="print:hidden">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm text-white/40">Periode: <span class="font-semibold text-white">{{ ringkasan.periode }}</span></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link href="/keuangan" class="dash-btn-ghost">← Transaksi</Link>
                    <button @click="cetak" class="dash-btn-primary">Cetak Rekap</button>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="dash-card p-5">
                    <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">TOTAL MASUK</p>
                    <p class="font-display mt-1 text-2xl font-bold text-emerald-300">+ {{ rupiah(ringkasan.masuk) }}</p>
                </div>
                <div class="dash-card p-5">
                    <p class="font-mono text-[10px] tracking-[.3em] text-rose-300">TOTAL KELUAR</p>
                    <p class="font-display mt-1 text-2xl font-bold text-rose-300">− {{ rupiah(ringkasan.keluar) }}</p>
                </div>
                <div class="dash-card p-5">
                    <p class="font-mono text-[10px] tracking-[.3em] text-amber-300">SELISIH PERIODE</p>
                    <p class="font-display mt-1 text-2xl font-bold text-white">{{ rupiah(ringkasan.selisih) }}</p>
                </div>
            </div>

            <form @submit.prevent="terapkan" class="dash-card no-print mb-6 p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                    <select v-model="filters.tipe" class="dash-input cursor-pointer">
                        <option value="">Masuk + Keluar</option>
                        <option value="masuk">Uang Masuk</option>
                        <option value="keluar">Uang Keluar</option>
                    </select>
                    <input v-model="filters.bulan" type="month" class="dash-input" title="Filter bulan" />
                    <input v-model="filters.dari" type="date" class="dash-input" title="Dari tanggal" />
                    <input v-model="filters.sampai" type="date" class="dash-input" title="Sampai tanggal" />
                    <div class="flex gap-2">
                        <button type="submit" class="dash-btn-primary flex-1">Terapkan</button>
                        <button type="button" @click="reset" class="dash-btn-ghost">Reset</button>
                    </div>
                </div>
            </form>

            <div class="dash-card mb-6 overflow-hidden">
                <div class="border-b border-white/5 p-5">
                    <h3 class="font-display text-lg font-bold text-white">Ringkasan per Kategori</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Tipe</th>
                                <th>Kategori</th>
                                <th class="text-center">Transaksi</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in perKategori" :key="row.tipe + row.kategori">
                                <td>
                                    <span v-if="row.tipe === 'masuk'" class="dash-badge bg-emerald-300/10 text-emerald-300">Masuk</span>
                                    <span v-else class="dash-badge bg-rose-300/10 text-rose-300">Keluar</span>
                                </td>
                                <td class="text-white">{{ row.kategori }}</td>
                                <td class="text-center font-mono">{{ row.transaksi }}x</td>
                                <td class="text-right font-mono text-white">{{ rupiah(row.total) }}</td>
                            </tr>
                            <tr v-if="perKategori.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-white/30">Tidak ada data pada periode ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dash-card overflow-hidden">
                <div class="border-b border-white/5 p-5">
                    <h3 class="font-display text-lg font-bold text-white">Rincian Transaksi ({{ items.length }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Tipe</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="t in items" :key="t.id">
                                <td class="whitespace-nowrap font-mono text-xs">{{ tanggalIndo(t.tanggal) }}</td>
                                <td>
                                    <p class="text-white">{{ t.keterangan || t.kategori }}</p>
                                    <p class="text-xs text-white/35">{{ t.kategori }}</p>
                                </td>
                                <td class="capitalize text-white/60">{{ t.tipe }}</td>
                                <td class="whitespace-nowrap text-right font-mono" :class="t.tipe === 'masuk' ? 'text-emerald-300' : 'text-rose-300'">
                                    {{ t.tipe === 'masuk' ? '+' : '−' }} {{ rupiah(t.jumlah) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===== Versi cetak (terang, rapi di kertas) ===== -->
        <div class="hidden print:block">
            <div class="bg-white p-8 text-black">
                <div class="border-b-2 border-black pb-4 text-center">
                    <h1 class="text-xl font-bold">REKAP KEUANGAN BEM SAINTEK</h1>
                    <p class="text-sm">Periode: {{ ringkasan.periode }}</p>
                </div>

                <table class="mt-4 w-full text-sm">
                    <tbody>
                        <tr><td class="py-1">Total Uang Masuk</td><td class="py-1 text-right font-bold">Rp {{ Number(ringkasan.masuk).toLocaleString('id-ID') }}</td></tr>
                        <tr><td class="py-1">Total Uang Keluar</td><td class="py-1 text-right font-bold">Rp {{ Number(ringkasan.keluar).toLocaleString('id-ID') }}</td></tr>
                        <tr class="border-t border-black"><td class="py-1 font-bold">Selisih Periode</td><td class="py-1 text-right font-bold">Rp {{ Number(ringkasan.selisih).toLocaleString('id-ID') }}</td></tr>
                    </tbody>
                </table>

                <h2 class="mb-2 mt-6 font-bold">Ringkasan per Kategori</h2>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-black px-2 py-1 text-left">Tipe</th>
                            <th class="border border-black px-2 py-1 text-left">Kategori</th>
                            <th class="border border-black px-2 py-1">Transaksi</th>
                            <th class="border border-black px-2 py-1 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in perKategori" :key="'c' + row.tipe + row.kategori">
                            <td class="border border-black px-2 py-1 capitalize">{{ row.tipe }}</td>
                            <td class="border border-black px-2 py-1">{{ row.kategori }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ row.transaksi }}x</td>
                            <td class="border border-black px-2 py-1 text-right">Rp {{ Number(row.total).toLocaleString('id-ID') }}</td>
                        </tr>
                    </tbody>
                </table>

                <h2 class="mb-2 mt-6 font-bold">Rincian Transaksi</h2>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-black px-2 py-1 text-left">Tanggal</th>
                            <th class="border border-black px-2 py-1 text-left">Keterangan</th>
                            <th class="border border-black px-2 py-1 text-left">Kategori</th>
                            <th class="border border-black px-2 py-1 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in items" :key="'p' + t.id">
                            <td class="border border-black px-2 py-1">{{ tanggalIndo(t.tanggal) }}</td>
                            <td class="border border-black px-2 py-1">{{ t.keterangan || '-' }}</td>
                            <td class="border border-black px-2 py-1">{{ t.kategori }} ({{ t.tipe }})</td>
                            <td class="border border-black px-2 py-1 text-right">Rp {{ Number(t.jumlah).toLocaleString('id-ID') }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-8 flex justify-end">
                    <div class="text-center text-sm">
                        <p>Dicetak pada {{ hariIni() }}</p>
                        <br /><br /><br />
                        <p class="font-bold underline">Bendahara BEM Saintek</p>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
@media print {
    @page {
        size: A4;
        margin: 10mm;
    }
}
</style>
