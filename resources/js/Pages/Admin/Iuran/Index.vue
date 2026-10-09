<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const props = defineProps({
    iurans: Object,
    filters: Object,
    statusList: Array,
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
    status: props.filters.status || '',
})

let t = null
const search = () => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    router.visit('/iuran' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}
const onSearch = () => {
    clearTimeout(t)
    t = setTimeout(search, 300)
}
const reset = () => {
    filters.search = ''
    filters.status = ''
    search()
}

const statusBadge = (s) => {
    if (s === 'aktif') return 'bg-emerald-300/10 text-emerald-300'
    if (s === 'selesai') return 'bg-cyan-300/10 text-cyan-300'
    return 'bg-rose-300/10 text-rose-300'
}

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
    router.delete(`/iuran/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}
</script>

<template>
    <Head title="Manajemen Iuran" />

    <Layout
        title="Manajemen Iuran"
        subtitle="Tagihan kas per anggota — siapa wajib bayar, siapa lunas, siapa nunggak"
        eyebrow="SAINTEK.OS // KEUANGAN"
        activeMenu="iuran"
        :user="user"
        backHref="/dashboard"
    >
        <!-- Filter -->
        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label class="dash-label">Cari iuran</label>
                    <input v-model="filters.search" @input="onSearch" type="text" placeholder="cth: Kas Oktober, Bazar..." class="dash-input" />
                </div>
                <div>
                    <label class="dash-label">Status</label>
                    <select v-model="filters.status" @change="search" class="dash-input cursor-pointer">
                        <option value="">Semua status</option>
                        <option v-for="s in statusList" :key="s" :value="s" class="capitalize">{{ s }}</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button @click="reset" class="dash-btn-ghost flex-1">Reset</button>
                    <Link href="/iuran/create" class="dash-btn-primary flex-1 text-center">+ Buat Iuran</Link>
                </div>
            </div>
        </div>

        <!-- List -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div v-for="i in iurans.data" :key="i.id" class="dash-card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <Link :href="`/iuran/${i.id}`" class="font-display text-lg font-bold text-white hover:text-cyan-300">
                            {{ i.nama }}
                        </Link>
                        <p class="mt-0.5 text-xs text-white/40">
                            {{ i.pesertas_count }} peserta · {{ rupiah(i.nominal) }}/orang
                            <span v-if="i.jatuh_tempo"> · jatuh tempo {{ tanggalIndo(i.jatuh_tempo) }}</span>
                        </p>
                    </div>
                    <span :class="['dash-badge capitalize', statusBadge(i.status)]">{{ i.status }}</span>
                </div>

                <!-- Progress -->
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-mono text-emerald-300">{{ rupiah(i.terkumpul) }} terkumpul</span>
                        <span class="font-mono text-white/40">{{ i.progress }}% · sisa {{ rupiah(i.sisa) }}</span>
                    </div>
                    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-white/5">
                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-cyan-300 transition-all" :style="{ width: Math.min(100, i.progress) + '%' }"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-white/30">Target {{ rupiah(i.target) }}</p>
                </div>

                <div class="mt-4 flex items-center gap-2 border-t border-white/5 pt-4">
                    <Link :href="`/iuran/${i.id}`" class="dash-btn-ghost flex-1 text-center">Kelola Tagihan</Link>
                    <Link :href="`/iuran/${i.id}/edit`" class="dash-link-edit">Edit</Link>
                    <button @click="askDelete(i)" class="dash-link-delete">Hapus</button>
                </div>
            </div>
        </div>

        <div v-if="iurans.data.length === 0" class="dash-card mt-4 p-12 text-center text-white/30">
            Belum ada iuran. Klik “Buat Iuran” untuk tagihan kas pertama.
        </div>

        <div v-if="iurans.last_page > 1" class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-white/35">Menampilkan {{ iurans.from }} - {{ iurans.to }} dari {{ iurans.total }} iuran</p>
            <div class="flex items-center gap-1">
                <Link
                    v-for="page in iurans.last_page"
                    :key="page"
                    :href="`/iuran?page=${page}`"
                    :class="['dash-page-btn', { active: page === iurans.current_page }]"
                >
                    {{ page }}
                </Link>
            </div>
        </div>

        <ConfirmModal
            :show="confirmOpen"
            title="Hapus iuran?"
            message="Semua tagihan peserta + catatan kas dari iuran ini ikut terhapus. Tidak bisa dikembalikan."
            :itemName="deleteTarget ? deleteTarget.nama : ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDelete"
        />
    </Layout>
</template>
