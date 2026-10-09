<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, ref, watch } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const props = defineProps({
    transaksis: Object,
    filters: Object,
    ringkasan: Object,
    kategoriMasuk: Array,
    kategoriKeluar: Array,
    user: Object,
})

const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID')
const tanggalIndo = (iso) => {
    if (!iso) return '-'
    const d = new Date(String(iso).substring(0, 10) + 'T00:00:00')
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const filters = reactive({
    search: props.filters.search || '',
    tipe: props.filters.tipe || '',
    kategori: props.filters.kategori || '',
    bulan: props.filters.bulan || '',
    dari: props.filters.dari || '',
    sampai: props.filters.sampai || '',
})

const semuaKategori = computed(() => [...props.kategoriMasuk, ...props.kategoriKeluar])

const search = () => {
    const params = new URLSearchParams()
    for (const [k, v] of Object.entries(filters)) {
        if (v) params.append(k, v)
    }
    router.visit('/keuangan' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}

let debounceTimer = null
watch(() => filters.search, () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(search, 300)
})

const reset = () => {
    Object.keys(filters).forEach((k) => (filters[k] = ''))
    search()
}

const rekapHref = computed(() => {
    const params = new URLSearchParams()
    for (const [k, v] of Object.entries(filters)) {
        if (v && k !== 'search' && k !== 'kategori') params.append(k, v)
    }
    if (filters.kategori) params.append('kategori', filters.kategori)
    return '/keuangan/rekap' + (params.toString() ? '?' + params.toString() : '')
})

const deleteTarget = ref(null)
const confirmOpen = ref(false)
const deleting = ref(false)

const askDelete = (item) => {
    deleteTarget.value = item
    confirmOpen.value = true
}

const confirmDelete = () => {
    if (!deleteTarget.value) return
    deleting.value = true
    router.delete(`/keuangan/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}
</script>

<template>
    <Head title="Keuangan" />

    <Layout
        title="Keuangan Bendahara"
        subtitle="Uang masuk, uang keluar, dan sisa saldo"
        eyebrow="SAINTEK.OS // KEUANGAN"
        activeMenu="keuangan"
        :user="user"
        backHref="/dashboard"
    >
     
        
        <!-- Ringkasan -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="dash-card border-emerald-300/20 p-5">
                <p class="font-mono text-[10px] tracking-[.3em] text-emerald-300">SALDO KAS</p>
                <p class="font-display mt-1 text-2xl font-bold text-white">{{ rupiah(ringkasan.saldo) }}</p>
                <p class="mt-1 text-xs text-white/35">Masuk {{ rupiah(ringkasan.total_masuk) }} − Keluar {{ rupiah(ringkasan.total_keluar) }}</p>
            </div>
            <div class="dash-card p-5">
                <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">MASUK (SESUAI FILTER)</p>
                <p class="font-display mt-1 text-2xl font-bold text-emerald-300">+ {{ rupiah(ringkasan.masuk) }}</p>
            </div>
            <div class="dash-card p-5">
                <p class="font-mono text-[10px] tracking-[.3em] text-rose-300">KELUAR (SESUAI FILTER)</p>
                <p class="font-display mt-1 text-2xl font-bold text-rose-300">− {{ rupiah(ringkasan.keluar) }}</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                <div class="md:col-span-2 flex items-center gap-4 ">
                    <Link :href="rekapHref" class="dash-btn-ghost dash-input">Rekap & Cetak</Link>
                    <Link href="/keuangan/create" class="dash-btn-primary">+ Catat Transaksi</Link>
                </div>
                
                <select v-model="filters.tipe" @change="search" class="dash-input cursor-pointer">
                    <option value="">Masuk + Keluar</option>
                    <option value="masuk">Uang Masuk</option>
                    <option value="keluar">Uang Keluar</option>
                </select>
                <select v-model="filters.kategori" @change="search" class="dash-input cursor-pointer">
                    <option value="">Semua Kategori</option>
                    <option v-for="k in semuaKategori" :key="k" :value="k">{{ k }}</option>
                </select>
                <input v-model="filters.bulan" @change="search" type="month" class="dash-input" title="Filter bulan" />
                <div class="flex gap-2">
                    <button @click="reset" class="dash-btn-ghost flex-1">Reset</button>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-6">
                <div class="md:col-span-2">
                    <label class="dash-label">Cari keterangan / kategori</label>
                    <input v-model="filters.search" type="text" placeholder="Cari keterangan / kategori..." class="dash-input" />
                </div>
                <div class="md:col-span-2">
                    <label class="dash-label">Dari tanggal</label>
                    <input v-model="filters.dari" @change="search" type="date" class="dash-input" />
                </div>
                <div class="md:col-span-2">
                    <label class="dash-label">Sampai tanggal</label>
                    <input v-model="filters.sampai" @change="search" type="date" class="dash-input" />
                </div>
                
            </div>
        </div>

        <!-- Tabel -->
        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Pencatat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in transaksis.data" :key="t.id">
                            <td class="whitespace-nowrap font-mono text-xs">{{ tanggalIndo(t.tanggal) }}</td>
                            <td>
                                <p class="font-medium text-white">{{ t.keterangan || t.kategori }}</p>
                                <p class="text-xs text-white/35">{{ t.kategori }}</p>
                            </td>
                            <td>
                                <span v-if="t.tipe === 'masuk'" class="dash-badge bg-emerald-300/10 text-emerald-300">+ Masuk</span>
                                <span v-else class="dash-badge bg-rose-300/10 text-rose-300">− Keluar</span>
                            </td>
                            <td class="whitespace-nowrap font-mono" :class="t.tipe === 'masuk' ? 'text-emerald-300' : 'text-rose-300'">
                                {{ t.tipe === 'masuk' ? '+' : '−' }} {{ rupiah(t.jumlah) }}
                            </td>
                            <td class="text-xs text-white/50">{{ t.pencatat?.name || '-' }}</td>
                            <td>
                                <div class="flex items-center gap-1 whitespace-nowrap">
                                    <Link :href="`/keuangan/${t.id}/edit`" class="dash-link-edit">Edit</Link>
                                    <button @click="askDelete(t)" class="dash-link-delete">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="transaksis.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-white/30">Belum ada transaksi. Klik “Catat Transaksi” untuk mulai.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="transaksis.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 px-6 py-4">
                <p class="text-sm text-white/35">Menampilkan {{ transaksis.from }} - {{ transaksis.to }} dari {{ transaksis.total }} transaksi</p>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="page in transaksis.last_page"
                        :key="page"
                        :href="transaksis.path + '?page=' + page"
                        :class="['dash-page-btn', { active: page === transaksis.current_page }]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="confirmOpen"
            title="Hapus transaksi?"
            message="Saldo kas akan berubah. Data yang dihapus tidak bisa dikembalikan."
            :itemName="deleteTarget ? `${deleteTarget.kategori} — ${rupiah(deleteTarget.jumlah)}` : ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDelete"
        />
    </Layout>
</template>
