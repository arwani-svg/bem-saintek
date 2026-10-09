<script setup>
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => [], // [{ label, short, value }]
    },
})

const ready = ref(false)
onMounted(() => {
    requestAnimationFrame(() => requestAnimationFrame(() => {
        ready.value = true
    }))
})

const palette = ['#8b5cf6', '#22d3ee', '#34d399', '#fb923c', '#f472b6', '#818cf8']

const max = computed(() => Math.max(1, ...props.items.map((i) => i.value ?? 0)))

const barColor = (idx) => palette[idx % palette.length]
</script>

<template>
    <div>
        <div v-if="items.length" class="flex h-[260px] items-end justify-around gap-2 sm:gap-3">
            <div
                v-for="(item, idx) in items"
                :key="item.label"
                class="group flex h-full min-w-0 flex-1 flex-col items-center justify-end"
            >
                <span
                    class="mb-1.5 font-mono text-xs font-bold text-white transition-colors"
                    :style="{ color: barColor(idx) }"
                >
                    {{ item.value }}
                </span>
                <div class="flex w-full flex-1 items-end justify-center rounded-xl bg-white/[0.02]">
                    <div
                        class="w-full max-w-[52px] rounded-lg transition-all duration-700 ease-out group-hover:brightness-125"
                        :style="{
                            height: ready ? Math.max((item.value / max) * 100, item.value > 0 ? 6 : 2) + '%' : '2%',
                            background: `linear-gradient(180deg, ${barColor(idx)} 0%, ${barColor(idx)}26 100%)`,
                            border: `1px solid ${barColor(idx)}55`,
                            boxShadow: `0 0 24px ${barColor(idx)}22`,
                            transitionDelay: `${idx * 80}ms`,
                        }"
                        :title="`${item.label}: ${item.value}`"
                    ></div>
                </div>
                <span class="mt-2 flex h-8 w-8 items-center justify-center rounded-lg font-mono text-[11px] font-bold text-white/70" :style="{ background: `${barColor(idx)}14`, border: `1px solid ${barColor(idx)}22` }">
                    {{ item.short }}
                </span>
                <span class="mt-1 hidden max-w-full truncate text-[10px] text-white/30 lg:block" :title="item.label">
                    {{ item.label.split(' ')[0] }}
                </span>
            </div>
        </div>
        <p v-else class="py-16 text-center text-sm text-white/30">Belum ada data prodi.</p>
        <div v-if="items.length" class="mt-4 flex flex-wrap gap-x-4 gap-y-1.5 border-t border-white/5 pt-3">
            <span v-for="(item, idx) in items" :key="'leg-' + item.label" class="flex items-center gap-1.5 text-[11px] text-white/40">
                <span class="h-2 w-2 rounded-full" :style="{ background: barColor(idx) }"></span>
                {{ item.label }} ({{ item.value }})
            </span>
        </div>
    </div>
</template>
