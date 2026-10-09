<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const props = defineProps({
    kegiatan: Object,
    pesertas: Array,
    filters: Object,
    prodiList: Array,
    ringkasan: Object,
    user: Object,
})

const tanggalIndo = (iso) => {
    if (!iso) return '-'
    const d = new Date(String(iso).substring(0, 10) + 'T00:00:00')
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const filters = reactive({
    search: props.filters.search || '',
    prodi: props.filters.prodi || '',
    status_hadir: props.filters.status_hadir || '',
})

let t = null
const search = () => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.prodi) params.append('prodi', filters.prodi)
    if (filters.status_hadir) params.append('status_hadir', filters.status_hadir)
    router.visit(`/kegiatan/${props.kegiatan.id}` + (params.toString() ? '?' + params.toString() : ''), {
        preserveState: true,
        replace: true,
    })
}
const onSearch = () => {
    clearTimeout(t)
    t = setTimeout(search, 300)
}
const reset = () => {
    filters.search = ''
    filters.prodi = ''
    filters.status_hadir = ''
    search()
}

const badge = (s) => {
    if (s === 'hadir') return 'bg-emerald-300/10 text-emerald-300'
    if (s === 'izin') return 'bg-cyan-300/10 text-cyan-300'
    if (s === 'sakit') return 'bg-amber-300/10 text-amber-300'
    if (s === 'alpa') return 'bg-rose-300/10 text-rose-300'
    return 'bg-white/5 text-white/40'
}
const labelStatus = (s) => s || 'belum'

// --- Tandai cepat per baris (tanpa buka halaman absen) ---
const tandai = (pesertaId, status) => {
    router.post(`/kegiatan/${props.kegiatan.id}/tandai`, { peserta_id: pesertaId, status }, { preserveScroll: true })
}

// --- Tandai sisa sebagai alpa ---
const sisaOpen = ref(false)
const sisaProcessing = ref(false)
const doSisaAlpa = () => {
    sisaProcessing.value = true
    router.post(`/kegiatan/${props.kegiatan.id}/tandai-sisa-alpa`, {}, {
        preserveScroll: true,
        onFinish: () => { sisaProcessing.value = false; sisaOpen.value = false },
    })
}

// --- Tambah peserta ---
const cari = reactive({ search: '' })
const hasilTambah = ref([])
const tambahDipilih = ref(new Map())
const cariTambah = async () => {
    const params = new URLSearchParams()
    if (cari.search) params.append('search', cari.search)
    params.append('except_kegiatan', props.kegiatan.id)
    params.append('limit', '10')
    const res = await fetch('/kegiatan/cari-user?' + params.toString(), { headers: { Accept: 'application/json' } })
    hasilTambah.value = res.ok ? await res.json() : []
}
cariTambah()
let deb2 = null
const onCariTambah = () => {
    clearTimeout(deb2)
    deb2 = setTimeout(cariTambah, 300)
}
const tambahForm = useForm({ user_ids: [] })
const toggleTambah = (u) => {
    if (tambahDipilih.value.has(u.id)) tambahDipilih.value.delete(u.id)
    else tambahDipilih.value.set(u.id, u)
}
const submitTambah = () => {
    tambahForm.user_ids = [...tambahDipilih.value.keys()]
    tambahForm.post(`/kegiatan/${props.kegiatan.id}/peserta`, {
        preserveScroll: true,
        onSuccess: () => { tambahDipilih.value.clear(); tambahForm.reset(); cariTambah() },
    })
}

// --- Hapus peserta ---
const hapusTarget = ref(null)
const confirmHapus = ref(false)
const processing = ref(false)
const doHapus = () => {
    if (!hapusTarget.value) return
    processing.value = true
    router.delete(`/kegiatan/${props.kegiatan.id}/peserta/${hapusTarget.value.id}`, {
        preserveScroll: true,
        onFinish: () => { processing.value = false; confirmHapus.value = false },
    })
}
</script>

<template>
    <Head :title="kegiatan.nama" />

    <Layout
        :title="kegiatan.nama"
        subtitle="Detail peserta + status kehadiran"
        eyebrow="SAINTEK.OS // ABSENSI"
        activeMenu="kegiatan-show"
        :user="user"
        backHref="/kegiatan"
    >
        <!-- Ringkasan -->
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-6">
            <div class="dash-card p-4 text-center">
                <p class="font-mono text-[10px] tracking-[.25em] text-white/40">PESERTA</p>
                <p class="font-display text-2xl font-bold text-white">{{ ringkasan.total }}</p>
            </div>
            <div class="dash-card border-emerald-300/20 p-4 text-center">
                <p class="font-mono text-[10px] tracking-[.25em] text-emerald-300">HADIR</p>
                <p class="font-display text-2xl font-bold text-emerald-300">{{ ringkasan.hadir }}</p>
            </div>
            <div class="dash-card p-4 text-center">
                <p class="font-mono text-[10px] tracking-[.25em] text-cyan-300">IZIN</p>
                <p class="font-display text-2xl font-bold text-cyan-300">{{ ringkasan.izin }}</p>
            </div>
            <div class="dash-card p-4 text-center">
                <p class="font-mono text-[10px] tracking-[.25em] text-amber-300">SAKIT</p>
                <p class="font-display text-2xl font-bold text-amber-300">{{ ringkasan.sakit }}</p>
            </div>
            <div class="dash-card border-rose-300/20 p-4 text-center">
                <p class="font-mono text-[10px] tracking-[.25em] text-rose-300">ALPA</p>
                <p class="font-display text-2xl font-bold text-rose-300">{{ ringkasan.alpa }}</p>
            </div>
            <div class="dash-card p-4 text-center">
                <p class="font-mono text-[10px] tracking-[.25em] text-white/40">BELUM</p>
                <p class="font-display text-2xl font-bold text-white/60">{{ ringkasan.belum }}</p>
            </div>
        </div>

        <div class="mb-6 flex flex-wrap items-center gap-2">
            <Link :href="`/kegiatan/${kegiatan.id}/absen`" class="dash-btn-primary">⚡ Buka Absen Cepat</Link>
            <button v-if="ringkasan.belum > 0" @click="sisaOpen = true" class="dash-btn-ghost">Tandai {{ ringkasan.belum }} sisa sebagai alpa</button>
            <span class="ml-auto text-xs text-white/40">{{ tanggalIndo(kegiatan.tanggal) }}<span v-if="kegiatan.lokasi"> · {{ kegiatan.lokasi }}</span> · alpa = {{ kegiatan.poin_alpa }} poin</span>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="dash-card mb-4 p-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                        <input v-model="filters.search" @input="onSearch" type="text" placeholder="Cari nama / NIM..." class="dash-input sm:col-span-2" />
                        <select v-model="filters.prodi" @change="search" class="dash-input cursor-pointer">
                            <option value="">Semua prodi</option>
                            <option v-for="p in prodiList" :key="p" :value="p">{{ p }}</option>
                        </select>
                        <select v-model="filters.status_hadir" @change="search" class="dash-input cursor-pointer">
                            <option value="">Semua status</option>
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpa">Alpa</option>
                            <option value="belum">Belum ditandai</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div v-for="p in pesertas" :key="p.id" class="dash-card flex flex-wrap items-center gap-3 p-3.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 text-sm font-bold text-black">
                            {{ (p.user?.name || '?').charAt(0).toUpperCase() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-white">{{ p.user?.name }}</p>
                            <p class="truncate font-mono text-[11px] text-white/40">{{ p.user?.nim }} · {{ p.user?.prodi }}</p>
                        </div>
                        <span :class="['dash-badge capitalize', badge(p.status)]">{{ labelStatus(p.status) }}</span>
                        <div class="flex gap-1">
                            <button @click="tandai(p.id, 'hadir')" title="Hadir" class="rounded-lg border border-emerald-300/25 px-2 py-1 text-[11px] font-bold text-emerald-300 transition hover:bg-emerald-300/10">H</button>
                            <button @click="tandai(p.id, 'izin')" title="Izin" class="rounded-lg border border-cyan-300/25 px-2 py-1 text-[11px] font-bold text-cyan-300 transition hover:bg-cyan-300/10">I</button>
                            <button @click="tandai(p.id, 'sakit')" title="Sakit" class="rounded-lg border border-amber-300/25 px-2 py-1 text-[11px] font-bold text-amber-300 transition hover:bg-amber-300/10">S</button>
                            <button @click="tandai(p.id, 'alpa')" title="Alpa" class="rounded-lg border border-rose-300/25 px-2 py-1 text-[11px] font-bold text-rose-300 transition hover:bg-rose-300/10">A</button>
                        </div>
                        <button @click="hapusTarget = p; confirmHapus = true" class="text-xs text-rose-300/60 hover:text-rose-300">×</button>
                    </div>
                    <div v-if="!pesertas.length" class="dash-card p-10 text-center text-white/30">
                        Tidak ada peserta cocok filter. <button @click="reset" class="text-cyan-300 hover:underline">Reset filter</button>
                    </div>
                </div>
            </div>

            <div>
                <div class="dash-card p-5">
                    <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">TAMBAH PESERTA</p>
                    <input v-model="cari.search" @input="onCariTambah" type="text" placeholder="Cari nama / NIM..." class="dash-input mt-3" />
                    <div class="mt-3 max-h-64 space-y-1.5 overflow-y-auto saintek-scroll">
                        <button
                            v-for="u in hasilTambah"
                            :key="u.id"
                            type="button"
                            @click="toggleTambah(u)"
                            :class="tambahDipilih.has(u.id) ? 'border-cyan-300/40 bg-cyan-300/10' : 'border-white/5 hover:border-white/20'"
                            class="flex w-full items-center gap-2 rounded-xl border bg-white/[0.02] px-3 py-2 text-left"
                        >
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold text-white">{{ u.name }}</span>
                                <span class="block truncate font-mono text-[11px] text-white/40">{{ u.nim }} · {{ u.prodi }}</span>
                            </span>
                            <span v-if="tambahDipilih.has(u.id)" class="text-xs font-bold text-cyan-300">✓</span>
                        </button>
                    </div>
                    <button @click="submitTambah" :disabled="!tambahDipilih.size || tambahForm.processing" class="dash-btn-primary mt-3 w-full disabled:opacity-40">
                        {{ tambahForm.processing ? 'Menambah...' : `Tambah (${tambahDipilih.size})` }}
                    </button>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="sisaOpen"
            title="Tandai sisa sebagai alpa?"
            :message="`${ringkasan.belum} peserta yang belum ditandai akan dihitung alpa (+${kegiatan.poin_alpa} poin tiap orang).`"
            itemName=""
            :processing="sisaProcessing"
            @cancel="sisaOpen = false"
            @update:show="sisaOpen = $event"
            @confirm="doSisaAlpa"
        />
        <ConfirmModal
            :show="confirmHapus"
            title="Hapus peserta?"
            message="Data kehadiran orang ini ikut terhapus."
            :itemName="hapusTarget?.user?.name || ''"
            :processing="processing"
            @cancel="confirmHapus = false"
            @update:show="confirmHapus = $event"
            @confirm="doHapus"
        />
    </Layout>
</template>
