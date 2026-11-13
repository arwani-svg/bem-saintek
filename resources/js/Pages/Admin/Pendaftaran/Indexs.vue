<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, ref, watch } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const props = defineProps({
    pendaftarans: Object,
    filters: Object,
    prodiList: Array,
    statusList: Array,
    periodeAktif: Object,
    gelombangs: Array,
    initialTab: String,
    user: Object,
})

const tab = ref(props.initialTab === 'periode' ? 'periode' : 'pendaftar')

const filters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    prodi: props.filters.prodi || '',
})

const search = () => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    if (filters.prodi) params.append('prodi', filters.prodi)
    if (tab.value === 'periode') params.append('tab', 'periode')
    router.visit('/admin/pendaftaran' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}

let debounceTimer = null
watch(() => filters.search, () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(search, 300)
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
    router.delete(`/pendaftaran/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}

const deleteGelombang = ref(null)
const confirmGelombangOpen = ref(false)
const deletingGelombang = ref(false)

const askDeleteGelombang = (item) => {
    deleteGelombang.value = item
    confirmGelombangOpen.value = true
}

const confirmDeleteGelombang = () => {
    if (!deleteGelombang.value) return
    deletingGelombang.value = true
    router.delete(`/admin/gelombang/${deleteGelombang.value.id}`, {
        onFinish: () => {
            deletingGelombang.value = false
            confirmGelombangOpen.value = false
            deleteGelombang.value = null
        },
    })
}

const toggleGelombang = (item) => {
    router.patch(`/admin/gelombang/${item.id}/toggle`, {}, { preserveScroll: true })
}

const statusColors = {
    pending: 'bg-amber-300/10 text-amber-300',
    diterima: 'bg-emerald-300/10 text-emerald-300',
    ditolak: 'bg-red-400/10 text-red-300',
}

const periodeColors = {
    Dibuka: 'bg-emerald-300/10 text-emerald-300',
    Ditutup: 'bg-white/5 text-white/40',
    Terjadwal: 'bg-cyan-300/10 text-cyan-300',
    Berakhir: 'bg-amber-300/10 text-amber-300',
}
</script>

<template>
    <Head title="Pendaftar BEM" />

    <Layout
        title="Pendaftar BEM"
        subtitle="Kelola pendaftar & periode pendaftaran"
        eyebrow="SAINTEK.OS // PENDAFTARAN"
        activeMenu="pendaftaran-admin"
        :user="user"
        backHref="/dashboard"
    >
        <!-- TAB -->
        <div class="mb-6 flex gap-2">
            <button
                @click="tab = 'pendaftar'"
                :class="tab === 'pendaftar'
                    ? 'border-cyan-300/40 bg-cyan-300/10 text-white'
                    : 'border-white/10 text-white/40 hover:text-white'"
                class="rounded-xl border px-5 py-2.5 text-sm font-semibold transition"
            >
                Pendaftar
            </button>
            <button
                @click="tab = 'periode'"
                :class="tab === 'periode'
                    ? 'border-cyan-300/40 bg-cyan-300/10 text-white'
                    : 'border-white/10 text-white/40 hover:text-white'"
                class="flex items-center gap-2 rounded-xl border px-5 py-2.5 text-sm font-semibold transition"
            >
                Periode Pendaftaran
                <span v-if="periodeAktif" class="h-2 w-2 rounded-full bg-emerald-300 shadow-[0_0_10px_rgba(110,231,183,0.9)]"></span>
            </button>
        </div>

        <!-- TAB: PENDAFTAR -->
        <div v-if="tab === 'pendaftar'">
            <div class="dash-card mb-6 flex flex-wrap items-center gap-3 p-5">
                <div v-if="periodeAktif" class="flex items-center gap-3">
                    <span class="dash-badge bg-emerald-300/10 text-emerald-300">Dibuka</span>
                    <p class="text-sm text-white/60">{{ periodeAktif.nama }} • {{ periodeAktif.tanggal_mulai }} — {{ periodeAktif.tanggal_selesai }}</p>
                </div>
                <div v-else class="flex items-center gap-3">
                    <span class="dash-badge bg-white/5 text-white/40">Ditutup</span>
                    <p class="text-sm text-white/40">Pendaftaran sedang ditutup. Form publik /join terkunci.</p>
                </div>
            </div>

            <div class="dash-card mb-6 p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <input v-model="filters.search" type="text" placeholder="Cari nama atau NIM..." class="dash-input" />
                    </div>
                    <div>
                        <select v-model="filters.status" @change="search" class="dash-input cursor-pointer">
                            <option value="">Semua Status</option>
                            <option v-for="s in statusList" :key="s" :value="s">{{ s }}</option>
                        </select>
                    </div>
                    <div>
                        <select v-model="filters.prodi" @change="search" class="dash-input cursor-pointer">
                            <option value="">Semua Prodi</option>
                            <option v-for="p in prodiList" :key="p" :value="p">{{ p }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="dash-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Pendaftar</th>
                                <th>Prodi / Smt</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in pendaftarans.data" :key="p.id">
                                <td>
                                    <p class="font-medium text-white">{{ p.nama }}</p>
                                    <p class="font-mono text-xs text-white/35">{{ p.nim }}<span v-if="p.gelombang"> • {{ p.gelombang.nama }}</span></p>
                                </td>
                                <td>{{ p.prodi }} <span class="text-white/35">/ Smt {{ p.semester }}</span></td>
                                <td><span :class="[statusColors[p.status], 'dash-badge capitalize']">{{ p.status }}</span></td>
                                <td>
                                    <div class="flex items-center gap-1">
                                        <Link :href="`/pendaftaran/${p.id}`" class="dash-link-edit !text-cyan-300">Lihat</Link>
                                        <Link :href="`/pendaftaran/${p.id}/edit`" class="dash-link-edit">Kelola</Link>
                                        <button @click="askDelete(p)" class="dash-link-delete">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="pendaftarans.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-white/30">Belum ada pendaftar.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="pendaftarans.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 px-6 py-4">
                    <p class="text-sm text-white/35">Menampilkan {{ pendaftarans.from }} - {{ pendaftarans.to }} dari {{ pendaftarans.total }} pendaftar</p>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="page in pendaftarans.last_page"
                            :key="page"
                            :href="pendaftarans.path + '?page=' + page"
                            :class="['dash-page-btn', { active: page === pendaftarans.current_page }]"
                        >
                            {{ page }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: PERIODE -->
        <div v-else>
            <div class="dash-card mb-6 flex flex-wrap items-center justify-between gap-3 p-5">
                <p class="max-w-xl text-sm text-white/50">Aktifkan satu periode dalam satu waktu. Form publik <span class="font-mono text-cyan-300">/join</span> hanya bisa diisi saat ada periode yang dibuka dan tanggalnya berlaku.</p>
                <Link href="/admin/gelombang/create" class="dash-btn-primary">+ Buat Periode</Link>
            </div>

            <div class="dash-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Rentang</th>
                                <th>Status</th>
                                <th class="center">Pendaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="g in gelombangs" :key="g.id">
                                <td>
                                    <p class="font-medium text-white">{{ g.nama }}</p>
                                    <p class="max-w-xs truncate text-xs text-white/35">{{ g.deskripsi || '-' }}</p>
                                </td>
                                <td class="whitespace-nowrap font-mono text-xs">{{ g.tanggal_mulai }} — {{ g.tanggal_selesai }}</td>
                                <td><span :class="[periodeColors[g.status_label], 'dash-badge']">{{ g.status_label }}</span></td>
                                <td class="text-center font-mono">{{ g.total_pendaftar }}</td>
                                <td>
                                    <div class="flex flex-wrap items-center gap-1">
                                        <button @click="toggleGelombang(g)" :class="g.is_open ? 'dash-link-delete' : 'dash-link-edit !text-emerald-300'">
                                            {{ g.is_open ? 'Tutup' : 'Buka' }}
                                        </button>
                                        <Link :href="`/admin/gelombang/${g.id}/edit`" class="dash-link-edit">Edit</Link>
                                        <button @click="askDeleteGelombang(g)" class="dash-link-delete">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="gelombangs.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-white/30">Belum ada periode. Buat periode pertama untuk membuka pendaftaran.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="confirmOpen"
            title="Hapus pendaftaran?"
            message="Data yang dihapus tidak bisa dikembalikan. Yakin lanjut?"
            :itemName="deleteTarget?.nama || ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDelete"
        />

        <ConfirmModal
            :show="confirmGelombangOpen"
            title="Hapus periode?"
            message="Pendaftar pada periode ini tidak ikut terhapus. Yakin lanjut?"
            :itemName="deleteGelombang?.nama || ''"
            :processing="deletingGelombang"
            @cancel="confirmGelombangOpen = false"
            @update:show="confirmGelombangOpen = $event"
            @confirm="confirmDeleteGelombang"
        />
    </Layout>
</template>
