
<script setup>
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
})

const ready = ref(false)

onMounted(() => {
    requestAnimationFrame(() => requestAnimationFrame(() => {
        ready.value = true
    }))
})

const colors = {
    masuk: '#34d399',
    keluar: '#f87171',
}

const max = computed(() => Math.max(
    1,
    ...props.items.flatMap(item => [
        Number(item.masuk) || 0,
        Number(item.keluar) || 0,
    ])
))

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value || 0)
}

const formatCompact = (value) => {
    const n = Number(value) || 0

    if (n >= 1_000_000_000) {
        return `Rp${(n / 1_000_000_000).toFixed(1)} M`
    }

    if (n >= 1_000_000) {
        return `Rp${(n / 1_000_000).toFixed(1)} jt`
    }

    if (n >= 1_000) {
        return `Rp${(n / 1_000).toFixed(0)} rb`
    }

    return `Rp${n.toLocaleString('id-ID')}`
}
</script>

<template>
    <div>
        <div v-if="items.length" class="space-y-4">
            <!-- Legend -->
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2 text-xs text-white/50">
                    <span class="h-2.5 w-2.5 rounded-sm"
                        :style="{ background: colors.masuk }"></span>
                    Pemasukan
                </div>

                <div class="flex items-center gap-2 text-xs text-white/50">
                    <span class="h-2.5 w-2.5 rounded-sm"
                        :style="{ background: colors.keluar }"></span>
                    Pengeluaran
                </div>
            </div>

            <!-- Chart -->
            <div class="flex h-[260px] items-end gap-1.5 border-b border-white/10 pb-2 sm:gap-3">
                <div
                    v-for="(item, idx) in items"
                    :key="item.label"
                    class="group flex h-full min-w-0 flex-1 flex-col items-center justify-end"
                >
                    <!-- Tooltip -->
                    <div class="mb-2 hidden flex-col gap-1 rounded-lg border border-white/10 bg-[#171725] p-2 text-[10px] shadow-xl group-hover:flex">
                        <span class="whitespace-nowrap text-emerald-400">
                            Masuk: {{ formatRupiah(item.masuk) }}
                        </span>
                        <span class="whitespace-nowrap text-rose-400">
                            Keluar: {{ formatRupiah(item.keluar) }}
                        </span>
                    </div>

                    <!-- Bars -->
                    <div class="flex w-full flex-1 items-end justify-center gap-1 rounded-t-lg bg-white/[0.015] px-0.5 sm:px-1">
                        <div
                            class="w-1/2 max-w-[32px] rounded-t-md transition-all duration-700 ease-out"
                            :style="{
                                height: ready
                                    ? (item.masuk > 0
                                        ? Math.max((item.masuk / max) * 100, 2)
                                        : 0) + '%'
                                    : '0%',
                                background: `linear-gradient(180deg, ${colors.masuk}, ${colors.masuk}35)`,
                                border: `1px solid ${colors.masuk}66`,
                                boxShadow: `0 0 16px ${colors.masuk}20`,
                                transitionDelay: `${idx * 45}ms`,
                            }"
                        ></div>

                        <div
                            class="w-1/2 max-w-[32px] rounded-t-md transition-all duration-700 ease-out"
                            :style="{
                                height: ready
                                    ? (item.keluar > 0
                                        ? Math.max((item.keluar / max) * 100, 2)
                                        : 0) + '%'
                                    : '0%',
                                background: `linear-gradient(180deg, ${colors.keluar}, ${colors.keluar}35)`,
                                border: `1px solid ${colors.keluar}66`,
                                boxShadow: `0 0 16px ${colors.keluar}20`,
                                transitionDelay: `${idx * 45 + 30}ms`,
                            }"
                        ></div>
                    </div>

                    <!-- Label -->
                    <span class="mt-2 font-mono text-[10px] font-semibold text-white/45 sm:text-xs">
                        {{ item.label }}
                    </span>
                </div>
            </div>

            <!-- Maximum value -->
            <div class="flex justify-between text-[10px] text-white/25">
                <span>Skala maksimal</span>
                <span class="font-mono">{{ formatCompact(max) }}</span>
            </div>
        </div>

        <div v-else class="py-16 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/5 text-white/30">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M3 3v18h18M8 16v-4m4 4V7m4 9v-6m4 6V5"/>
                </svg>
            </div>
            <p class="text-sm text-white/40">Belum ada transaksi pada periode ini.</p>
        </div>
    </div>
</template>
