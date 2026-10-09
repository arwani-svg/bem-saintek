<script setup>
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
    labels: {
        type: Array,
        default: () => [],
    },
    series: {
        type: Array,
        default: () => [], // [{ label, color, data: [] }]
    },
})

const ready = ref(false)
onMounted(() => {
    requestAnimationFrame(() => requestAnimationFrame(() => {
        ready.value = true
    }))
})

const W = 620
const H = 260
const PAD_L = 34
const PAD_R = 8
const PAD_T = 14
const PAD_B = 30

const max = computed(() => Math.max(1, ...props.series.flatMap((s) => s.data ?? [])))
const innerW = W - PAD_L - PAD_R
const innerH = H - PAD_T - PAD_B
const count = computed(() => props.labels.length || 1)
const groupW = computed(() => innerW / count.value)
const barW = computed(() =>
    Math.max(8, Math.min(24, (groupW.value - 14) / Math.max(props.series.length, 1)))
)

const baseY = PAD_T + innerH
const yOf = (v) => PAD_T + innerH - (v / max.value) * innerH

const ticks = computed(() =>
    [0, 0.5, 1].map((f) => ({ v: Math.round(max.value * f), y: yOf(max.value * f) }))
)

const barX = (gi, si) => {
    const start = PAD_L + gi * groupW.value
    const total = barW.value * props.series.length
    return start + (groupW.value - total) / 2 + si * barW.value
}

const barH = (v) => {
    if (!v || v <= 0) return 0
    return Math.max(3, baseY - yOf(v))
}
</script>

<template>
    <div>
        <div class="flex flex-wrap items-center gap-4">
            <span v-for="s in series" :key="s.label" class="flex items-center gap-1.5 text-xs text-white/50">
                <span class="h-2.5 w-2.5 rounded-sm" :style="{ background: s.color }"></span>
                {{ s.label }}
            </span>
        </div>

        <svg :viewBox="`0 0 ${W} ${H}`" class="mt-2 w-full" role="img" aria-label="Grafik batang aktivitas">
            <line
                v-for="t in ticks"
                :key="t.v"
                :x1="PAD_L"
                :x2="W - PAD_R"
                :y1="t.y"
                :y2="t.y"
                stroke="rgba(255,255,255,0.07)"
                stroke-dasharray="3 4"
            />
            <text
                v-for="t in ticks"
                :key="'lbl' + t.v"
                :x="PAD_L - 6"
                :y="t.y + 4"
                text-anchor="end"
                font-size="10"
                fill="rgba(255,255,255,0.3)"
                font-family="DM Mono, monospace"
            >
                {{ t.v }}
            </text>

            <g v-for="(label, gi) in labels" :key="gi">
                <rect
                    v-for="(s, si) in series"
                    :key="s.label"
                    :x="barX(gi, si)"
                    :y="yOf(s.data[gi] ?? 0)"
                    :width="barW"
                    :height="barH(s.data[gi] ?? 0)"
                    :fill="s.color"
                    rx="4"
                    class="chart-bar"
                    :style="{
                        transform: ready ? 'scaleY(1)' : 'scaleY(0)',
                        transitionDelay: `${gi * 60 + si * 90}ms`,
                    }"
                >
                    <title>{{ s.label }} • {{ label }}: {{ s.data[gi] ?? 0 }}</title>
                </rect>
                <text
                    :x="PAD_L + gi * groupW + groupW / 2"
                    :y="H - 8"
                    text-anchor="middle"
                    font-size="11"
                    fill="rgba(255,255,255,0.3)"
                    font-family="DM Mono, monospace"
                >
                    {{ label }}
                </text>
            </g>
        </svg>
    </div>
</template>

<style scoped>
.chart-bar {
    transform-box: fill-box;
    transform-origin: bottom;
    transition: transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
}
</style>
