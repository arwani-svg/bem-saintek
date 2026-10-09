<script setup>
import Reveal from '@/components/ui/Reveal.vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => [
            { no: '001', color: 'text-cyan-300', title: 'Innovation Day', tags: 'Research • Technology • Exhibition', date: 'MAR 2026' },
            { no: '002', color: 'text-violet-300', title: 'Leadership Camp', tags: 'Leadership • Growth • Collaboration', date: 'APR 2026' },
            { no: '003', color: 'text-green-300', title: 'SAINTEK Berdampak', tags: 'Community • Education • Social Impact', date: 'JUN 2026' },
            { no: '004', color: 'text-pink-300', title: 'Tech Future Forum', tags: 'AI • Digital • Future', date: 'AUG 2026' },
            { no: '005', color: 'text-yellow-300', title: 'SAINTEK Expo', tags: 'Showcase • Community • Celebration', date: 'OCT 2026' },
        ],
    },
})
</script>

<template>
    <section id="archive" class="relative py-36 bg-white/[.015] border-y border-white/5">
        <div class="max-w-7xl mx-auto px-5 lg:px-8">
            <div class="grid lg:grid-cols-[.65fr_1.35fr] gap-14">
                <Reveal variant="right">
                    <div class="font-mono text-[10px] text-cyan-300 tracking-[.3em]">09 / FUTURE ARCHIVE</div>
                    <h2 class="font-display text-5xl md:text-7xl font-bold tracking-[-.06em] mt-5 leading-[.9] text-white">
                        MEMORY<br /><span class="text-white/20">BECOMES</span><br />HISTORY.
                    </h2>
                    <p class="text-white/30 leading-relaxed mt-8 max-w-md">
                        Setiap program meninggalkan jejak. Setiap jejak menjadi bagian dari perjalanan.
                    </p>
                </Reveal>

                <Reveal variant="left">
                    <div class="border-t border-white/10">
                        <div
                            v-for="item in items"
                            :key="item.no"
                            class="archive-item border-b border-white/10 py-7"
                        >
                            <div class="grid md:grid-cols-[100px_1fr_auto] gap-5 items-center px-2">
                                <div class="font-mono text-xs" :class="item.color">{{ item.no }}</div>
                                <div>
                                    <h3 class="font-display text-xl font-bold text-white">{{ item.title }}</h3>
                                    <p class="text-xs text-white/25 mt-2">{{ item.tags }}</p>
                                </div>
                                <div class="text-xs text-white/20 font-mono">{{ item.date }}</div>
                            </div>
                        </div>
                    </div>
                </Reveal>
            </div>
        </div>
    </section>
</template>
