<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, ref, watch } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import Modal from '@/components/dashboard/Modal.vue'
import StrukturalForm from './StrukturalForm.vue'

const props = defineProps({
    strukturals: Object,
    filters: Object,
    periods: Array,
    departemens: Array,
    users: Array,
    user: Object,
})

const filters = reactive({
    search: props.filters.search || '',
    periode: props.filters.periode || '',
    departemen_id: props.filters.departemen_id || '',
})

const search = () => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.periode) params.append('periode', filters.periode)
    if (filters.departemen_id) params.append('departemen_id', filters.departemen_id)

    router.visit('/struktural' + (params.toString() ? '?' + params.toString() : ''), {
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

const showModal = ref(false)
const editing = ref(null)

const modalTitle = computed(() => (editing.value ? 'Edit Struktural' : 'Tambah Struktural'))
const modalSubtitle = computed(() =>
    editing.value
        ? `Perbarui ${editing.value.jabatan} periode ${editing.value.periode}`
        : 'Hubungkan user dengan jabatan, departemen, dan periode.',
)

const openCreate = () => {
    editing.value = null
    showModal.value = true
}

const openEdit = (struktural) => {
    editing.value = struktural
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editing.value = null
}

const askDelete = (struktural) => {
    deleteTarget.value = struktural
    confirmOpen.value = true
}

const confirmDelete = () => {
    if (!deleteTarget.value) return
    deleting.value = true
    router.delete(`/struktural/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}

const unitLabel = (struktural) => struktural.departemen?.nama || 'Pengurus Harian (PH)'

const initials = (name) => (name || '?').trim().charAt(0).toUpperCase()
</script>

<template>
    <Head title="Manajemen Struktural" />

    <Layout
        title="Manajemen Struktural"
        subtitle="Kelola jabatan pengurus per periode dan departemen"
        eyebrow="SAINTEK.OS // STRUKTURAL"
        activeMenu="struktural"
        :user="user"
        backHref="/dashboard"
    >
        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Cari nama, NIM, prodi, atau jabatan..."
                        class="dash-input"
                    />
                </div>
                <div>
                    <select v-model="filters.periode" @change="search" class="dash-input cursor-pointer">
                        <option value="">Semua Periode</option>
                        <option v-for="p in periods" :key="p" :value="p">{{ p }}</option>
                    </select>
                </div>
                <div>
                    <select v-model="filters.departemen_id" @change="search" class="dash-input cursor-pointer">
                        <option value="">Semua Unit</option>
                        <option value="__ph__">Pengurus Harian (PH)</option>
                        <option v-for="d in departemens" :key="d.id" :value="String(d.id)">{{ d.nama }}</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button @click="openCreate" class="dash-btn-primary">
                    + Tambah Struktural
                </button>
            </div>
        </div>

        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Pengurus</th>
                            <th>Jabatan</th>
                            <th>Unit</th>
                            <th>Periode</th>
                            <th class="center">Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="s in strukturals.data" :key="s.id">
                            <td>
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="s.user?.foto_url"
                                        :src="s.user.foto_url"
                                        :alt="s.user.name"
                                        class="h-10 w-10 shrink-0 rounded-xl border border-white/10 object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-cyan-300/20 bg-cyan-300/5 font-display text-sm font-bold text-cyan-300"
                                    >
                                        {{ initials(s.user?.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-white">{{ s.user?.name || '-' }}</p>
                                        <p class="text-xs text-white/35">
                                            {{ s.user?.nim || '-' }} · {{ s.user?.prodi || '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="dash-badge bg-cyan-300/10 text-cyan-300">{{ s.jabatan }}</span>
                            </td>
                            <td>
                                <span v-if="s.departemen" class="dash-badge bg-violet-300/10 text-violet-300">
                                    {{ s.departemen.nama }}
                                </span>
                                <span v-else class="dash-badge bg-white/10 text-white/50">
                                    PH
                                </span>
                            </td>
                            <td class="font-mono text-xs text-white/60">{{ s.periode }}</td>
                            <td class="text-center font-mono text-xs text-white/40">{{ s.urutan }}</td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <button @click="openEdit(s)" class="dash-link-edit">
                                        Edit
                                    </button>
                                    <button @click="askDelete(s)" class="dash-link-delete">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="strukturals.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-white/30">
                                Belum ada data struktural. Klik "Tambah Struktural" untuk menghubungkan user dengan jabatan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="strukturals.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 px-6 py-4">
                <p class="text-sm text-white/35">
                    Menampilkan {{ strukturals.from }} - {{ strukturals.to }} dari {{ strukturals.total }} data
                </p>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="page in strukturals.last_page"
                        :key="page"
                        :href="strukturals.path + '?page=' + page"
                        :class="['dash-page-btn', { active: page === strukturals.current_page }]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="confirmOpen"
            title="Hapus data struktural?"
            message="Data jabatan ini akan dihapus dari halaman struktural publik. User dan departemen tetap aman."
            :itemName="deleteTarget ? `${deleteTarget.user?.name || '-'} — ${deleteTarget.jabatan}` : ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDelete"
        />

        <Modal
            :show="showModal"
            :title="modalTitle"
            :subtitle="modalSubtitle"
            eyebrow="SAINTEK.OS // STRUKTURAL"
            size="lg"
            @close="closeModal"
            @update:show="showModal = $event"
        >
            <StrukturalForm
                :key="editing?.id || 'create'"
                :struktural="editing"
                :users="users"
                :departemens="departemens"
                :periods="periods"
                @success="closeModal"
                @cancel="closeModal"
            />
        </Modal>
    </Layout>
</template>
