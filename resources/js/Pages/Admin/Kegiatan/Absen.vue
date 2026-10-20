<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue'
import Layout from '@/components/dashboard/Layout.vue'

const props = defineProps({
    kegiatan: Object,
    ringkasan: Object,
    user: Object,
})

// Saran max 12 baris dari server — RAM aman berapa pun jumlah peserta.
const hasil = ref([])
const loading = ref(false)
const stats = reactive({ ...props.ringkasan })
const query = ref('')
const highlight = ref(0)
const inputEl = ref(null)
const recent = ref([]) // [{nama, status, jam}]
const tandaiSisaLoading = ref(false)
const pending = ref(new Set())

const fetchSaran = async () => {
    loading.value = true
    try {
        const params = new URLSearchParams({ limit: '8' })
        if (query.value.trim()) params.append('q', query.value.trim())
        const res = await fetch(`/kegiatan/${props.kegiatan.id}/cari-peserta?` + params.toString(), {
            headers: { Accept: 'application/json' },
        })
        hasil.value = res.ok ? await res.json() : []
        highlight.value = 0
    } catch (e) {
        hasil.value = []
    } finally {
        loading.value = false
    }
}

let deb = null
watch(query, () => {
    clearTimeout(deb)
    deb = setTimeout(fetchSaran, 200)
})

const fokus = () => nextTick(() => inputEl.value?.focus())
onMounted(() => { fetchSaran(); fokus() })

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

const labelStatus = { hadir: 'HADIR', izin: 'IZIN', sakit: 'SAKIT', alpa: 'ALPA' }
const warnaStatus = { hadir: 'text-emerald-300', izin: 'text-cyan-300', sakit: 'text-amber-300', alpa: 'text-rose-300' }

async function tandai(pesertaId, status) {
    if (!pesertaId || pending.value.has(pesertaId)) return
    pending.value.add(pesertaId)
    const idx = hasil.value.findIndex((p) => p.id === pesertaId)
    const sebelumnya = idx >= 0 ? hasil.value[idx].status : null
    if (idx >= 0) hasil.value[idx].status = status // optimistis
    try {
        const res = await fetch(`/kegiatan/${props.kegiatan.id}/tandai`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf(),
            },
            body: JSON.stringify({ peserta_id: pesertaId, status }),
        })
        if (!res.ok) throw new Error('gagal')
        const data = await res.json()
        Object.assign(stats, data.ringkasan)
        const jam = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
        recent.value.unshift({ nama: data.nama, foto_url: data.foto_url || hasil.value[idx]?.user?.foto_url || null, status, jam })
        recent.value = recent.value.slice(0, 10)
        query.value = ''
        fetchSaran() // segarkan antrian sisa
    } catch (e) {
        if (idx >= 0) hasil.value[idx].status = sebelumnya
    } finally {
        pending.value.delete(pesertaId)
        fokus()
    }
}

const tandaiSorot = (status) => {
    const target = hasil.value[highlight.value]
    if (target) tandai(target.id, status)
}

const onKey = (e) => {
    if (e.key === 'ArrowDown') {
        e.preventDefault()
        highlight.value = Math.min(hasil.value.length - 1, highlight.value + 1)
    } else if (e.key === 'ArrowUp') {
        e.preventDefault()
        highlight.value = Math.max(0, highlight.value - 1)
    } else if (e.key === 'Enter') {
        e.preventDefault()
        tandaiSorot('hadir')
    } else if (['2', '3', '4'].includes(e.key) && /[a-zA-Z]/.test(query.value)) {
        // Mode cari nama: angka jadi shortcut status (tanpa mouse).
        // Mode cari NIM (murni angka): angka tetap mengetik seperti biasa.
        e.preventDefault()
        tandaiSorot(e.key === '2' ? 'izin' : e.key === '3' ? 'sakit' : 'alpa')
    }
}

const tandaiSisa = async () => {
    if (!stats.belum) return
    tandaiSisaLoading.value = true
    try {
        const res = await fetch(`/kegiatan/${props.kegiatan.id}/tandai-sisa-alpa`, {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
        })
        if (res.ok) {
            const data = await res.json()
            Object.assign(stats, data.ringkasan)
            fetchSaran()
        }
    } finally {
        tandaiSisaLoading.value = false
        fokus()
    }
}

const progress = computed(() => {
    const total = stats.total || 0
    const sudah = total - (stats.belum || 0)
    return total ? Math.round((sudah / total) * 100) : 0
})
</script>

<template>
    <Head :title="`Absen — ${kegiatan.nama}`" />

    <Layout
        title="Absen Cepat"
        :subtitle="kegiatan.nama"
        eyebrow="SAINTEK.OS // ABSENSI LIVE"
        activeMenu="kegiatan-absen"
        :user="user"
        :backHref="`/kegiatan/${kegiatan.id}`"
    >
        <!-- Statistik live -->
        <div class="mb-5 grid grid-cols-3 gap-3 sm:grid-cols-6">
            <div class="dash-card p-3 text-center"><p class="font-mono text-[9px] tracking-[.2em] text-white/40">TOTAL</p><p class="font-display text-xl font-bold">{{ stats.total }}</p></div>
            <div class="dash-card border-emerald-300/25 p-3 text-center"><p class="font-mono text-[9px] tracking-[.2em] text-emerald-300">HADIR</p><p class="font-display text-xl font-bold text-emerald-300">{{ stats.hadir }}</p></div>
            <div class="dash-card p-3 text-center"><p class="font-mono text-[9px] tracking-[.2em] text-cyan-300">IZIN</p><p class="font-display text-xl font-bold text-cyan-300">{{ stats.izin }}</p></div>
            <div class="dash-card p-3 text-center"><p class="font-mono text-[9px] tracking-[.2em] text-amber-300">SAKIT</p><p class="font-display text-xl font-bold text-amber-300">{{ stats.sakit }}</p></div>
            <div class="dash-card border-rose-300/25 p-3 text-center"><p class="font-mono text-[9px] tracking-[.2em] text-rose-300">ALPA</p><p class="font-display text-xl font-bold text-rose-300">{{ stats.alpa }}</p></div>
            <div class="dash-card p-3 text-center"><p class="font-mono text-[9px] tracking-[.2em] text-white/40">SISA</p><p class="font-display text-xl font-bold text-white/60">{{ stats.belum }}</p></div>
        </div>
        <div class="mb-5 h-2 overflow-hidden rounded-full bg-white/5">
            <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-cyan-300 transition-all duration-300" :style="{ width: progress + '%' }"></div>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <!-- Terminal input -->
            <div class="lg:col-span-2">
                <div class="dash-card p-5">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60"></span><span class="relative inline-flex h-3 w-3 rounded-full bg-emerald-400"></span></span>
                        <p class="font-mono text-[11px] tracking-[.25em] text-emerald-300">MODE ABSEN — FOKUS DI SINI, TANGAN DI KEYBOARD</p>
                    </div>

                    <input
                        ref="inputEl"
                        v-model="query"
                        @keydown="onKey"
                        type="text"
                        autocomplete="off"
                        spellcheck="false"
                        placeholder="Ketik nama / NIM… cth: arw"
                        class="dash-input mt-4 !py-4 text-center font-mono text-2xl tracking-wide"
                    />

                    <div class="mt-3 flex flex-wrap gap-1.5 font-mono text-[11px]">
                        <span class="rounded-md border border-emerald-300/30 bg-emerald-300/10 px-2 py-1 text-emerald-200">Enter = Hadir</span>
                        <span class="rounded-md border border-cyan-300/30 bg-cyan-300/10 px-2 py-1 text-cyan-200">2 = Izin</span>
                        <span class="rounded-md border border-amber-300/30 bg-amber-300/10 px-2 py-1 text-amber-200">3 = Sakit</span>
                        <span class="rounded-md border border-rose-300/30 bg-rose-300/10 px-2 py-1 text-rose-200">4 = Alpa</span>
                        <span class="rounded-md border border-white/10 px-2 py-1 text-white/40">↑↓ = pilih</span>
                    </div>
                    <p class="mt-2 text-[11px] text-white/30">Tips: saat cari pakai NIM (angka), shortcut 2/3/4 nonaktif agar tidak bentrok mengetik — pakai tombol status di baris atau Enter untuk hadir.</p>

                    <!-- Saran (max 8 dari server) -->
                    <div class="mt-4 space-y-2">
                        <div
                            v-for="(p, i) in hasil"
                            :key="p.id"
                            :class="i === highlight ? 'border-emerald-300/60 bg-emerald-300/[0.07] shadow-[0_0_24px_rgba(52,211,153,0.12)]' : 'border-white/5 bg-white/[0.02]'"
                            class="flex items-center gap-3 rounded-xl border px-4 py-3 transition-all"
                        >
                            <img
                                v-if="p.user?.foto_url"
                                :src="p.user.foto_url"
                                :alt="p.user?.name"
                                class="h-11 w-11 shrink-0 rounded-xl border border-white/20 object-cover"
                                loading="lazy"
                            />
                            <span v-else :class="i === highlight ? 'bg-emerald-300 text-black' : 'bg-white/10 text-white/40'" class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md font-mono text-xs font-bold">{{ i + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-white">{{ p.user?.name }} <span class="font-mono text-[11px] font-normal text-white/35">{{ p.user?.nim }}</span></p>
                                <p class="font-mono text-[11px] text-white/35">{{ p.user?.prodi }} · <span :class="warnaStatus[p.status] || 'text-white/35'">{{ p.status ? labelStatus[p.status] : 'BELUM' }}</span></p>
                            </div>
                            <div class="flex shrink-0 gap-1" @mousedown.prevent>
                                <button @click="tandai(p.id, 'hadir')" title="Hadir (Enter)" class="rounded-lg border border-emerald-300/25 px-2 py-1 text-[11px] font-bold text-emerald-300 hover:bg-emerald-300/10">H</button>
                                <button @click="tandai(p.id, 'izin')" title="Izin (2)" class="rounded-lg border border-cyan-300/25 px-2 py-1 text-[11px] font-bold text-cyan-300 hover:bg-cyan-300/10">I</button>
                                <button @click="tandai(p.id, 'sakit')" title="Sakit (3)" class="rounded-lg border border-amber-300/25 px-2 py-1 text-[11px] font-bold text-amber-300 hover:bg-amber-300/10">S</button>
                                <button @click="tandai(p.id, 'alpa')" title="Alpa (4)" class="rounded-lg border border-rose-300/25 px-2 py-1 text-[11px] font-bold text-rose-300 hover:bg-rose-300/10">A</button>
                            </div>
                        </div>
                        <p v-if="!hasil.length && !loading" class="rounded-xl border border-white/5 py-8 text-center text-sm text-white/30">
                            {{ query ? 'Tidak ketemu. Coba kata kunci lain.' : 'Semua peserta sudah ditandai 🎉' }}
                        </p>
                        <p v-if="loading" class="py-2 text-center font-mono text-xs text-white/30">mencari…</p>
                    </div>
                </div>
            </div>

            <!-- Samping: baru ditandai + aksi -->
            <div class="space-y-5">
                <div class="dash-card p-5">
                    <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">BARU DITANDAI</p>
                    <div class="mt-3 space-y-1.5">
                        <div v-for="(r, i) in recent" :key="i" class="flex items-center gap-2 rounded-lg bg-white/[0.03] px-3 py-2 text-xs">
                            <img
                                v-if="r.foto_url"
                                :src="r.foto_url"
                                :alt="r.nama"
                                class="h-7 w-7 shrink-0 rounded-full border border-white/20 object-cover"
                                loading="lazy"
                            />
                            <span
                                v-else
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 text-[11px] font-bold text-black"
                            >
                                {{ (r.nama || '?').charAt(0).toUpperCase() }}
                            </span>
                            <span class="min-w-0 flex-1 truncate font-semibold text-white">{{ r.nama }}</span>
                            <span :class="['font-mono font-bold', warnaStatus[r.status]]">{{ labelStatus[r.status] }}</span>
                            <span class="font-mono text-white/30">{{ r.jam }}</span>
                        </div>
                        <p v-if="!recent.length" class="py-4 text-center text-xs text-white/30">Belum ada. Ketik + Enter untuk mulai.</p>
                    </div>
                </div>
                <div class="dash-card space-y-2 p-5">
                    <button @click="tandaiSisa" :disabled="!stats.belum || tandaiSisaLoading" class="dash-btn-ghost w-full disabled:opacity-40">
                        {{ tandaiSisaLoading ? 'Memproses...' : `Tandai ${stats.belum} sisa sebagai alpa` }}
                    </button>
                    <Link :href="`/kegiatan/${kegiatan.id}`" class="dash-btn-ghost block w-full text-center">← Kembali ke Detail</Link>
                </div>
            </div>
        </div>
    </Layout>
</template>
