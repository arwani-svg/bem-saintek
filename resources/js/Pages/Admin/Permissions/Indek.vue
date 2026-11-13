<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

defineProps({
    permissions: Array, // [{id, name, description, roles_count}]
    user: Object,
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
    router.delete(`/permissions/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
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
        
        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="mt-4">
                <Link href="/permissions/create" class="dash-btn-primary">
                    + Tambah Permission
                </Link>
            </div>
        </div>

        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in permissions" :key="p.id">
                            <td><span class="font-mono text-xs whitespace-nowrap text-cyan-200">{{ p.name }}</span></td>
                            <td class="max-w-md text-sm text-white/50">{{ p.description || '— belum ada deskripsi —' }}</td>
                            <td>
                                <div class="flex items-center gap-1 whitespace-nowrap">
                                    <Link :href="`/permissions/${p.id}/edit`" class="dash-link-edit">Ubah</Link>
                                    <button v-if="p.roles_count === 0" class="dash-link-delete" @click="askDelete(p)">Hapus</button>
                                    <span v-else class="text-xs text-white/25">terkunci</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="permissions.length === 0">
                            <td colspan="3" class="px-6 py-12 text-center text-white/30">Belum ada permission.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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
    </Layout>
</template>
