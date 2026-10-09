<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, ref, watch } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const props = defineProps({
    informasis: Object,
    filters: Object,
    jenisList: Object,
    user: Object,
})

const filters = reactive({
    search: props.filters.search || '',
    jenis: props.filters.jenis || '',
})

const search = () => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.jenis) params.append('jenis', filters.jenis)

    router.visit('/informasi' + (params.toString() ? '?' + params.toString() : ''), {
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

const askDeleteItem = (item) => {
    deleteTarget.value = item
    confirmOpen.value = true
}

const confirmDeleteItem = () => {
    if (!deleteTarget.value) return
    deleting.value = true
    router.delete(`/informasi/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}

const jenisColors = {
    berita: 'bg-cyan-300/10 text-cyan-300',
    infografis: 'bg-violet-300/10 text-violet-300',
    penghargaan: 'bg-amber-300/10 text-amber-300',
    lomba: 'bg-emerald-300/10 text-emerald-300',
}
</script>

<template>
    <Head title="Manajemen Informasi" />

    <Layout
        title="Manajemen Informasi"
        subtitle="Satu CRUD untuk berita, infografis, penghargaan & lomba"
        eyebrow="SAINTEK.OS // INFORMASI"
        activeMenu="informasi"
        :user="user"
        backHref="/dashboard"
    >

        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Cari judul atau ringkasan..."
                        class="dash-input"
                    />
                </div>
                <div class="md:col-span-2">
                    <select v-model="filters.jenis" @change="search" class="dash-input cursor-pointer">
                        <option value="">Semua Jenis</option>
                        <option v-for="(label, key) in jenisList" :key="key" :value="key">{{ label }}</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <Link href="/informasi/create" class="dash-btn-primary">
                    + Tambah Informasi
                </Link>
            </div>
        </div>

        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Konten</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th class="center">Unggulan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in informasis.data" :key="item.id">
                            <td>
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="item.gambar_url"
                                        :src="item.gambar_url"
                                        :alt="item.judul"
                                        class="h-14 w-14 shrink-0 rounded-xl border border-white/10 object-cover"
                                    />
                                    <div v-else class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/[0.03] text-xl">📰</div>
                                    <div class="min-w-0">
                                        <p class="max-w-xs truncate font-medium text-white">{{ item.judul }}</p>
                                        <p class="font-mono text-xs text-white/35">{{ item.tanggal_publikasi || item.created_at }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span :class="[jenisColors[item.jenis], 'dash-badge']">
                                    {{ item.jenis_label }}
                                </span>
                            </td>
                            <td>
                                <span
                                    :class="item.is_published
                                        ? 'bg-emerald-300/10 text-emerald-300'
                                        : 'bg-white/10 text-white/40'"
                                    class="dash-badge"
                                >
                                    {{ item.is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="text-center text-lg" :title="item.is_unggulan ? 'Tampil sebagai sorotan' : 'Tidak disorot'">
                                {{ item.is_unggulan ? '⭐' : '—' }}
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <Link :href="`/informasi/${item.id}/edit`" class="dash-link-edit">
                                        Edit
                                    </Link>
                                    <button @click="askDeleteItem(item)" class="dash-link-delete">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="informasis.data.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-white/30">
                                Belum ada informasi. Klik "Tambah Informasi" untuk membuat yang pertama.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="informasis.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 px-6 py-4">
                <p class="text-sm text-white/35">
                    Menampilkan {{ informasis.from }} - {{ informasis.to }} dari {{ informasis.total }} informasi
                </p>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="page in informasis.last_page"
                        :key="page"
                        :href="informasis.path + '?page=' + page"
                        :class="['dash-page-btn', { active: page === informasis.current_page }]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="confirmOpen"
            title="Hapus informasi?"
            message="Konten yang dihapus tidak bisa dikembalikan. Yakin lanjut?"
            :itemName="deleteTarget?.judul || ''"
            :processing="deleting"
            @cancel="confirmOpen = false"
            @update:show="confirmOpen = $event"
            @confirm="confirmDeleteItem"
        />
    </Layout>
</template>
