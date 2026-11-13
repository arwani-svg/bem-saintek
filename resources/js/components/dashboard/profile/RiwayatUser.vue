<script setup>
defineProps({
    riwayatKegiatan: {
        type: Array,
        default: () => [],
    },
    tagihanIuran: {
        type: Array,
        default: () => [],
    },
    totalPoin: {
        type: Number,
        default: 0,
    },
    totalSisaIuran: {
        type: Number,
        default: 0,
    },
})

const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID')
const tanggalIndo = (iso) => {
    if (!iso) return '-'
    const d = new Date(String(iso).substring(0, 10) + 'T00:00:00')
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const badgeHadir = (s) => {
    if (s === 'hadir') return 'bg-emerald-300/10 text-emerald-300'
    if (s === 'izin') return 'bg-cyan-300/10 text-cyan-300'
    if (s === 'sakit') return 'bg-amber-300/10 text-amber-300'
    if (s === 'alpa') return 'bg-rose-300/10 text-rose-300'
    return 'bg-white/5 text-white/40'
}
const badgeIuran = (s) => {
    if (s === 'lunas') return 'bg-emerald-300/10 text-emerald-300'
    if (s === 'sebagian') return 'bg-amber-300/10 text-amber-300'
    return 'bg-rose-300/10 text-rose-300'
}
</script>

<template>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Riwayat kegiatan -->
        <div class="dash-card p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">KEGIATAN SAYA</p>
                <span class="dash-badge font-mono" :class="totalPoin > 0 ? 'bg-rose-300/10 text-rose-300' : 'bg-emerald-300/10 text-emerald-300'">
                    {{ totalPoin }} poin pelanggaran
                </span>
            </div>
            <div class="mt-4 max-h-96 space-y-2 overflow-y-auto pr-1 saintek-scroll">
                <div v-for="r in riwayatKegiatan" :key="r.kegiatan_id" class="rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3">
                    <div class="flex items-center gap-2">
                        <p class="min-w-0 flex-1 truncate text-sm font-semibold text-white">{{ r.nama }}</p>
                        <span :class="['dash-badge capitalize', badgeHadir(r.status_hadir)]">{{ r.status_hadir || 'belum ditandai' }}</span>
                    </div>
                    <p class="mt-1 font-mono text-[11px] text-white/40">
                        {{ tanggalIndo(r.tanggal) }}
                        <span v-if="r.poin > 0" class="text-rose-300">· +{{ r.poin }} poin</span>
                        <span v-if="!r.status_hadir" class="text-amber-300">· belum diabsen — ingatkan panitia</span>
                    </p>
                </div>
                <p v-if="!riwayatKegiatan.length" class="py-8 text-center text-sm text-white/30">Belum terdaftar di kegiatan apa pun.</p>
            </div>
        </div>

        <!-- Tagihan iuran -->
        <div class="dash-card p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">TAGIHAN IURAN SAYA</p>
                <span class="dash-badge font-mono" :class="totalSisaIuran > 0 ? 'bg-amber-300/10 text-amber-300' : 'bg-emerald-300/10 text-emerald-300'">
                    sisa {{ rupiah(totalSisaIuran) }}
                </span>
            </div>
            <div class="mt-4 max-h-96 space-y-2 overflow-y-auto pr-1 saintek-scroll">
                <div v-for="t in tagihanIuran" :key="t.iuran_id" class="rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3">
                    <div class="flex items-center gap-2">
                        <p class="min-w-0 flex-1 truncate text-sm font-semibold text-white">{{ t.nama }}</p>
                        <span :class="['dash-badge capitalize', badgeIuran(t.status_bayar)]">{{ t.status_bayar }}</span>
                    </div>
                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white/5">
                        <div class="h-full rounded-full bg-emerald-400" :style="{ width: (t.nominal_wajib ? Math.min(100, Math.round(t.total_bayar / t.nominal_wajib * 100)) : 0) + '%' }"></div>
                    </div>
                    <p class="mt-1 font-mono text-[11px] text-white/40">
                        Sudah {{ rupiah(t.total_bayar) }} · Sisa <span :class="t.sisa === 0 ? 'text-emerald-300' : 'text-amber-300'">{{ rupiah(t.sisa) }}</span>
                        <span class="text-white/25"> / {{ rupiah(t.nominal_wajib) }}</span>
                    </p>
                </div>
                <p v-if="!tagihanIuran.length" class="py-8 text-center text-sm text-white/30">Tidak ada tagihan iuran.</p>
            </div>
        </div>
    </div>
</template>
