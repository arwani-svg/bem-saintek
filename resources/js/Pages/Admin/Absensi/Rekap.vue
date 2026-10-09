<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'

const props = defineProps({
    rows: Array,
    filters: Object,
    prodiList: Array,
    totalKegiatan: Number,
    user: Object,
})

const filters = reactive({
    search: props.filters.search || '',
    prodi: props.filters.prodi || '',
    dari: props.filters.dari || '',
    sampai: props.filters.sampai || '',
})

let t = null
const terapkan = () => {
    const params = new URLSearchParams()
    for (const [k, v] of Object.entries(filters)) {
        if (v) params.append(k, v)
    }
    router.visit('/absensi/rekap' + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}
const onSearch = () => {
    clearTimeout(t)
    t = setTimeout(terapkan, 300)
}
const reset = () => {
    Object.keys(filters).forEach((k) => (filters[k] = ''))
    terapkan()
}

const poinBadge = (poin) => {
    if (poin >= 30) return 'bg-rose-300/15 text-rose-300'
    if (poin >= 10) return 'bg-amber-300/10 text-amber-300'
    return 'bg-emerald-300/10 text-emerald-300'
}
</script>

<template>
    <Head title="Rekap Skor Absensi" />

    <Layout
        title="Rekap Skor Pelanggaran"
        :subtitle="`${totalKegiatan} kegiatan · alpa kena poin, izin/sakit/hadir nol`"
        eyebrow="SAINTEK.OS // ABSENSI"
        activeMenu="absensi-rekap"
        :user="user"
        backHref="/kegiatan"
    >
        <div class="dash-card mb-6 p-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div class="md:col-span-2">
                    <label class="dash-label">Cari nama / NIM</label>
                    <input v-model="filters.search" @input="onSearch" type="text" placeholder="cth: arwani / 2024..." class="dash-input" />
                </div>
                <div>
                    <label class="dash-label">Prodi</label>
                    <select v-model="filters.prodi" @change="terapkan" class="dash-input cursor-pointer">
                        <option value="">Semua prodi</option>
                        <option v-for="p in prodiList" :key="p" :value="p">{{ p }}</option>
                    </select>
                </div>
                <div>
                    <label class="dash-label">Dari</label>
                    <input v-model="filters.dari" @change="terapkan" type="date" class="dash-input" />
                </div>
                <div>
                    <label class="dash-label">Sampai</label>
                    <input v-model="filters.sampai" @change="terapkan" type="date" class="dash-input" />
                </div>
            </div>
            <div class="mt-3 flex justify-end">
                <button @click="reset" class="dash-btn-ghost">Reset</button>
            </div>
        </div>

        <div class="dash-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Alpa</th>
                            <th class="text-center">Total Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(r, i) in rows" :key="r.user_id">
                            <td class="font-mono text-xs text-white/30">{{ i + 1 }}</td>
                            <td>
                                <p class="font-medium text-white">{{ r.nama }}</p>
                                <p class="font-mono text-[11px] text-white/35">{{ r.nim }} · {{ r.prodi }} · {{ r.kegiatan }} kegiatan</p>
                            </td>
                            <td class="text-center font-mono text-emerald-300">{{ r.hadir }}</td>
                            <td class="text-center font-mono text-cyan-300">{{ r.izin }}</td>
                            <td class="text-center font-mono text-amber-300">{{ r.sakit }}</td>
                            <td class="text-center font-mono text-rose-300">{{ r.alpa }}</td>
                            <td class="text-center">
                                <span :class="['dash-badge font-mono', poinBadge(r.poin)]">{{ r.poin }} poin</span>
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td colspan="7" class="px-6 py-12 text-center text-white/30">Belum ada data kehadiran pada filter ini.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="mt-3 text-[11px] text-white/30">Catatan: yang belum ditandai tidak masuk rekap — selesaikan absen tiap kegiatan (tandai sisa sebagai alpa) agar skor adil.</p>
    </Layout>
</template>
