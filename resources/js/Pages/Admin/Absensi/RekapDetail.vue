<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'

const props = defineProps({
    detailUser: Object,
    ringkasan: Object,
    rows: Array,
    filters: Object,
    user: Object,
})

const filters = reactive({
    dari: props.filters.dari || '',
    sampai: props.filters.sampai || '',
})

const terapkan = () => {
    const params = new URLSearchParams()
    if (filters.dari) params.append('dari', filters.dari)
    if (filters.sampai) params.append('sampai', filters.sampai)
    router.visit(`/absensi/rekap/${props.detailUser.id}` + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}
const reset = () => {
    filters.dari = ''
    filters.sampai = ''
    terapkan()
}

const tanggalIndo = (iso) => {
    if (!iso) return '-'
    return new Date(String(iso).substring(0, 10) + 'T00:00:00')
        .toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const statusBadge = (s) => ({
    hadir: 'bg-emerald-300/10 text-emerald-300',
    izin: 'bg-cyan-300/10 text-cyan-300',
    sakit: 'bg-amber-300/10 text-amber-300',
    alpa: 'bg-rose-300/10 text-rose-300',
    belum: 'bg-white/5 text-white/40',
}[s] || 'bg-white/5 text-white/40')

const stats = [
    { label: 'Hadir', value: props.ringkasan.hadir, cls: 'text-emerald-300' },
    { label: 'Izin', value: props.ringkasan.izin, cls: 'text-cyan-300' },
    { label: 'Sakit', value: props.ringkasan.sakit, cls: 'text-amber-300' },
    { label: 'Alpa', value: props.ringkasan.alpa, cls: 'text-rose-300' },
    { label: 'Belum ditandai', value: props.ringkasan.belum, cls: 'text-white/50' },
    { label: 'Total poin', value: `${props.ringkasan.poin} poin`, cls: 'text-white' },
]
</script>

<template>
    <Head :title="`Rekap ${detailUser.nama}`" />

    <Layout
        :title="detailUser.nama"
        :subtitle="`${detailUser.nim} · ${detailUser.prodi} · smt ${detailUser.semester ?? '-'} · ${ringkasan.kegiatan} kegiatan`"
        eyebrow="SAINTEK.OS // ABSENSI"
        activeMenu="absensi-rekap"
        :user="user"
        backHref="/absensi/rekap"
    >
        <div class="dash-card mb-4 flex items-center gap-4 p-5">
            <img
                v-if="detailUser.foto_url"
                :src="detailUser.foto_url"
                :alt="detailUser.nama"
                class="h-16 w-16 shrink-0 rounded-2xl border border-white/20 object-cover"
            />
            <div
                v-else
                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-violet-500 font-display text-2xl font-bold text-black"
            >
                {{ (detailUser.nama || '?').charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0">
                <p class="truncate font-display text-lg font-bold text-white">{{ detailUser.nama }}</p>
                <p class="font-mono text-xs text-white/40">{{ detailUser.nim }} · {{ detailUser.prodi }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <div v-for="s in stats" :key="s.label" class="dash-card p-4 text-center">
                <p class="font-mono text-[10px] tracking-[.2em] text-white/40">{{ s.label.toUpperCase() }}</p>
                <p :class="['font-display mt-1 text-xl font-bold', s.cls]">{{ s.value }}</p>
            </div>
        </div>

        <div class="dash-card mt-4 p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="dash-label">Dari tanggal</label>
                    <input v-model="filters.dari" @change="terapkan" type="date" class="dash-input" />
                </div>
                <div>
                    <label class="dash-label">Sampai tanggal</label>
                    <input v-model="filters.sampai" @change="terapkan" type="date" class="dash-input" />
                </div>
                <div class="flex items-end">
                    <button @click="reset" class="dash-btn-ghost w-full">Reset</button>
                </div>
            </div>
        </div>

        <div class="dash-card mt-4 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kegiatan</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th class="text-center">Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="r in rows" :key="r.kegiatan_id">
                            <td class="whitespace-nowrap text-white/60">{{ tanggalIndo(r.tanggal) }}</td>
                            <td>
                                <Link
                                    v-if="r.kegiatan_id"
                                    :href="`/kegiatan/${r.kegiatan_id}`"
                                    class="font-medium text-white hover:text-cyan-300 hover:underline"
                                >
                                    {{ r.nama }}
                                </Link>
                                <span v-else class="text-white/60">{{ r.nama }}</span>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="dash-badge capitalize" :class="statusBadge(r.status)">{{ r.status }}</span>
                            </td>
                            <td class="min-w-[160px] text-white/50">{{ r.keterangan || '—' }}</td>
                            <td class="text-center font-mono" :class="r.poin ? 'text-rose-300' : 'text-white/30'">
                                {{ r.poin }}
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td colspan="5" class="px-6 py-12 text-center text-white/30">Belum ada kegiatan pada rentang ini.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="mt-3 text-[11px] text-white/30">Poin hanya dari status alpa (poin per kegiatan). Izin/sakit/hadir = 0 poin.</p>
    </Layout>
</template>
