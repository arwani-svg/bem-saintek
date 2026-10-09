<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'

defineProps({
    userName: {
        type: String,
        default: '',
    },
})

const now = ref(new Date())
let timer = null

onMounted(() => {
    timer = setInterval(() => {
        now.value = new Date()
    }, 1000)
})

onUnmounted(() => {
    if (timer) clearInterval(timer)
})

const hour = computed(() => now.value.getHours())

const moment = computed(() => {
    const h = hour.value
    if (h >= 4 && h < 11)
        return {
            key: 'pagi',
            greet: 'Selamat pagi',
            text: 'Awali hari dengan memeriksa ringkasan sistem sebelum beraktivitas.',
            icon: '🌅',
            glow: 'bg-amber-300/[0.07]',
            orb: 'bg-amber-300/20',
        }
    if (h >= 11 && h < 15)
        return {
            key: 'siang',
            greet: 'Selamat siang',
            text: 'Tetap fokus — pantau aktivitas dan progres BEM siang ini.',
            icon: '☀️',
            glow: 'bg-cyan-300/[0.07]',
            orb: 'bg-cyan-300/20',
        }
    if (h >= 15 && h < 18)
        return {
            key: 'sore',
            greet: 'Selamat sore',
            text: 'Saatnya meninjau progres hari ini sebelum waktu pulang tiba.',
            icon: '🌇',
            glow: 'bg-orange-300/[0.07]',
            orb: 'bg-orange-300/20',
        }
    return {
        key: 'malam',
        greet: 'Selamat malam',
        text: 'Mode malam aktif. Cek hal penting, sisanya bisa lanjut besok.',
        icon: '🌙',
        glow: 'bg-violet-400/[0.08]',
        orb: 'bg-violet-400/20',
    }
})

const timeStr = computed(() =>
    now.value.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false })
)

const dateStr = computed(() =>
    now.value.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
)
</script>

<template>
    <div class="dash-card relative mb-6 overflow-hidden">
        <div class="pointer-events-none absolute inset-0 transition-colors duration-700" :class="moment.glow"></div>
        <div
            class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full blur-[90px] transition-colors duration-700"
            :class="moment.orb"
        ></div>

        <div class="relative flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-7">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-300 opacity-60"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-300"></span>
                    </span>
                    <p class="font-mono text-[10px] tracking-[.3em] text-emerald-300/90">LIVE // {{ moment.key.toUpperCase() }}</p>
                </div>
                <h2 class="font-display mt-2 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                    {{ moment.greet }}<span class="text-cyan-300">,</span>
                    {{ userName || 'Admin' }} <span class="ml-1">{{ moment.icon }}</span>
                </h2>
                <p class="mt-1 max-w-xl truncate text-sm text-white/40 sm:whitespace-normal">{{ moment.text }}</p>
            </div>

            <div class="shrink-0 text-left sm:text-right">
                <p class="font-mono text-4xl font-medium tabular-nums tracking-tight text-white sm:text-5xl">
                    {{ timeStr }}
                </p>
                <p class="mt-1 font-mono text-[11px] uppercase tracking-[.2em] text-white/40">
                    {{ dateStr }}
                </p>
            </div>
        </div>
    </div>
</template>
