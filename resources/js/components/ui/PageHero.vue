<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    label: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    crumbs: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <section class="relative overflow-hidden pt-32 pb-14">
        <!-- background ala beranda -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="grid-bg absolute inset-0 opacity-[0.11]"></div>
            <div class="absolute -top-40 left-1/2 h-[380px] w-[680px] -translate-x-1/2 rounded-full bg-cyan-400/[0.06] blur-[120px]"></div>
            <div class="absolute -bottom-48 -right-32 h-[380px] w-[380px] rounded-full bg-violet-500/[0.05] blur-[120px]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-5 lg:px-8">
            <nav class="mb-6 flex items-center gap-2 font-mono text-[10px] tracking-[.2em] text-white/30">
                <template v-for="(c, i) in crumbs" :key="i">
                    <Link v-if="c.href" :href="c.href" class="transition hover:text-cyan-300">
                        {{ c.label }}
                    </Link>
                    <span v-else class="text-white/60">{{ c.label }}</span>
                    <span v-if="i < crumbs.length - 1" class="text-white/15">/</span>
                </template>
            </nav>

            <div
                v-if="label"
                class="mb-4 inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/5 px-4 py-1.5 font-mono text-[10px] tracking-[.25em] text-cyan-300"
            >
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-cyan-300"></span>
                {{ label }}
            </div>

            <h1
                class="font-display max-w-3xl text-4xl font-bold leading-[1.02] tracking-tight text-white sm:text-5xl"
            >
                <slot name="title" />
            </h1>

            <p v-if="description" class="mt-4 max-w-2xl text-sm leading-relaxed text-white/40">
                {{ description }}
            </p>
            <slot name="description" />

            <slot name="extra" />
        </div>
    </section>
</template>
