<script setup>
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
    segments: {
        type: Array,
        default: () => [], // [{ label, value, color }]
    },
    centerLabel: {
        type: String,
        default: 'Total',
    },
    size: {
        type: Number,
        default: 180,
    },
})

const ready = ref(false)
const active = ref(-1)

onMounted(() => {
    requestAnimationFrame(() => requestAnimationFrame(() => {
        ready.value = true
    }))
})

const total = computed(() => props.segments.reduce((a, s) => a + (s.value || 0), 0))

const R = 70
const C = 2 * Math.PI * R

const parts = computed(() => {
    let acc = 0
    return props.segments.map((s) => {
        const frac = total.value ? (s.value || 0) / total.value : 0
        const gap = frac > 0 && frac < 1 ? 3 : 0
        const len = Math.max(frac * C - gap, 0)
        const p = { ...s, frac, len, offset: acc }
        acc += frac * C
        return p
    })
})

const pct = (frac) => `${Math.round(frac * 100)}%`
</script>

<template>
    <div class="flex flex-col items-center gap-5 sm:flex-row sm:gap-6">
        <div
            class="relative shrink-0 transition-all duration-700"
            :class="ready ? 'scale-100 opacity-100' : 'scale-90 opacity-0'"
            :style="{ width: size + 'px', height: size + 'px' }"
        >
            <svg :viewBox="`0 0 180 180`" :width="size" :height="size">
                <circle cx="90" cy="90" :r="R" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="26" />
                <g transform="rotate(-90 90 90)">
                    <circle
                        v-for="(p, i) in parts"
                        :key="p.label"
                        cx="90"
                        cy="90"
                        :r="R"
                        fill="none"
                        :stroke="p.color"
                        :stroke-width="active === i ? 33 : 26"
                        :stroke-dasharray="`${p.len} ${C - p.len}`"
                        :stroke-dashoffset="-p.offset"
                        stroke-linecap="round"
                        class="cursor-pointer transition-all duration-300"
                        :opacity="active === -1 || active === i ? 1 : 0.35"
                        @mouseenter="active = i"
                        @mouseleave="active = -1"
                    >
                        <title>{{ p.label }}: {{ p.value }} ({{ pct(p.frac) }})</title>
                    </circle>
                </g>
            </svg>
            <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                <template v-if="active === -1">
                    <p class="font-display text-3xl font-bold text-white">{{ total }}</p>
                    <p class="font-mono text-[10px] tracking-[.25em] text-white/35">{{ centerLabel.toUpperCase() }}</p>
                </template>
                <template v-else>
                    <p class="font-display text-3xl font-bold" :style="{ color: parts[active].color }">
                        {{ pct(parts[active].frac) }}
                    </p>
                    <p class="max-w-[110px] truncate font-mono text-[10px] tracking-[.2em] text-white/50">
                        {{ parts[active].label.toUpperCase() }}
                    </p>
                </template>
            </div>
        </div>

        <div class="w-full min-w-0 flex-1 space-y-2.5">
            <button
                v-for="(p, i) in parts"
                :key="p.label"
                class="flex w-full items-center gap-3 rounded-xl border px-3 py-2 text-left transition-all"
                :class="active === i
                    ? 'border-white/20 bg-white/[0.05]'
                    : 'border-transparent hover:border-white/10 hover:bg-white/[0.03]'"
                @mouseenter="active = i"
                @mouseleave="active = -1"
            >
                <span class="h-3 w-3 shrink-0 rounded-full" :style="{ background: p.color, boxShadow: `0 0 12px ${p.color}66` }"></span>
                <span class="min-w-0 flex-1 truncate text-sm font-medium text-white/70">{{ p.label }}</span>
                <span class="font-display text-sm font-bold text-white">{{ p.value }}</span>
                <span class="w-11 text-right font-mono text-xs text-white/35">{{ pct(p.frac) }}</span>
            </button>
        </div>
    </div>
</template>
