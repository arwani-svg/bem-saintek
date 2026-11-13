
<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import Modal from '@/components/dashboard/Modal.vue'
import RoleForm from './RoleForm.vue'

const props = defineProps({
    roles: Array,
    allPermissions: Array,
    user: Object,
})

const PALETTE = [
    'border-cyan-300/20 bg-cyan-300/10 text-cyan-300',
    'border-violet-300/20 bg-violet-300/10 text-violet-300',
    'border-emerald-300/20 bg-emerald-300/10 text-emerald-300',
    'border-amber-300/20 bg-amber-300/10 text-amber-300',
    'border-rose-300/20 bg-rose-300/10 text-rose-300',
    'border-sky-300/20 bg-sky-300/10 text-sky-300',
    'border-fuchsia-300/20 bg-fuchsia-300/10 text-fuchsia-300',
]

const permColor = (name) => {
    let hash = 0

    for (let i = 0; i < name.length; i++) {
        hash = (hash * 31 + name.charCodeAt(i)) >>> 0
    }

    return PALETTE[hash % PALETTE.length]
}

// Popup create / edit
const showModal = ref(false)
const editing = ref(null)

const modalTitle = computed(() =>
    editing.value ? `Atur Role ${editing.value.name}` : 'Tambah Role'
)

const modalSubtitle = computed(() =>
    editing.value
        ? editing.value.is_protected
            ? 'Role sistem: nama dikunci, permission tetap bisa diubah.'
            : 'Perbarui nama role dan hak aksesnya.'
        : 'Buat jabatan baru beserta hak aksesnya.'
)

const openCreate = () => {
    editing.value = null
    showModal.value = true
}

const openEdit = (role) => {
    editing.value = role
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editing.value = null
}

// Hapus role
const deleteTarget = ref(null)
const confirmOpen = ref(false)
const deleting = ref(false)

const askDelete = (role) => {
    deleteTarget.value = role
    confirmOpen.value = true
}

const closeConfirm = () => {
    confirmOpen.value = false
    deleteTarget.value = null
}

const confirmDelete = () => {
    if (!deleteTarget.value) return

    deleting.value = true

    router.delete(`/roles/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            closeConfirm()
        },
    })
}
</script>

<template>
    <Head title="Daftar Role" />

    <Layout
        title="Daftar Role"
        subtitle="Kelola jabatan dan hak akses pengguna"
        eyebrow="SAINTEK.OS // ACCESS"
        activeMenu="roles"
        :user="user"
        backHref="/dashboard"
    >
        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-display text-lg font-bold text-white">
                        Manajemen Role
                    </h3>
                    <p class="mt-1 text-sm text-white/40">
                        Atur jabatan dan permission sistem.
                    </p>
                </div>

                <button @click="openCreate" class="dash-btn-primary">
                    + Tambah Role
                </button>
            </div>
        </div>

        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table w-full">
                    <thead>
                        <tr>
                            <th class="w-full">Role</th>
                            <th class="whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="r in roles" :key="r.id">
                            <td class="align-top">
                                <div class="flex items-center gap-2">
                                    <span class="capitalize text-base font-semibold tracking-tight text-white">
                                        {{ r.name }}
                                    </span>

                                    <span
                                        v-if="r.is_protected"
                                        class="text-[10px] font-medium text-amber-300"
                                    >
                                        sistem
                                    </span>
                                </div>

                                <div
                                    v-if="r.permissions?.length"
                                    class="mt-2 flex w-full flex-wrap gap-1"
                                >
                                    <span
                                        v-for="p in r.permissions"
                                        :key="p"
                                        :class="[
                                            'dash-badge border px-2 py-0.5 text-[10px] leading-4',
                                            permColor(p)
                                        ]"
                                    >
                                        {{ p }}
                                    </span>
                                </div>

                                <span
                                    v-else
                                    class="mt-2 block text-[10px] text-white/30"
                                >
                                    — tanpa permission —
                                </span>
                            </td>

                            <td class="align-top">
                                <div class="flex items-center gap-2 whitespace-nowrap">
                                    <button
                                        @click="openEdit(r)"
                                        class="dash-link-edit"
                                    >
                                        Atur
                                    </button>

                                    <button
                                        v-if="!r.is_protected"
                                        class="dash-link-delete"
                                        @click="askDelete(r)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="roles.length === 0">
                            <td colspan="2" class="px-6 py-12 text-center text-white/30">
                                Belum ada role. Klik Tambah Role untuk membuat yang pertama.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Popup Create / Edit -->
        <Modal
            :show="showModal"
            :title="modalTitle"
            :subtitle="modalSubtitle"
            eyebrow="SAINTEK.OS // ACCESS"
            size="lg"
            @close="closeModal"
            @update:show="showModal = $event"
        >
            <RoleForm
                :key="editing?.id || 'create'"
                :role="editing"
                :allPermissions="allPermissions"
                @success="closeModal"
                @cancel="closeModal"
            />
        </Modal>

        <!-- Konfirmasi Hapus -->
        <ConfirmModal
            :show="confirmOpen"
            title="Hapus role?"
            message="User dengan role ini dikembalikan ke mahasiswa. Yakin lanjut?"
            :itemName="deleteTarget?.name || ''"
            :processing="deleting"
            @cancel="closeConfirm"
            @update:show="confirmOpen = $event"
            @confirm="confirmDelete"
        />
    </Layout>
</template>
