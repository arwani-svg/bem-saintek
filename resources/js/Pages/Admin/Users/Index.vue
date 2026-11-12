<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, ref, watch } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import Modal from '@/components/dashboard/Modal.vue'
// import UserEditForm from './UserEditForm.vue'
import UserForm from './UserForm.vue'

const props = defineProps({
    users: Object,
    filters: Object,
    user: Object,
    rolesList: Array,
    prodiList: Array,
    semesterList: Array,
    statusList: Array,
})

const filters = reactive({
    search: props.filters.search || '',
    role: props.filters.role || '',
    status: props.filters.status || '',
    prodi: props.filters.prodi || '',
    semester: props.filters.semester || '',
})

const search = () => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.role) params.append('role', filters.role)
    if (filters.status) params.append('status', filters.status)
    if (filters.prodi) params.append('prodi', filters.prodi)
    if (filters.semester) params.append('semester', filters.semester)

    router.visit('/users' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}

const reset = () => {
    filters.search = ''
    filters.role = ''
    filters.status = ''
    filters.prodi = ''
    filters.semester = ''
    search()
}

const adaFilter = () => !!(filters.search || filters.role || filters.status || filters.prodi || filters.semester)

let debounceTimer = null
watch(() => filters.search, () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(search, 300)
})

const deleteTarget = ref(null)
const confirmOpen = ref(false)
const deleting = ref(false)

const askDeleteUser = (user) => {
    deleteTarget.value = user
    confirmOpen.value = true
}

const confirmDeleteUser = () => {
    if (!deleteTarget.value) return
    deleting.value = true
    router.delete(`/users/${deleteTarget.value.id}`, {
        onFinish: () => {
            deleting.value = false
            confirmOpen.value = false
            deleteTarget.value = null
        },
    })
}

const statusColors = {
    aktif: 'bg-emerald-300/10 text-emerald-300',
    cuti: 'bg-amber-300/10 text-amber-300',
    DO: 'bg-red-400/10 text-red-300',
    keluar: 'bg-rose-300/10 text-rose-300',
    lulus: 'bg-cyan-300/10 text-cyan-300',
}

const roleColors = {
    mahasiswa: 'bg-cyan-300/10 text-cyan-300',
    pengurus: 'bg-violet-300/10 text-violet-300',
}
// --- Modal State untuk Create & Edit ---
const showModal = ref(false)
const editingUser = ref(null)

const modalTitle = computed(() => (editingUser.value ? 'Edit User' : 'Tambah User'))
const modalSubtitle = computed(() =>
    editingUser.value ? `Perbarui informasi ${editingUser.value.name}` : 'Buat akun baru untuk mahasiswa atau pengurus',
)

const openCreate = () => {
    editingUser.value = null
    showModal.value = true
}

const openEdit = (user) => {
    editingUser.value = user
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editingUser.value = null
}
</script>

<template>
    <Head title="Manajemen User" />

    <Layout
        title="Manajemen User"
        subtitle="Kelola data mahasiswa dan pengurus"
        eyebrow="SAINTEK.OS // USERS"
        activeMenu="users"
        :user="user"
        backHref="/dashboard"
    >

        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-6">
                <div class="md:col-span-3 lg:col-span-2">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Cari nama / NIM..."
                        class="dash-input"
                    />
                </div>
                <div>
                    <select v-model="filters.role" @change="search" class="dash-input cursor-pointer">
                        <option value="">Semua Role</option>
                        <option v-for="r in rolesList ?? ['mahasiswa', 'pengurus']" :key="r" :value="r" class="capitalize">{{ r }}</option>
                    </select>
                </div>
                <div>
                    <select v-model="filters.status" @change="search" class="dash-input cursor-pointer">
                        <option value="">Semua Status</option>
                        <option v-for="s in statusList ?? ['aktif', 'cuti', 'DO', 'keluar', 'lulus']" :key="s" :value="s">{{ s }}</option>
                    </select>
                </div>
                <div>
                    <select v-model="filters.prodi" @change="search" class="dash-input cursor-pointer">
                        <option value="">Semua Prodi</option>
                        <option v-for="p in prodiList ?? []" :key="p" :value="p">{{ p }}</option>
                    </select>
                </div>
                <div>
                    <select
                        v-model="filters.semester"
                        @change="search"
                        class="dash-input cursor-pointer"
                        title="Semester berjalan (dihitung otomatis dari angkatan)"
                    >
                        <option value="">Semua Semester</option>
                        <option v-for="s in semesterList ?? []" :key="s" :value="s">Semester {{ s }}</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <button @click="openCreate" class="dash-btn-primary">
                    + Tambah User
                </button>
                <button v-if="adaFilter()" @click="reset" class="dash-btn-ghost">
                    Reset
                </button>
            </div>
        </div>

        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>NIM</th>
                            <th>Role</th>
                            <th>Prodi</th>
                            <th>Angkatan / Smt</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="u in users.data" :key="u.id">
                            <td>
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="u.foto_url"
                                        :src="u.foto_url"
                                        :alt="u.name"
                                        class="h-10 w-10 shrink-0 rounded-full border border-white/20 object-cover"
                                        loading="lazy"
                                    />
                                    <div
                                        v-else
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 text-sm font-bold text-black"
                                    >
                                        {{ u.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-white">{{ u.name }}</p>
                                        <p class="text-xs text-white/35">{{ u.jenis_kelamin }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs">{{ u.nim }}</td>
                            <td>
                                <span :class="[(roleColors[u.role] ?? 'bg-white/10 text-white/60'), 'dash-badge capitalize']">
                                    {{ u.role }}
                                </span>
                            </td>
                            <td>{{ u.prodi }}</td>
                            <td class="text-center whitespace-nowrap">{{ u.angkatan }} <span class="text-white/35">/ Smt {{ u.semester ?? '-' }}</span></td>
                            <td class="text-center">{{ u.kelas || '-' }}</td>
                            <td>
                                <span :class="[statusColors[u.status], 'dash-badge capitalize']">
                                    {{ u.status }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <Link :href="`/users/${u.id}`" class="dash-link-edit !text-cyan-300">Lihat</Link>
                <button @click="openEdit(u)" class="dash-link-edit">Edit</button>
                <button @click="askDeleteUser(u)" class="dash-link-delete">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="8" class="px-6 py-12 text-center text-white/30">
                                Tidak ada data user ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="users.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 px-6 py-4">
                <p class="text-sm text-white/35">
                    Menampilkan {{ users.from }} - {{ users.to }} dari {{ users.total }} user
                </p>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="page in users.last_page"
                        :key="page"
                        :href="users.path + '?page=' + page"
                        :class="['dash-page-btn', { active: page === users.current_page }]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>

        <!-- Pop-up Create / Edit User Modal -->
        <!-- Pop-up Modal untuk Create / Edit User -->
        <Modal
            :show="showModal"
            :title="modalTitle"
            :subtitle="modalSubtitle"
            eyebrow="SAINTEK.OS // USERS"
            size="xl"
            @close="closeModal"
            @update:show="showModal = $event"
        >
            <UserForm
                :key="editingUser?.id || 'create-user'"
                :user="editingUser"
                :prodiList="prodiList"
                :statusList="statusList"
                :keloasList="keloasList"
                :rolesList="rolesList"
                @success="closeModal"
                @cancel="closeModal"
            />
        </Modal>
    </Layout>
</template>