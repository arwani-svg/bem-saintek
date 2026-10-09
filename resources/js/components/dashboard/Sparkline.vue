<script setup>
import { computed } from 'vue'

const props = defineProps({
    data: {
        type: Array,
        default: () => [],
    },
    width: {
        type: Number,
        default: 220,
    },
    height: {
        type: Number,
        default: 56,
    },
    stroke: {
        type: String,
        default: '#8b5cf6',
    },
    strokeWidth: {
        type: Number,
        default: 2,
    },
})

const id = `spark-${Math.random().toString(36).slice(2, 9)}`

const points = computed(() => {
    const d = props.data.length ? props.data : [0, 0]
    const min = Math.min(...d)
    const max = Math.max(...d)
    const range = max - min || 1
    const padX = 2
    const padTop = 6
    const padBottom = 6
    const stepX = (props.width - padX * 2) / Math.max(d.length - 1, 1)
    return d.map((v, i) => {
        const x = padX + i * stepX
        const y = padTop + (1 - (v - min) / range) * (props.height - padTop - padBottom)
        return [x, y]
    })
})

// Smooth path ala Chart.js tension .45 (catmull-rom -> bezier).
const linePath = computed(() => {
    const p = points.value
    if (p.length < 2) return ''
    let d = `M${p[0][0].toFixed(1)},${p[0][1].toFixed(1)}`
    for (let i = 0; i < p.length - 1; i++) {
        const p0 = p[Math.max(0, i - 1)]
        const p1 = p[i]
        const p2 = p[i + 1]
        const p3 = p[Math.min(p.length - 1, i + 2)]
        const c1x = p1[0] + (p2[0] - p0[0]) / 6
        const c1y = p1[1] + (p2[1] - p0[1]) / 6
        const c2x = p2[0] - (p3[0] - p1[0]) / 6
        const c2y = p2[1] - (p3[1] - p1[1]) / 6
        d += ` C${c1x.toFixed(1)},${c1y.toFixed(1)} ${c2x.toFixed(1)},${c2y.toFixed(1)} ${p2[0].toFixed(1)},${p2[1].toFixed(1)}`
    }
    return d
})

const areaPath = computed(() => {
    if (!linePath.value) return ''
    const firstX = points.value[0][0]
    const lastX = points.value[points.value.length - 1][0]
    return `${linePath.value} L${lastX.toFixed(1)},${props.height} L${firstX.toFixed(1)},${props.height} Z`
})
</script>

<template>
    <svg
        :width="width"
        :height="height"
        :viewBox="`0 0 ${width} ${height}`"
        class="w-full overflow-visible"
        preserveAspectRatio="none"
        role="img"
        aria-label="Tren mini"
    >
        <defs>
            <linearGradient :id="id" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" :stop-color="stroke" stop-opacity="0.28" />
                <stop offset="100%" :stop-color="stroke" stop-opacity="0" />
            </linearGradient>
        </defs>
        <path :d="areaPath" :fill="`url(#${id})`" />
        <path
            :d="linePath"
            fill="none"
            :stroke="stroke"
            :stroke-width="strokeWidth"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>
