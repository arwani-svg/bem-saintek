<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'

// Angka yang menghitung naik saat terlihat, cth: "500+", "95%"
// Contoh: <CountUp value="500+" />
const props = defineProps({
    value: { type: String, required: true },
    duration: { type: Number, default: 1600 },
})

const display = ref('0')
const el = ref(null)
let observer = null
let raf = null

const parsed = computed(() => {
    const match = String(props.value).match(/^([\d.,]+)(.*)$/)
    if (!match) return { num: 0, decimals: 0, suffix: props.value }
    const decimals = (match[1].split('.')[1] || '').length
    return { num: parseFloat(match[1].replace(/,/g, '')), decimals, suffix: match[2] }
})

function animate() {
    const { num, decimals, suffix } = parsed.value
    const start = performance.now()
    const tick = (now) => {
        const p = Math.min((now - start) / props.duration, 1)
        const eased = p === 1 ? 1 : 1 - Math.pow(2, -10 * p)
        display.value = (num * eased).toFixed(decimals) + suffix
        if (p < 1) raf = requestAnimationFrame(tick)
    }
    raf = requestAnimationFrame(tick)
}

onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                animate()
                observer.disconnect()
            }
        },
        { threshold: 0.4 }
    )
    if (el.value) observer.observe(el.value)
})

onUnmounted(() => {
    observer?.disconnect()
    if (raf) cancelAnimationFrame(raf)
})
</script>

<template>
    <span ref="el">{{ display }}</span>
</template>
