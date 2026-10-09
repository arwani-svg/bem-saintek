<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    pendaftaran: Object,
    user: Object,
})
</script>

<template>
    <Head title="Pendaftaran Terkirim" />

    <div class="bem-saintek relative min-h-screen bg-[#03050a] font-sans text-white antialiased">
        <div class="noise"></div>
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-40 left-1/3 h-[380px] w-[680px] rounded-full bg-emerald-400/[0.06] blur-[130px]"></div>
        </div>

        <div class="relative mx-auto max-w-xl px-5 py-16 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border border-emerald-300/30 bg-emerald-300/10 text-4xl">✓</div>
            <p class="mt-6 font-mono text-[10px] tracking-[.35em] text-emerald-300">PENDAFTARAN TERKIRIM</p>
            <h1 class="font-display mt-3 text-3xl font-bold tracking-tight md:text-4xl">Makasih, {{ pendaftaran.nama }}!</h1>
            <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-white/40">
                Pendaftaranmu <span class="font-mono text-white/70">{{ pendaftaran.nim }}</span>
                <span v-if="pendaftaran.gelombang">untuk <span class="text-white/70">{{ pendaftaran.gelombang.nama }}</span></span>
                sudah kami terima dengan status <span class="dash-badge bg-amber-300/10 text-amber-300">pending</span>.
                Pantau statusnya lewat halaman pendaftaranku.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <Link href="/" class="dash-btn-ghost">← Kembali ke Beranda</Link>
                <Link href="/pendaftaran" class="dash-btn-primary">Lihat Pendaftaranku</Link>
            </div>
        </div>
    </div>
</template>
