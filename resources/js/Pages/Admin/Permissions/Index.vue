
<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import Modal from '@/components/dashboard/Modal.vue'

import PermissionForm from './PermissionForm.vue'

const props = defineProps({
    permissions: Array,
    user: Object,
})

// ================= DELETE =================

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

    router.delete(`/permissions/${deleteTarget.value.id}`, {
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
    editing.value ? 'Ubah Permission' : 'Tambah Permission'
)

const modalSubtitle = computed(() =>
    editing.value
        ? `Perbarui deskripsi permission ${editing.value.name}`
        : 'Daftarkan hak akses baru untuk ditempel ke role'
)

const openCreate = () => {
    editing.value = null
    showModal.value = true
}

const openEdit = (permission) => {
    editing.value = permission
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editing.value = null
}
</script>

<template>
    <Head title="Permissions" />

    <Layout
        title="Permissions"
        subtitle="Hak akses yang bisa ditempel ke role"
        eyebrow="SAINTEK.OS // ACCESS"
        activeMenu="permissions"
        :user="user"
        backHref="/dashboard"
    >

        <!-- HEADER ACTION -->
        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h3 class="font-display font-bold text-white">
                        Manajemen Permissions
                    </h3>
                    <p class="mt-1 text-xs text-white/40">
                        {{ permissions.length }} hak akses terdaftar
                    </p>
                </div>

                <button
                    @click="openCreate"
                    class="dash-btn-primary"
                >
                    + Tambah Permission
                </button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table w-full">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="p in permissions"
                            :key="p.id"
                        >
                            <td>
                                <span class="whitespace-nowrap font-mono text-xs text-cyan-200">
                                    {{ p.name }}
                                </span>
                            </td>

                            <td class="max-w-md text-sm text-white/50">
                                {{ p.description || '— belum ada deskripsi —' }}
                            </td>

                            <td>
                                <div class="flex items-center gap-1 whitespace-nowrap">

                                    <button
                                        @click="openEdit(p)"
                                        class="dash-link-edit"
                                    >
                                        Ubah
                                    </button>

                                    <button
                                        v-if="p.roles_count === 0"
                                        class="dash-link-delete"
                                        @click="askDelete(p)"
                                    >
                                        Hapus
                                    </button>

                                    <span
                                        v-else
                                        class="text-xs text-white/25"
                                    >
                                        terkunci
                                    </span>

                                </div>
                            </td>
                        </tr>

                        <tr v-if="permissions.length === 0">
                            <td
                                colspan="3"
                                class="px-6 py-12 text-center text-white/30"
                            >
                                Belum ada permission.
                                Klik "Tambah Permission" untuk membuat yang pertama.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CONFIRM DELETE -->
        <ConfirmModal
            :show="confirmOpen"
            title="Hapus permission?"
            message="Permission yang tak terpakai akan dihapus permanen."
            :itemName="deleteTarget?.name || ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDelete"
        />

        <!-- POPUP CREATE / EDIT -->
        <Modal
            :show="showModal"
            :title="modalTitle"
            :subtitle="modalSubtitle"
            eyebrow="SAINTEK.OS // ACCESS"
            size="md"
            @close="closeModal"
            @update:show="showModal = $event"
        >
            <PermissionForm
                :key="editing?.id || 'create'"
                :permission="editing"
                @success="closeModal"
                @cancel="closeModal"
            />
        </Modal>

    </Layout>
</template>
