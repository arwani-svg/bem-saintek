<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const props = defineProps({
    kegiatans: Object,
    filters: Object,
    statusList: Array,
    user: Object,
})

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
    router.visit('/kegiatan' + (params.toString() ? '?' + params.toString() : ''), {
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
    if (s === 'berlangsung') return 'bg-emerald-300/10 text-emerald-300'
    if (s === 'terjadwal') return 'bg-cyan-300/10 text-cyan-300'
    if (s === 'selesai') return 'bg-white/10 text-white/60'
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
    router.delete(`/kegiatan/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}
</script>

<template>
    <Head title="Daftar Kegiatan" />

    <Layout
        title="Daftar Kegiatan"
        subtitle="Buat kegiatan, atur peserta, lalu absen cepat pakai keyboard"
        eyebrow="SAINTEK.OS // ABSENSI"
        activeMenu="kegiatan"
        :user="user"
        backHref="/dashboard"
    >
        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label class="dash-label">Cari kegiatan</label>
                    <input v-model="filters.search" @input="onSearch" type="text" placeholder="cth: Rapat rutin, Makrab..." class="dash-input" />
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
                    <Link href="/kegiatan/create" class="dash-btn-primary flex-1 text-center">+ Buat Kegiatan</Link>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div v-for="k in kegiatans.data" :key="k.id" class="dash-card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <Link :href="`/kegiatan/${k.id}`" class="font-display text-lg font-bold text-white hover:text-cyan-300">
                            {{ k.nama }}
                        </Link>
                        <p class="mt-0.5 text-xs text-white/40">
                            {{ tanggalIndo(k.tanggal) }}<span v-if="k.waktu_mulai"> · {{ k.waktu_mulai }}</span><span v-if="k.lokasi"> · {{ k.lokasi }}</span>
                        </p>
                        <p class="mt-0.5 text-xs text-white/40">{{ k.pesertas_count }} peserta · alpa = {{ k.poin_alpa }} poin</p>
                    </div>
                    <span :class="['dash-badge capitalize', statusBadge(k.status)]">{{ k.status }}</span>
                </div>

                <div class="mt-3 flex flex-wrap gap-1.5 text-[11px]">
                    <span class="rounded-full bg-emerald-300/10 px-2 py-0.5 font-mono text-emerald-300">{{ k.hadir }} hadir</span>
                    <span class="rounded-full bg-cyan-300/10 px-2 py-0.5 font-mono text-cyan-300">{{ k.izin }} izin</span>
                    <span class="rounded-full bg-amber-300/10 px-2 py-0.5 font-mono text-amber-300">{{ k.sakit }} sakit</span>
                    <span class="rounded-full bg-rose-300/10 px-2 py-0.5 font-mono text-rose-300">{{ k.alpa }} alpa</span>
                    <span class="rounded-full bg-white/5 px-2 py-0.5 font-mono text-white/40">{{ k.belum }} belum</span>
                </div>

                <div class="mt-4 flex items-center gap-2 border-t border-white/5 pt-4">
                    <Link :href="`/kegiatan/${k.id}/absen`" class="dash-btn-primary flex-1 text-center !py-2 text-xs">⚡ Mulai Absen</Link>
                    <Link :href="`/kegiatan/${k.id}`" class="dash-btn-ghost !px-3 !py-2 text-xs">Detail</Link>
                    <Link :href="`/kegiatan/${k.id}/edit`" class="dash-link-edit">Edit</Link>
                    <button @click="askDelete(k)" class="dash-link-delete">Hapus</button>
                </div>
            </div>
        </div>

        <div v-if="kegiatans.data.length === 0" class="dash-card mt-4 p-12 text-center text-white/30">
            Belum ada kegiatan. Klik “Buat Kegiatan” untuk mulai.
        </div>

        <div v-if="kegiatans.last_page > 1" class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-white/35">Menampilkan {{ kegiatans.from }} - {{ kegiatans.to }} dari {{ kegiatans.total }} kegiatan</p>
            <div class="flex items-center gap-1">
                <Link
                    v-for="page in kegiatans.last_page"
                    :key="page"
                    :href="`/kegiatan?page=${page}`"
                    :class="['dash-page-btn', { active: page === kegiatans.current_page }]"
                >
                    {{ page }}
                </Link>
            </div>
        </div>

        <ConfirmModal
            :show="confirmOpen"
            title="Hapus kegiatan?"
            message="Semua data kehadiran kegiatan ini ikut terhapus. Tidak bisa dikembalikan."
            :itemName="deleteTarget ? deleteTarget.nama : ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDelete"
        />
    </Layout>
</template>
