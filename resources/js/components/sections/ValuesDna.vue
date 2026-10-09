<script setup>
import { ref } from 'vue'
import Reveal from '@/components/ui/Reveal.vue'

const props = defineProps({
    values: {
        type: Array,
        default: () => [
            { name: 'INTEGRITY', desc: 'Jujur dan konsisten dalam setiap keputusan.', pos: 'left-[31%] top-[6%]' },
            { name: 'INNOVATION', desc: 'Berani menciptakan solusi baru berbasis sains & teknologi.', pos: 'right-[31%] top-[18%]' },
            { name: 'COLLABORATION', desc: 'Bergerak bersama lintas departemen dan komunitas.', pos: 'left-[26%] top-[31%]' },
            { name: 'COURAGE', desc: 'Berani menyuarakan kebenaran dan mengambil langkah.', pos: 'right-[26%] top-[44%]' },
            { name: 'EMPATHY', desc: 'Mendengar dan hadir untuk mahasiswa serta masyarakat.', pos: 'left-[29%] top-[57%]' },
            { name: 'IMPACT', desc: 'Setiap program harus meninggalkan perubahan nyata.', pos: 'right-[29%] top-[70%]' },
        ],
    },
})

const active = ref(null)
const label = ref('HOVER A NODE')
const desc = ref('Enam prinsip yang menjadi DNA dari setiap keputusan, kolaborasi, dan gerakan.')

function enter(v) {
    active.value = v.name
    label.value = v.name
    desc.value = v.desc
}

function leave() {
    active.value = null
    label.value = 'HOVER A NODE'
    desc.value = 'Enam prinsip yang menjadi DNA dari setiap keputusan, kolaborasi, dan gerakan.'
}
</script>

<template>
    <section id="values" class="relative py-36 bg-white/[.015] border-y border-white/5 overflow-hidden">
        <div class="max-w-7xl mx-auto px-5 lg:px-8">
            <Reveal variant="up">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-3 mb-6">
                        <span class="w-8 h-px bg-violet-300"></span>
                        <span class="font-mono text-[10px] text-violet-300 tracking-[.3em]">03 / OUR DNA</span>
                        <span class="w-8 h-px bg-violet-300"></span>
                    </div>
                    <h2 class="font-display text-5xl md:text-7xl font-bold tracking-[-.06em] text-white">
                        WHAT <span class="text-white/20">DRIVES US.</span>
                    </h2>
                    <p class="max-w-xl mx-auto text-white/35 mt-6">{{ desc }}</p>
                </div>
            </Reveal>

            <div class="relative h-[620px] max-w-4xl mx-auto mt-20">
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 800 620" preserveAspectRatio="none">
                    <path
                        d="M250 0 C650 80 150 150 550 230 C650 280 150 350 250 430 C650 500 150 570 550 620"
                        fill="none" stroke="rgba(34,211,238,.2)" stroke-width="2"
                    />
                    <path
                        d="M550 0 C150 80 650 150 250 230 C150 280 650 350 550 430 C150 500 650 570 250 620"
                        fill="none" stroke="rgba(139,92,246,.2)" stroke-width="2"
                    />
                    <line x1="270" y1="48" x2="530" y2="48" stroke="rgba(255,255,255,.08)" />
                    <line x1="235" y1="125" x2="565" y2="125" stroke="rgba(255,255,255,.08)" />
                    <line x1="210" y1="205" x2="590" y2="205" stroke="rgba(255,255,255,.08)" />
                    <line x1="210" y1="285" x2="590" y2="285" stroke="rgba(255,255,255,.08)" />
                    <line x1="235" y1="365" x2="565" y2="365" stroke="rgba(255,255,255,.08)" />
                    <line x1="270" y1="445" x2="530" y2="445" stroke="rgba(255,255,255,.08)" />
                    <line x1="300" y1="525" x2="500" y2="525" stroke="rgba(255,255,255,.08)" />
                </svg>

                <button
                    v-for="v in values"
                    :key="v.name"
                    class="dna-node"
                    :class="[v.pos, { 'is-active': active === v.name }]"
                    @mouseenter="enter(v)"
                    @mouseleave="leave()"
                    @click="enter(v)"
                    :aria-label="v.name"
                ></button>

                <div
                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 rounded-full border border-cyan-300/30 bg-cyan-300/[.03] flex flex-col items-center justify-center text-center shadow-[0_0_100px_rgba(34,211,238,.06)]"
                >
                    <div class="font-mono text-[8px] tracking-[.3em] text-cyan-300">SAINTEK</div>
                    <div class="font-display text-xl font-bold mt-2 text-white">DNA</div>
                    <div class="text-[9px] text-white/25 mt-2 font-mono">THE CORE</div>
                </div>
            </div>

            <div class="text-center font-mono text-xs text-cyan-300 tracking-[.3em] min-h-[20px]">
                {{ label }}
            </div>

            <div class="flex flex-wrap justify-center gap-2 mt-8 md:hidden">
                <button
                    v-for="v in values"
                    :key="'chip-' + v.name"
                    class="px-3 py-1.5 rounded-full border text-[10px] font-mono transition"
                    :class="active === v.name ? 'border-cyan-300/60 text-cyan-300' : 'border-white/10 text-white/40'"
                    @click="enter(v)"
                >
                    {{ v.name }}
                </button>
            </div>
        </div>
    </section>
</template>
