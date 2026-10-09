<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import Reveal from '@/components/ui/Reveal.vue'

const props = defineProps({
    programs: {
        type: Array,
        default: () => [],
    },
})

const defaults = [
    {
        category: 'Research',
        title: 'SAINTEK INNOVATION',
        highlight: 'INNOVATION',
        description: 'Ruang eksperimen bagi mahasiswa untuk membangun solusi berbasis teknologi terhadap persoalan nyata.',
        status: 'PROJECT_001',
        tags: ['Research', 'Technology', 'Impact'],
        color: 'cyan',
    },
    {
        category: 'Community',
        title: 'SAINTEK BERDAMPAK',
        highlight: 'BERDAMPAK',
        description: 'Membawa ilmu keluar dari ruang kelas dan mengubahnya menjadi kontribusi nyata bagi masyarakat.',
        status: 'PROJECT_002',
        tags: ['Community', 'Education', 'Impact'],
        color: 'green',
    },
]

const fallbackImages = [
    'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1400&q=85',
    'https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?auto=format&fit=crop&w=1400&q=85',
]

const list = computed(() => {
    if (props.programs?.length) {
        return props.programs.slice(0, 2).map((p, i) => {
            const parts = String(p.title ?? '').split(' ')
            return {
                category: p.category ?? p.departemen ?? 'Program',
                title: p.title ?? 'Program',
                highlight: parts.slice(1).join(' ') || p.departemen || '',
                description: p.description ?? '',
                status: p.status ?? `PROJECT_00${i + 1}`,
                tags: [p.category, p.departemen, p.status].filter(Boolean).slice(0, 3),
                color: i === 0 ? 'cyan' : 'green',
                image: fallbackImages[i % 2],
            }
        })
    }
    return defaults.map((d, i) => ({ ...d, image: fallbackImages[i % 2] }))
})
</script>

<template>
    <section id="programs" class="relative py-36 bg-white/[.015] border-y border-white/5">
        <div class="max-w-7xl mx-auto px-5 lg:px-8">
            <Reveal variant="up">
                <div class="text-center">
                    <div class="font-mono text-[10px] text-cyan-300 tracking-[.3em]">07 / SELECTED PROJECTS</div>
                    <h2 class="font-display text-5xl md:text-7xl font-bold tracking-[-.06em] mt-5 text-white">
                        IDEAS <span class="text-white/20">IN MOTION.</span>
                    </h2>
                    <p class="text-white/30 mt-6 max-w-xl mx-auto text-sm">
                        Program unggulan dari database — ditampilkan dengan bahasa visual SAINTEK.OS.
                    </p>
                </div>
            </Reveal>

            <div class="grid lg:grid-cols-2 gap-4 mt-16">
                <Reveal
                    v-for="(program, i) in list"
                    :key="program.title + i"
                    variant="up"
                    :delay="i * 120"
                >
                    <article
                        class="group relative min-h-[520px] rounded-[2rem] overflow-hidden border border-white/10"
                    >
                        <img
                            :src="program.image"
                            :alt="program.title"
                            class="absolute inset-0 w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition duration-1000"
                            loading="lazy"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#03050a] via-[#03050a]/50 to-transparent"></div>
                        <div class="relative h-full min-h-[520px] p-8 md:p-10 flex flex-col justify-end">
                            <div
                                class="font-mono text-[9px] tracking-[.3em]"
                                :class="program.color === 'green' ? 'text-green-300' : 'text-cyan-300'"
                            >
                                {{ program.status }}
                            </div>
                            <h3 class="font-display text-4xl md:text-5xl font-bold mt-3 text-white leading-[.95]">
                                {{ program.title.split(' ')[0] }}<br />
                                <span class="text-white/45">{{ program.highlight }}</span>
                            </h3>
                            <p class="text-sm text-white/40 max-w-lg mt-5 leading-relaxed">
                                {{ program.description }}
                            </p>
                            <div class="mt-7 flex flex-wrap items-center gap-2 text-xs">
                                <span
                                    v-for="t in program.tags"
                                    :key="t"
                                    class="px-3 py-1.5 rounded-full bg-white/10 text-white/60"
                                >
                                    {{ t }}
                                </span>
                            </div>
                        </div>
                    </article>
                </Reveal>
            </div>

            <Reveal variant="up" :delay="150">
                <div class="text-center mt-12">
                    <Link
                        href="/program-kerja"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-full border border-white/10 bg-white/[.025] text-sm text-white/60 hover:text-white hover:bg-white/[.06] transition"
                    >
                        Lihat Semua Program
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </Reveal>
        </div>
    </section>
</template>
