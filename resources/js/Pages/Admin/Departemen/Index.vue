<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, ref, watch } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import Modal from '@/components/dashboard/Modal.vue'
import DepartemenForm from './DepartemenForm.vue'

const props = defineProps({
    departemens: Object,
    filters: Object,
    user: Object,
})

const filters = reactive({
    search: props.filters.search || '',
})

const search = () => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)

    router.visit('/departemen' + (params.toString() ? '?' + params.toString() : ''), {
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

// --- Pop-up create / edit tanpa pindah halaman ---
const showModal = ref(false)
const editing = ref(null)

const modalTitle = computed(() => (editing.value ? 'Edit Departemen' : 'Tambah Departemen'))
const modalSubtitle = computed(() =>
    editing.value ? `Perbarui data ${editing.value.nama}` : 'Buat departemen baru BEM SAINTEK',
)

const openCreate = () => {
    editing.value = null
    showModal.value = true
}

const openEdit = (departemen) => {
    editing.value = departemen
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editing.value = null
}

const askDeleteDepartemen = (departemen) => {
    deleteTarget.value = departemen
    confirmOpen.value = true
}

const confirmDeleteDepartemen = () => {
    if (!deleteTarget.value) return
    deleting.value = true
    router.delete(`/departemen/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}
</script>

<template>
    <Head title="Manajemen Departemen" />

    <Layout
        title="Manajemen Departemen"
        subtitle="Kelola departemen BEM SAINTEK"
        eyebrow="SAINTEK.OS // DEPARTEMEN"
        activeMenu="departemen"
        :user="user"
        backHref="/dashboard"
    >

        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="flex flex-col gap-4 md:flex-row">
                <input
                    v-model="filters.search"
                    type="text"
                    placeholder="Cari nama atau kepanjangan departemen..."
                    class="dash-input flex-1"
                />
                <button @click="openCreate" class="dash-btn-primary">
                    + Tambah Departemen
                </button>
            </div>
        </div>

        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Departemen</th>
                            <th>Kepanjangan</th>
                            <th>Deskripsi</th>
                            <th class="center">Proker</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="d in departemens.data" :key="d.id">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-cyan-300/20 bg-cyan-300/5 text-xl">
                                        {{ d.icon }}
                                    </div>
                                    <span class="font-semibold text-white">{{ d.nama }}</span>
                                </div>
                            </td>
                            <td>{{ d.kepanjangan || '-' }}</td>
                            <td class="max-w-xs truncate">{{ d.deskripsi || '-' }}</td>
                            <td class="text-center">
                                <span class="dash-badge bg-violet-300/10 text-violet-300">
                                    {{ d.program_kerjas_count }} proker
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <button @click="openEdit(d)" class="dash-link-edit">
                                        Edit
                                    </button>
                                    <button @click="askDeleteDepartemen(d)" class="dash-link-delete">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="departemens.data.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-white/30">
                                Belum ada departemen. Klik "Tambah Departemen" untuk membuat yang pertama.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="departemens.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 px-6 py-4">
                <p class="text-sm text-white/35">
                    Menampilkan {{ departemens.from }} - {{ departemens.to }} dari {{ departemens.total }} departemen
                </p>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="page in departemens.last_page"
                        :key="page"
                        :href="departemens.path + '?page=' + page"
                        :class="['dash-page-btn', { active: page === departemens.current_page }]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="confirmOpen"
            title="Hapus departemen?"
            message="Pastikan departemen ini sudah tidak punya proker. Data yang dihapus tidak bisa dikembalikan."
            :itemName="deleteTarget?.nama || ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDeleteDepartemen"
        />

        <!-- Pop-up create / edit -->
        <Modal
            :show="showModal"
            :title="modalTitle"
            :subtitle="modalSubtitle"
            eyebrow="SAINTEK.OS // DEPARTEMEN"
            size="lg"
            @close="closeModal"
            @update:show="showModal = $event"
        >
            <DepartemenForm
                :key="editing?.id || 'create'"
                :departemen="editing"
                @success="closeModal"
                @cancel="closeModal"
            />
        </Modal>
    </Layout>
</template>
