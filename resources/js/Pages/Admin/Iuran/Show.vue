<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { computed, reactive, ref } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const props = defineProps({
    iuran: Object,
    pesertas: Array,
    filters: Object,
    prodiList: Array,
    ringkasan: Object,
    user: Object,
})

const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID')
const tanggalIndo = (iso) => {
    if (!iso) return '-'
    const d = new Date(String(iso).substring(0, 10) + 'T00:00:00')
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const filters = reactive({
    search: props.filters.search || '',
    prodi: props.filters.prodi || '',
    status_bayar: props.filters.status_bayar || '',
})

let t = null
const search = () => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.prodi) params.append('prodi', filters.prodi)
    if (filters.status_bayar) params.append('status_bayar', filters.status_bayar)
    router.visit(`/iuran/${props.iuran.id}` + (params.toString() ? '?' + params.toString() : ''), {
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
    filters.status_bayar = ''
    search()
}

const badgeBayar = (s) => {
    if (s === 'lunas') return 'bg-emerald-300/10 text-emerald-300'
    if (s === 'sebagian') return 'bg-amber-300/10 text-amber-300'
    return 'bg-rose-300/10 text-rose-300'
}

// --- Modal bayar (boleh cicil, otomatis masuk kas) ---
const bayarTarget = ref(null)
const bayarForm = useForm({ peserta_id: '', jumlah: '', tanggal: new Date().toISOString().substring(0, 10), keterangan: '' })
const bayarOpen = ref(false)
const bukaBayar = (p) => {
    bayarTarget.value = p
    bayarForm.peserta_id = p.id
    bayarForm.jumlah = p.sisa
    bayarForm.tanggal = new Date().toISOString().substring(0, 10)
    bayarForm.keterangan = ''
    bayarOpen.value = true
}
const submitBayar = () => {
    bayarForm.jumlah = Number(String(bayarForm.jumlah).replace(/[^0-9]/g, '') || 0)
    bayarForm.post(`/iuran/${props.iuran.id}/bayar`, {
        preserveScroll: true,
        onSuccess: () => { bayarOpen.value = false },
    })
}

// --- Tambah peserta ke iuran berjalan ---
const cari = reactive({ search: '', limit: 10 })
const hasilTambah = ref([])
const tambahDipilih = ref(new Map())
const cariTambah = async () => {
    const params = new URLSearchParams()
    if (cari.search) params.append('search', cari.search)
    params.append('except_iuran', props.iuran.id)
    params.append('limit', '10')
    const res = await fetch('/iuran/cari-user?' + params.toString(), { headers: { Accept: 'application/json' } })
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
    tambahForm.post(`/iuran/${props.iuran.id}/peserta`, {
        preserveScroll: true,
        onSuccess: () => { tambahDipilih.value.clear(); tambahForm.reset(); cariTambah() },
    })
}

// --- Hapus ---
const hapusPesertaTarget = ref(null)
const hapusBayarTarget = ref(null)
const confirmPeserta = ref(false)
const confirmBayar = ref(false)
const processing = ref(false)

const doHapusPeserta = () => {
    if (!hapusPesertaTarget.value) return
    processing.value = true
    router.delete(`/iuran/${props.iuran.id}/peserta/${hapusPesertaTarget.value.id}`, {
        preserveScroll: true,
        onFinish: () => { processing.value = false; confirmPeserta.value = false },
    })
}
const doHapusBayar = () => {
    if (!hapusBayarTarget.value) return
    processing.value = true
    router.delete(`/iuran/${props.iuran.id}/bayar/${hapusBayarTarget.value.id}`, {
        preserveScroll: true,
        onFinish: () => { processing.value = false; confirmBayar.value = false },
    })
}

const expanded = ref({})
const toggleExpand = (id) => { expanded.value[id] = !expanded.value[id] }

const progress = computed(() => props.ringkasan.progress || 0)
</script>

<template>
    <Head :title="iuran.nama" />

    <Layout
        :title="iuran.nama"
        subtitle="Detail tagihan — bayar bisa dicicil, otomatis masuk kas"
        eyebrow="SAINTEK.OS // MANAJEMEN IURAN"
        activeMenu="iuran"
        :user="user"
        backHref="/iuran"
    >
        <!-- Ringkasan -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="dash-card border-emerald-300/20 p-5 sm:col-span-2">
                <p class="font-mono text-[10px] tracking-[.3em] text-emerald-300">TERKUMPUL</p>
                <p class="font-display mt-1 text-2xl font-bold text-white">{{ rupiah(ringkasan.terkumpul) }}</p>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/5">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-cyan-300" :style="{ width: Math.min(100, progress) + '%' }"></div>
                </div>
                <p class="mt-1.5 text-xs text-white/40">Target {{ rupiah(ringkasan.target) }} · Sisa {{ rupiah(ringkasan.sisa) }} · {{ progress }}%</p>
            </div>
            <div class="dash-card p-5">
                <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">PESERTA</p>
                <p class="font-display mt-1 text-2xl font-bold text-white">{{ ringkasan.total_peserta }}</p>
                <p class="mt-1 text-xs text-white/40">
                    <span class="text-emerald-300">{{ ringkasan.lunas }} lunas</span> ·
                    <span class="text-amber-300">{{ ringkasan.sebagian }} nyicil</span> ·
                    <span class="text-rose-300">{{ ringkasan.belum }} belum</span>
                </p>
            </div>
            <div class="dash-card flex flex-col justify-between gap-3 p-5">
                <div class="text-xs text-white/40">
                    <p>{{ rupiah(iuran.nominal) }}/orang</p>
                    <p v-if="iuran.jatuh_tempo">Jatuh tempo {{ tanggalIndo(iuran.jatuh_tempo) }}</p>
                    <p v-if="iuran.deskripsi" class="mt-1 line-clamp-2">{{ iuran.deskripsi }}</p>
                </div>
                <Link :href="`/iuran/${iuran.id}/edit`" class="dash-btn-ghost w-full text-center">Edit Iuran</Link>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Daftar peserta -->
            <div class="lg:col-span-2">
                <div class="dash-card mb-4 p-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                        <input v-model="filters.search" @input="onSearch" type="text" placeholder="Cari nama / NIM..." class="dash-input sm:col-span-2" />
                        <select v-model="filters.prodi" @change="search" class="dash-input cursor-pointer">
                            <option value="">Semua prodi</option>
                            <option v-for="p in prodiList" :key="p" :value="p">{{ p }}</option>
                        </select>
                        <select v-model="filters.status_bayar" @change="search" class="dash-input cursor-pointer">
                            <option value="">Semua status</option>
                            <option value="lunas">Lunas</option>
                            <option value="sebagian">Nyicil</option>
                            <option value="belum">Belum bayar</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-3">
                    <div v-for="p in pesertas" :key="p.id" class="dash-card p-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 text-sm font-bold text-black">
                                {{ (p.user?.name || '?').charAt(0).toUpperCase() }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-white">{{ p.user?.name }}</p>
                                <p class="truncate font-mono text-[11px] text-white/40">{{ p.user?.nim }} · {{ p.user?.prodi }}</p>
                                <div class="mt-1.5 h-1.5 w-full max-w-xs overflow-hidden rounded-full bg-white/5">
                                    <div class="h-full rounded-full bg-emerald-400" :style="{ width: Math.min(100, p.nominal_wajib ? Math.round(p.total_bayar / p.nominal_wajib * 100) : 0) + '%' }"></div>
                                </div>
                                <p class="mt-1 font-mono text-[11px] text-white/50">
                                    Sudah {{ rupiah(p.total_bayar) }} · Sisa <span :class="p.sisa === 0 ? 'text-emerald-300' : 'text-amber-300'">{{ rupiah(p.sisa) }}</span>
                                    <span class="text-white/30"> / {{ rupiah(p.nominal_wajib) }}</span>
                                </p>
                            </div>
                            <span :class="['dash-badge capitalize', badgeBayar(p.status_bayar)]">{{ p.status_bayar }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-white/5 pt-3">
                            <button v-if="p.sisa > 0" @click="bukaBayar(p)" class="dash-btn-primary !px-3 !py-1.5 text-xs">+ Catat Bayar</button>
                            <button @click="toggleExpand(p.id)" class="dash-btn-ghost !px-3 !py-1.5 text-xs">
                                {{ expanded[p.id] ? 'Tutup riwayat' : `Riwayat (${p.pembayarans.length})` }}
                            </button>
                            <button @click="hapusPesertaTarget = p; confirmPeserta = true" class="ml-auto text-xs text-rose-300/70 hover:text-rose-300">Keluarkan</button>
                        </div>
                        <div v-if="expanded[p.id]" class="mt-3 space-y-1.5 rounded-xl bg-black/20 p-3">
                            <div v-for="b in p.pembayarans" :key="b.id" class="flex items-center gap-3 text-xs">
                                <span class="font-mono text-white/40">{{ tanggalIndo(b.tanggal) }}</span>
                                <span class="font-mono font-bold text-emerald-300">{{ rupiah(b.jumlah) }}</span>
                                <span class="min-w-0 flex-1 truncate text-white/40">{{ b.keterangan || '-' }}</span>
                                <button @click="hapusBayarTarget = b; confirmBayar = true" class="text-rose-300/60 hover:text-rose-300">hapus</button>
                            </div>
                            <p v-if="!p.pembayarans.length" class="text-center text-xs text-white/30">Belum ada pembayaran.</p>
                        </div>
                    </div>
                    <div v-if="!pesertas.length" class="dash-card p-10 text-center text-white/30">
                        Tidak ada peserta cocok filter. <button @click="reset" class="text-cyan-300 hover:underline">Reset filter</button>
                    </div>
                </div>
            </div>

            <!-- Tambah peserta -->
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
                    <p class="mt-2 text-[11px] text-white/30">Nominal ikut default {{ rupiah(iuran.nominal) }}/orang. Bisa diubah per peserta via database bila perlu dispensasi.</p>
                </div>
            </div>
        </div>

        <!-- Modal bayar -->
        <div v-if="bayarOpen" class="fixed inset-0 z-[90] flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm" @click.self="bayarOpen = false">
            <div class="w-full max-w-md rounded-2xl border border-white/10 bg-[#070b14] p-6">
                <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">CATAT PEMBAYARAN</p>
                <h3 class="font-display mt-1 text-lg font-bold text-white">{{ bayarTarget?.user?.name }}</h3>
                <p class="text-xs text-white/40">Sisa tagihan {{ rupiah(bayarTarget?.sisa) }} · otomatis masuk kas</p>
                <form @submit.prevent="submitBayar" class="mt-4 space-y-4">
                    <div>
                        <label class="dash-label">Jumlah (Rp) *</label>
                        <input v-model="bayarForm.jumlah" type="text" inputmode="numeric" class="dash-input font-mono" required />
                        <p v-if="bayarForm.errors.jumlah" class="dash-error">{{ bayarForm.errors.jumlah }}</p>
                    </div>
                    <div>
                        <label class="dash-label">Tanggal bayar *</label>
                        <input v-model="bayarForm.tanggal" type="date" class="dash-input" required />
                    </div>
                    <div>
                        <label class="dash-label">Keterangan</label>
                        <input v-model="bayarForm.keterangan" type="text" maxlength="255" class="dash-input" placeholder="cth: transfer / tunai" />
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="bayarOpen = false" class="dash-btn-ghost">Batal</button>
                        <button type="submit" :disabled="bayarForm.processing" class="dash-btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <ConfirmModal
            :show="confirmPeserta"
            title="Keluarkan peserta?"
            message="Tagihan + riwayat bayar dan kas terkait ikut terhapus."
            :itemName="hapusPesertaTarget?.user?.name || ''"
            :processing="processing"
            @cancel="confirmPeserta = false"
            @update:show="confirmPeserta = $event"
            @confirm="doHapusPeserta"
        />
        <ConfirmModal
            :show="confirmBayar"
            title="Hapus pembayaran?"
            message="Catatan kas terkait ikut dihapus dan saldo menyesuaikan."
            :itemName="hapusBayarTarget ? rupiah(hapusBayarTarget.jumlah) : ''"
            :processing="processing"
            @cancel="confirmBayar = false"
            @update:show="confirmBayar = $event"
            @confirm="doHapusBayar"
        />
    </Layout>
</template>
