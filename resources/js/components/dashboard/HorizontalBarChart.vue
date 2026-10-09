<script setup>
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => [], // [{ id, nama, icon, total }]
    },
})

const ready = ref(false)
onMounted(() => {
    requestAnimationFrame(() => requestAnimationFrame(() => {
        ready.value = true
    }))
})

const palette = ['#8b5cf6', '#22d3ee', '#34d399', '#fb923c', '#f472b6', '#818cf8', '#fbbf24', '#f87171']

const max = computed(() => Math.max(1, ...props.items.map((i) => i.total ?? 0)))
const barColor = (idx) => palette[idx % palette.length]
const pct = (total) => Math.max((total / max.value) * 100, total > 0 ? 5 : 0)
</script>

<template>
    <div>
        <div v-if="items.length" class="space-y-4">
            <div v-for="(item, idx) in items" :key="item.id ?? item.nama">
                <div class="mb-1.5 flex items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/[0.03] text-lg">{{ item.icon || '🏢' }}</span>
                        <span class="truncate text-sm font-medium text-white">{{ item.nama }}</span>
                    </div>
                    <span class="shrink-0 font-mono text-xs" :style="{ color: barColor(idx) }">{{ item.total }} proker</span>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-white/[0.04]">
                    <div
                        class="h-full rounded-full transition-all duration-700 ease-out"
                        :style="{
                            width: ready ? pct(item.total) + '%' : '0%',
                            background: `linear-gradient(90deg, ${barColor(idx)}, ${barColor(idx)}88)`,
                            boxShadow: `0 0 16px ${barColor(idx)}44`,
                            transitionDelay: `${idx * 90}ms`,
                        }"
                    ></div>
                </div>
            </div>
        </div>
        <p v-else class="py-10 text-center text-sm text-white/30">Belum ada data departemen.</p>
    </div>
</template>
