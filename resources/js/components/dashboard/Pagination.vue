<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    currentPage: { type: Number, required: true },
    lastPage: { type: Number, required: true },
    total: { type: Number, default: 0 },
    from: { type: Number, default: null },
    to: { type: Number, default: null },
    itemLabel: { type: String, default: 'data' },
    hrefFor: { type: Function, required: true },
})

// Windowed page numbers: [1, '...', c-1, c, c+1, '...', last].
// Maks 7 slot angka sehingga DOM tetap kecil walau total ribuan baris.
const pages = computed(() => {
    const current = props.currentPage
    const last = props.lastPage
    if (last <= 7) {
        return Array.from({ length: last }, (_, i) => i + 1)
    }
    let left = current - 1
    let right = current + 1
    if (current <= 3) {
        left = 2
        right = 4
    } else if (current >= last - 2) {
        left = last - 3
        right = last - 1
    }
    const list = [1]
    if (left > 2) list.push('...')
    for (let p = left; p <= right; p++) list.push(p)
    if (right < last - 1) list.push('...')
    list.push(last)
    return list
})
</script>

<template>
    <div
        v-if="lastPage > 1"
        class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 px-6 py-4"
    >
        <p class="text-sm text-white/35">
            Menampilkan {{ from }} - {{ to }} dari {{ total }} {{ itemLabel }}
        </p>
        <nav class="flex items-center gap-1" aria-label="Pagination">
            <Link
                :href="hrefFor(currentPage - 1)"
                :class="[
                    'dash-page-btn',
                    { 'pointer-events-none opacity-30': currentPage <= 1 },
                ]"
                :preserve-state="true"
                :preserve-scroll="true"
                aria-label="Halaman sebelumnya"
            >
                &laquo;
            </Link>
            <template v-for="(page, i) in pages" :key="`${page}-${i}`">
                <span
                    v-if="page === '...'"
                    class="select-none px-1 text-sm text-white/30"
                    aria-hidden="true"
                >&hellip;</span>
                <Link
                    v-else
                    :href="hrefFor(page)"
                    :class="['dash-page-btn', { active: page === currentPage }]"
                    :preserve-state="true"
                    :preserve-scroll="true"
                    :aria-current="page === currentPage ? 'page' : undefined"
                >
                    {{ page }}
                </Link>
            </template>
            <Link
                :href="hrefFor(currentPage + 1)"
                :class="[
                    'dash-page-btn',
                    { 'pointer-events-none opacity-30': currentPage >= lastPage },
                ]"
                :preserve-state="true"
                :preserve-scroll="true"
                aria-label="Halaman berikutnya"
            >
                &raquo;
            </Link>
        </nav>
    </div>
</template>
