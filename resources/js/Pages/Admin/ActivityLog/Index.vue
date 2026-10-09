<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, ref, watch } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'

const props = defineProps({
    logs: Object,
    filters: Object,
    activities: Array,
    user: Object,
})

const filters = reactive({
    search: props.filters.search || '',
    activity: props.filters.activity || '',
    dari: props.filters.dari || '',
    sampai: props.filters.sampai || '',
})

const loading = ref(false)

const visit = () => {
    const params = new URLSearchParams()
    for (const [k, v] of Object.entries(filters)) {
        if (v) params.append(k, v)
    }
    loading.value = true
    router.visit('/activity-log' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
        onFinish: () => (loading.value = false),
    })
}

// Pagination yang tidak menghilangkan filter aktif.
const pageHref = (page) => {
    const params = new URLSearchParams()
    for (const [k, v] of Object.entries(filters)) {
        if (v) params.append(k, v)
    }
    params.set('page', page)
    return '/activity-log?' + params.toString()
}

let debounceTimer = null
watch(() => filters.search, () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(visit, 400)
})

const reset = () => {
    Object.keys(filters).forEach((k) => (filters[k] = ''))
    visit()
}

const badgeClass = (activity) => ({
    login: 'bg-cyan-300/10 text-cyan-300',
    logout: 'bg-white/5 text-white/50',
    create: 'bg-emerald-300/10 text-emerald-300',
    update: 'bg-amber-300/10 text-amber-300',
    delete: 'bg-rose-300/10 text-rose-300',
    pendaftaran: 'bg-violet-300/10 text-violet-300',
    status: 'bg-sky-300/10 text-sky-300',
}[activity] || 'bg-white/5 text-white/50')

const activityLabel = (activity) => ({
    login: 'Masuk',
    logout: 'Keluar',
    create: 'Tambah',
    update: 'Ubah',
    delete: 'Hapus',
    pendaftaran: 'Daftar',
    status: 'Status',
}[activity] || activity)

const tanggalPenuh = (iso) => {
    if (!iso) return '-'
    return new Date(String(iso).replace(' ', 'T')).toLocaleString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    })
}

const waktuRelatif = (iso) => {
    if (!iso) return '-'
    const diff = Date.now() - new Date(String(iso).replace(' ', 'T')).getTime()
    const menit = Math.floor(diff / 60000)
    if (menit < 1) return 'baru saja'
    if (menit < 60) return `${menit} mnt lalu`
    const jam = Math.floor(menit / 60)
    if (jam < 24) return `${jam} jam lalu`
    const hari = Math.floor(jam / 24)
    if (hari < 30) return `${hari} hari lalu`
    return tanggalPenuh(iso)
}
</script>

<template>
    <Head title="Activity Log" />

    <Layout
        title="Activity Log"
        subtitle="Jejak aktivitas penting user — read-only"
        eyebrow="SAINTEK.OS // SISTEM"
        activeMenu="activity-log"
        :user="user"
        backHref="/dashboard"
    >
        <!-- Filter -->
        <div class="dash-card mb-6 p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div class="md:col-span-2">
                    <label class="dash-label">Cari deskripsi / user / aktivitas</label>
                    <input v-model="filters.search" type="text" placeholder="cth: Budi, keuangan, hapus..." class="dash-input" />
                </div>
                <div>
                    <label class="dash-label">Jenis aktivitas</label>
                    <select v-model="filters.activity" @change="visit" class="dash-input cursor-pointer">
                        <option value="">Semua aktivitas</option>
                        <option v-for="a in activities" :key="a" :value="a">{{ activityLabel(a) }} ({{ a }})</option>
                    </select>
                </div>
                <div>
                    <label class="dash-label">Dari tanggal</label>
                    <input v-model="filters.dari" @change="visit" type="date" class="dash-input" />
                </div>
                <div>
                    <label class="dash-label">Sampai tanggal</label>
                    <input v-model="filters.sampai" @change="visit" type="date" class="dash-input" />
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between gap-3">
                <p class="font-mono text-[11px] text-white/30">
                    {{ logs.total }} aktivitas tercatat
                    <span v-if="loading" class="ml-2 inline-flex items-center gap-1.5 text-cyan-300">
                        <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        memuat...
                    </span>
                </p>
                <button @click="reset" class="dash-btn-ghost !py-2 text-sm">Reset</button>
            </div>
        </div>

        <!-- Tabel -->
        <div class="dash-card relative overflow-hidden">
            <div v-if="loading" class="absolute inset-0 z-10 flex items-start justify-center bg-[#03050a]/40 pt-16 backdrop-blur-[1px]">
                <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-[#0a0f1a] px-4 py-2.5 text-sm text-white/70">
                    <svg class="h-4 w-4 animate-spin text-cyan-300" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                    </svg>
                    Memuat log...
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="log in logs.data" :key="log.id">
                            <td class="whitespace-nowrap">
                                <p class="text-white/80">{{ waktuRelatif(log.created_at) }}</p>
                                <p class="font-mono text-[11px] text-white/30" :title="log.created_at">{{ tanggalPenuh(log.created_at) }}</p>
                            </td>
                            <td class="whitespace-nowrap">
                                <p class="font-medium text-white">{{ log.user?.name || '—' }}</p>
                                <p v-if="log.user?.nim" class="font-mono text-[11px] text-white/30">{{ log.user.nim }}</p>
                                <p v-else class="font-mono text-[11px] text-white/30">sistem / terhapus</p>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="dash-badge capitalize" :class="badgeClass(log.activity)">{{ activityLabel(log.activity) }}</span>
                            </td>
                            <td class="min-w-[240px]">
                                <p class="text-white/80">{{ log.description || '—' }}</p>
                            </td>
                        </tr>
                        <tr v-if="logs.data.length === 0">
                            <td colspan="4" class="px-6 py-14 text-center">
                                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.03]">
                                    <svg class="h-7 w-7 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="font-semibold text-white/60">Belum ada aktivitas tercatat</p>
                                <p class="mt-1 text-sm text-white/30">Coba ubah kata kunci atau reset filter di atas.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="logs.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 px-6 py-4">
                <p class="text-sm text-white/35">
                    Menampilkan {{ logs.from }} - {{ logs.to }} dari {{ logs.total }} aktivitas
                </p>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="page in logs.last_page"
                        :key="page"
                        :href="pageHref(page)"
                        :class="['dash-page-btn', { active: page === logs.current_page }]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>
    </Layout>
</template>
