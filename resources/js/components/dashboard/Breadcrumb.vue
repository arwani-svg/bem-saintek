<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <nav aria-label="Breadcrumb" class="flex min-w-0 flex-wrap items-center gap-1.5 font-mono text-[11px] tracking-wider">
        <template v-for="(item, i) in items" :key="i">
            <span v-if="i > 0" class="select-none text-white/20">/</span>
            <Link
                v-if="item.href && i !== items.length - 1"
                :href="item.href"
                class="truncate text-white/40 transition hover:text-cyan-300"
            >
                {{ item.label }}
            </Link>
            <span
                v-else
                :class="i === items.length - 1 ? 'truncate text-cyan-300' : 'truncate text-white/40'"
                :aria-current="i === items.length - 1 ? 'page' : undefined"
            >
                {{ item.label }}
            </span>
        </template>
    </nav>
</template>
