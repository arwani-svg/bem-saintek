
<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, ref, watch } from 'vue'

import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import Modal from '@/components/dashboard/Modal.vue'
import ProkerForm from './ProkerForm.vue'

const props = defineProps({
    prokers: Object,
    filters: Object,
    departemens: Array,
    statusList: Object,
    warnaList: Array,
    user: Object,
})

// ================= FILTER =================

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

    router.visit('/proker' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}

let debounceTimer = null

watch(() => filters.search, () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(search, 300)
})

// ================= DELETE =================

const deleteTarget = ref(null)
const confirmOpen = ref(false)
const deleting = ref(false)

const askDeleteProker = (proker) => {
    deleteTarget.value = proker
    confirmOpen.value = true
}

const confirmDeleteProker = () => {
    if (!deleteTarget.value) return

    deleting.value = true

    router.delete(`/proker/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}

// ================= MODAL CREATE / EDIT =================

const showModal = ref(false)
const editing = ref(null)

const modalTitle = computed(() =>
    editing.value ? 'Edit Program Kerja' : 'Tambah Program Kerja'
)

const modalSubtitle = computed(() =>
    editing.value
        ? `Perbarui ${editing.value.judul}`
        : 'Buat program kerja baru untuk departemen'
)

const openCreate = () => {
    editing.value = null
    showModal.value = true
}

const openEdit = (proker) => {
    editing.value = proker
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editing.value = null
}

// ================= STATUS =================

const statusColors = {
    akan_datang: 'bg-cyan-300/10 text-cyan-300',
    berlangsung: 'bg-amber-300/10 text-amber-300',
    selesai: 'bg-white/10 text-white/40',
}
</script>

<template>
    <Head title="Manajemen Program Kerja" />

    <Layout
        title="Manajemen Program Kerja"
        subtitle="Kelola proker tiap departemen"
        eyebrow="SAINTEK.OS // PROKER"
        activeMenu="proker"
        :user="user"
        backHref="/dashboard"
    >

        <!-- FILTER & TAMBAH -->
        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                <div class="md:col-span-2">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Cari judul, kategori, atau lokasi..."
                        class="dash-input"
                    />
                </div>

                <div>
                    <select
                        v-model="filters.departemen_id"
                        @change="search"
                        class="dash-input cursor-pointer"
                    >
                        <option value="">Semua Departemen</option>

                        <option
                            v-for="d in departemens"
                            :key="d.id"
                            :value="d.id"
                        >
                            {{ d.nama }}
                        </option>
                    </select>
                </div>

                <div>
                    <select
                        v-model="filters.status"
                        @change="search"
                        class="dash-input cursor-pointer"
                    >
                        <option value="">Semua Status</option>

                        <option
                            v-for="(label, key) in statusList"
                            :key="key"
                            :value="key"
                        >
                            {{ label }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button
                    @click="openCreate"
                    class="dash-btn-primary"
                >
                    + Tambah Program Kerja
                </button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Program Kerja</th>
                            <th>Departemen</th>
                            <th>Status</th>
                            <th class="center">Depan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="p in prokers.data"
                            :key="p.id"
                        >
                            <td>
                                <p class="font-medium text-white">
                                    {{ p.judul }}
                                </p>

                                <p class="text-xs text-white/35">
                                    {{ p.kategori || '-' }}
                                    <span v-if="p.lokasi">
                                        • {{ p.lokasi }}
                                    </span>
                                </p>
                            </td>

                            <td>
                                <span class="dash-badge bg-cyan-300/10 text-cyan-300">
                                    {{ p.departemen?.nama || '-' }}
                                </span>
                            </td>

                            <td>
                                <span
                                    :class="[
                                        statusColors[p.status] || 'bg-white/10 text-white/40',
                                        'dash-badge'
                                    ]"
                                >
                                    {{ statusList[p.status] || p.status }}
                                </span>
                            </td>

                            <td
                                class="text-center text-lg"
                                :title="p.is_unggulan
                                    ? 'Tampil di halaman depan'
                                    : 'Tidak tampil di halaman depan'"
                            >
                                {{ p.is_unggulan ? '⭐' : '—' }}
                            </td>

                            <td>
                                <div class="flex items-center gap-1">
                                    <button
                                        @click="openEdit(p)"
                                        class="dash-link-edit"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        @click="askDeleteProker(p)"
                                        class="dash-link-delete"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="prokers.data.length === 0">
                            <td
                                colspan="5"
                                class="px-6 py-12 text-center text-white/30"
                            >
                                Belum ada program kerja.
                                Klik "Tambah Program Kerja" untuk membuat yang pertama.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div
                v-if="prokers.last_page > 1"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 px-6 py-4"
            >
                <p class="text-sm text-white/35">
                    Menampilkan {{ prokers.from }} - {{ prokers.to }}
                    dari {{ prokers.total }} program kerja
                </p>

                <div class="flex items-center gap-1">
                    <Link
                        v-for="page in prokers.last_page"
                        :key="page"
                        :href="prokers.path + '?page=' + page"
                        :class="[
                            'dash-page-btn',
                            { active: page === prokers.current_page }
                        ]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>

        <!-- CONFIRM DELETE -->
        <ConfirmModal
            :show="confirmOpen"
            title="Hapus program kerja?"
            message="Proker yang dihapus tidak bisa dikembalikan. Yakin lanjut?"
            :itemName="deleteTarget?.judul || ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDeleteProker"
        />

        <!-- POPUP CREATE / EDIT -->
        <Modal
            :show="showModal"
            :title="modalTitle"
            :subtitle="modalSubtitle"
            eyebrow="SAINTEK.OS // PROKER"
            size="xl"
            @close="closeModal"
            @update:show="showModal = $event"
        >
            <ProkerForm
                :key="editing?.id || 'create'"
                :proker="editing"
                :departemens="departemens"
                :statusList="statusList"
                :warnaList="warnaList"
                @success="closeModal"
                @cancel="closeModal"
            />
        </Modal>

    </Layout>
</template>
