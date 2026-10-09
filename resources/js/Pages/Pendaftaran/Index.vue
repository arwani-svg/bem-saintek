<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const props = defineProps({
    pendaftarans: Array,
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
    router.delete(`/pendaftaran/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}

const statusColors = {
    pending: 'bg-amber-300/10 text-amber-300',
    diterima: 'bg-emerald-300/10 text-emerald-300',
    ditolak: 'bg-red-400/10 text-red-300',
}
</script>

<template>
    <Head title="Pendaftaran Pengurus" />

    <Layout
        title="Pendaftaran Pengurus"
        subtitle="Pantau status pendaftaranmu"
        eyebrow="SAINTEK.OS // PENDAFTARAN"
        activeMenu="pendaftaran"
        :user="user"
        :showBack="false"
    >
        <div class="dash-card mb-6 flex flex-wrap items-center justify-between gap-3 p-5">
            <p class="text-sm text-white/50">Satu akun hanya bisa mendaftar satu kali per periode yang dibuka. Pantau statusmu di bawah.</p>
            <Link href="/pendaftaran/create" class="dash-btn-primary">+ Daftar Pengurus</Link>
        </div>

        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Nama / NIM</th>
                            <th>Prodi</th>
                            <th>Smt</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in pendaftarans" :key="p.id">
                            <td>
                                <p class="font-medium text-white">{{ p.nama }}</p>
                                <p class="font-mono text-xs text-white/35">{{ p.nim }}</p>
                            </td>
                            <td>{{ p.prodi }}</td>
                            <td class="text-center">{{ p.semester }}</td>
                            <td><span :class="[statusColors[p.status], 'dash-badge capitalize']">{{ p.status }}</span></td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <Link :href="`/pendaftaran/${p.id}`" class="dash-link-edit !text-cyan-300">Lihat</Link>
                                    <Link v-if="p.status === 'pending'" :href="`/pendaftaran/${p.id}/edit`" class="dash-link-edit">Edit</Link>
                                    <button v-if="p.status === 'pending'" @click="askDelete(p)" class="dash-link-delete">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="pendaftarans.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-white/30">Belum ada pendaftaran. Klik “Daftar Pengurus” untuk mulai.</td>
                        </tr>
                    </tbody>
                </table>
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
    </Layout>
</template>
