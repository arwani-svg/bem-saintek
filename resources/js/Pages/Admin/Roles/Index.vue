<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

defineProps({
    roles: Array,
    user: Object,
})

// Warna badge permission bervariasi tapi tetap selaras tema dashboard.
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

    router.delete(`/roles/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}
</script>

<template>
    <Head title="Daftar Role" />

    <Layout
        title="Daftar Role"
        subtitle="Jabatan yang tersedia — klik Atur untuk ubah hak aksesnya"
        eyebrow="SAINTEK.OS // ACCESS"
        activeMenu="roles"
        :user="user"
        backHref="/dashboard"
    >

        <div class="dash-card mb-6 p-5 sm:p-6">

            <div class="mt-4">
                <Link href="/roles/create" class="dash-btn-primary">
                    + Tambah Role
                </Link>
            </div>
        </div>

        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table w-full">

                    <thead>
                        <tr>
                            <th class="w-full">Role</th>
                            <th class="w-auto whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="r in roles" :key="r.id">

                            <!-- ROLE + PERMISSIONS -->
                            <td class="align-top">

                                <!-- Role -->
                                <div class="flex items-center gap-2">
                                    <span
                                        class="capitalize text-base font-semibold tracking-tight text-white"
                                    >
                                        {{ r.name }}
                                    </span>

                                    <span
                                        v-if="r.is_protected"
                                        class="text-[10px] font-medium text-amber-300"
                                    >
                                        sistem
                                    </span>
                                </div>

                                <!-- Permissions -->
                                <div
                                    v-if="r.permissions.length"
                                    class="mt-2 flex w-full flex-wrap gap-1"
                                >
                                    <span
                                        v-for="p in r.permissions"
                                        :key="p"
                                        :class="[
                                            'dash-badge border',
                                            'px-2 py-0.5',
                                            'text-[10px] leading-4',
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

                            <!-- AKSI -->
                            <td class="align-top">
                                <div
                                    class="flex items-center gap-2 whitespace-nowrap"
                                >
                                    <Link
                                        :href="`/roles/${r.id}/edit`"
                                        class="dash-link-edit"
                                    >
                                        Atur
                                    </Link>

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
                    </tbody>
                </table>
            </div>
        </div>

        <ConfirmModal
            :show="confirmOpen"
            title="Hapus role?"
            message="User dengan role ini dikembalikan ke mahasiswa. Yakin lanjut?"
            :itemName="deleteTarget?.name || ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDelete"
        />
    </Layout>
</template>