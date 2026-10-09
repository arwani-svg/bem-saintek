<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import Reveal from '@/components/ui/Reveal.vue'

const props = defineProps({
    impact: { type: Number, default: 98.7 },
    highlights: {
        type: Array,
        default: () => [
            { value: '32', label: 'PROJECTS' },
            { value: '24', label: 'PARTNERS' },
            { value: '1.2K', label: 'STUDENTS' },
            { value: '08', label: 'NODES' },
        ],
    },
    bars: {
        type: Array,
        default: () => [
            { label: 'COLLABORATION', value: 92 },
            { label: 'INNOVATION', value: 87 },
            { label: 'PARTICIPATION', value: 95 },
            { label: 'SOCIAL IMPACT', value: 89 },
        ],
    },
})

const root = ref(null)
const display = ref(0)
const started = ref(false)
const barWidths = ref(props.bars.map(() => 0))

function animate() {
    const start = performance.now()
    const target = props.impact
    function tick(now) {
        const p = Math.min((now - start) / 1800, 1)
        const eased = 1 - Math.pow(1 - p, 3)
        display.value = target * eased
        if (p < 1) requestAnimationFrame(tick)
    }
    requestAnimationFrame(tick)
    setTimeout(() => {
        barWidths.value = props.bars.map((b) => b.value)
    }, 300)
}

let observer = null

onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting && !started.value) {
                started.value = true
                animate()
                observer.disconnect()
            }
        },
        { threshold: 0.3 }
    )
    if (root.value) observer.observe(root.value)
})

onUnmounted(() => observer?.disconnect())
</script>

<template>
    <section id="mission" ref="root" class="relative py-36 bg-white/[.015] border-y border-white/5">
        <div class="max-w-7xl mx-auto px-5 lg:px-8">
            <Reveal variant="up">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">
                    <div>
                        <div class="flex items-center gap-3 mb-7">
                            <span class="w-9 h-px bg-green-300"></span>
                            <span class="font-mono text-[10px] text-green-300 tracking-[.3em]">05 / MISSION CONTROL</span>
                        </div>
                        <h2 class="font-display text-5xl md:text-7xl font-bold tracking-[-.06em] text-white">
                            MEASURE <span class="text-white/20">THE IMPACT.</span>
                        </h2>
                    </div>
                    <div class="font-mono text-[9px] text-white/20">LAST SYNC // 27.09.2026</div>
                </div>
            </Reveal>

            <div class="grid lg:grid-cols-12 gap-4 mt-16">
                <Reveal variant="up" class="lg:col-span-7 rounded-[2rem] glass p-8 md:p-10">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-mono text-[9px] text-white/25 tracking-[.25em]">TOTAL IMPACT</div>
                            <div class="font-display text-7xl md:text-8xl font-bold mt-3 text-white">
                                {{ display.toFixed(1) }}<span class="text-cyan-300">%</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-green-400/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h4l3 8 4-16 3 8h4" />
                            </svg>
                        </div>
                    </div>

                    <div class="mt-10">
                        <div class="flex justify-between text-xs mb-3">
                            <span class="text-white/30 font-mono">ORGANIZATIONAL PERFORMANCE</span>
                            <span class="text-cyan-300 font-mono">{{ impact }}</span>
                        </div>
                        <div class="progress-bar"><span :style="{ width: (started ? impact : 0) + '%' }"></span></div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-8">
                        <div
                            v-for="h in highlights"
                            :key="h.label"
                            class="p-5 rounded-2xl bg-white/[.025] border border-white/5"
                        >
                            <div class="text-2xl font-display font-bold text-white">{{ h.value }}</div>
                            <div class="text-[9px] text-white/25 mt-2 font-mono">{{ h.label }}</div>
                        </div>
                    </div>
                </Reveal>

                <Reveal variant="up" :delay="120" class="lg:col-span-5 rounded-[2rem] glass p-8">
                    <div class="flex items-center justify-between">
                        <div class="font-mono text-[9px] text-white/25">SYSTEM STATUS</div>
                        <span class="flex items-center gap-2 text-[9px] font-mono text-green-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> OPERATIONAL
                        </span>
                    </div>

                    <div class="mt-10 space-y-6">
                        <div v-for="(b, i) in bars" :key="b.label">
                            <div class="flex justify-between text-xs mb-3">
                                <span class="text-white/35 font-mono">{{ b.label }}</span>
                                <span class="text-white/50 font-mono">{{ b.value }}%</span>
                            </div>
                            <div class="progress-bar"><span :style="{ width: barWidths[i] + '%' }"></span></div>
                        </div>
                    </div>

                    <div class="mt-10 p-5 rounded-2xl bg-cyan-300/[.03] border border-cyan-300/10">
                        <div class="flex gap-3">
                            <svg class="w-4 h-4 text-cyan-300 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M8.464 15.536a5 5 0 010-7.072m7.072 0a5 5 0 010 7.072M12 13a1 1 0 100-2 1 1 0 000 2z" />
                            </svg>
                            <div>
                                <div class="text-xs text-cyan-300 font-mono">LIVE MESSAGE</div>
                                <p class="text-xs text-white/35 leading-relaxed mt-2">Semua sistem bergerak. Ide baru sedang diproses.</p>
                            </div>
                        </div>
                    </div>
                </Reveal>
            </div>
        </div>
    </section>
</template>
