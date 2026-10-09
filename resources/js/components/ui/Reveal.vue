<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

// Pembungkus animasi scroll-reveal.
// Varian: fade | up | down | left | right | zoom
// Contoh: <Reveal variant="up" :delay="150"><div>...</div></Reveal>
const props = defineProps({
    as: { type: String, default: 'div' },
    variant: { type: String, default: 'up' },
    delay: { type: Number, default: 0 },
    duration: { type: Number, default: 700 },
    threshold: { type: Number, default: 0.15 },
    once: { type: Boolean, default: true },
})

const el = ref(null)
const visible = ref(false)
let observer = null

onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                visible.value = true
                if (props.once) observer.disconnect()
            } else if (!props.once) {
                visible.value = false
            }
        },
        { threshold: props.threshold }
    )
    if (el.value) observer.observe(el.value)
})

onUnmounted(() => observer?.disconnect())
</script>

<template>
    <component
        :is="as"
        ref="el"
        class="reveal"
        :class="[`reveal-${variant}`, { 'is-visible': visible }]"
        :style="{ '--reveal-delay': `${delay}ms`, '--reveal-duration': `${duration}ms` }"
    >
        <slot />
    </component>
</template>
