<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/components/dashboard/Layout.vue'

const props = defineProps({
    pendaftaran: Object,
    user: Object,
})

const listOf = (list, raw) => {
    if (Array.isArray(list) && list.length > 0) return list
    if (typeof raw !== 'string' || raw.trim() === '') return []
    return raw.trim().split(/\r\n|\r|\n/).map((v) => v.trim()).filter((v) => v !== '')
}

const sekolah = computed(() => listOf(props.pendaftaran.riwayat_sekolah_list, props.pendaftaran.riwayat_sekolah))
const madrasah = computed(() => listOf(props.pendaftaran.riwayat_madrasah_list, props.pendaftaran.riwayat_madrasah))
const organisasi = computed(() => listOf(props.pendaftaran.riwayat_organisasi_list, props.pendaftaran.riwayat_organisasi))

const ttl = computed(() => {
    const t = props.pendaftaran.tempat_lahir || ''
    const d = props.pendaftaran.tanggal_lahir ? String(props.pendaftaran.tanggal_lahir).substring(0, 10) : ''
    if (!t && !d) return '-'
    return [t, d].filter(Boolean).join(', ')
})

const statusColors = {
    pending: 'bg-amber-300/10 text-amber-300',
    diterima: 'bg-emerald-300/10 text-emerald-300',
    ditolak: 'bg-red-400/10 text-red-300',
}
</script>

<template>
    <Head :title="`Pendaftaran - ${pendaftaran.nama}`" />

    <Layout
        :title="pendaftaran.nama"
        subtitle="Detail pendaftaran pengurus"
        eyebrow="SAINTEK.OS // PENDAFTARAN"
        activeMenu="pendaftaran"
        :user="user"
        backHref="/pendaftaran"
    >
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <Link href="/pendaftaran" class="rounded-xl border border-white/10 p-2 text-white/60 transition hover:border-cyan-300/30 hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </Link>
                    <div>
                        <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">SAINTEK.OS // PENDAFTARAN</p>
                        <h1 class="font-display text-xl font-bold tracking-tight text-white">{{ pendaftaran.nama }}</h1>
                        <p class="font-mono text-xs text-white/40">{{ pendaftaran.nim }} • Semester {{ pendaftaran.semester }} • <span :class="[statusColors[pendaftaran.status], 'dash-badge capitalize']">{{ pendaftaran.status }}</span></p>
                    </div>
                </div>
                <Link v-if="pendaftaran.status === 'pending'" :href="`/pendaftaran/${pendaftaran.id}/edit`" class="dash-btn-primary">Edit</Link>
            </div>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="dash-card space-y-4 p-6">
                <h3 class="font-display text-lg font-bold text-white">Data Diri</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-white/40">Nama</dt><dd class="text-right font-medium text-white">{{ pendaftaran.nama }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-white/40">NPM/NIM</dt><dd class="font-mono text-right text-white">{{ pendaftaran.nim }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-white/40">Prodi</dt><dd class="text-right text-white">{{ pendaftaran.prodi }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-white/40">Semester</dt><dd class="text-right text-white">{{ pendaftaran.semester }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-white/40">TTL</dt><dd class="text-right text-white">{{ ttl }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-white/40">Asrama</dt><dd class="text-right text-white">{{ pendaftaran.asrama || '-' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-white/40">Email</dt><dd class="break-all text-right text-white">{{ pendaftaran.email || '-' }}</dd></div>
                </dl>
                <div>
                    <p class="text-white/40 text-sm">Tujuan</p>
                    <p class="mt-1 text-sm leading-relaxed text-white/80">{{ pendaftaran.tujuan || '-' }}</p>
                </div>
                <div v-if="pendaftaran.catatan_seleksi">
                    <p class="text-white/40 text-sm">Catatan Seleksi</p>
                    <p class="mt-1 rounded-xl border border-white/10 bg-white/[0.02] p-3 text-sm text-white/80">{{ pendaftaran.catatan_seleksi }}</p>
                </div>
            </div>

            <div class="space-y-6">
                <div v-for="section in [
                    { title: 'Riwayat Pendidikan Sekolah', items: sekolah },
                    { title: 'Riwayat Pendidikan Madrasah', items: madrasah },
                    { title: 'Riwayat Organisasi', items: organisasi },
                ]" :key="section.title" class="dash-card space-y-3 p-6">
                    <h3 class="font-display text-lg font-bold text-white">{{ section.title }} <span class="ml-1 font-mono text-xs font-normal text-cyan-300">{{ section.items.length }} point</span></h3>
                    <ol v-if="section.items.length > 0" class="space-y-2.5">
                        <li v-for="(point, idx) in section.items" :key="idx" class="flex items-start gap-2.5 rounded-xl border border-white/5 bg-white/[0.02] p-3 text-sm text-white/80">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-300/10 font-mono text-xs font-bold text-cyan-300">{{ idx + 1 }}</span>
                            <span>{{ point }}</span>
                        </li>
                    </ol>
                    <p v-else class="text-sm text-white/30">Belum diisi.</p>
                </div>
            </div>
        </div>
    </Layout>
</template>
