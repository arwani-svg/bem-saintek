<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    subtitle: {
        type: String,
        default: '',
    },
    eyebrow: {
        type: String,
        default: 'SAINTEK.OS',
    },
    size: {
        type: String,
        default: 'lg', // sm | md | lg | xl
    },
    processing: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close', 'update:show'])

const maxWidth = computed(() => ({
    sm: 'max-w-md',
    md: 'max-w-xl',
    lg: 'max-w-2xl',
    xl: 'max-w-3xl',
}[props.size] || 'max-w-2xl'))

const close = () => {
    if (props.processing) return
    emit('close')
    emit('update:show', false)
}

const onKeydown = (e) => {
    if (e.key === 'Escape') close()
}

watch(() => props.show, (v) => {
    document.body.style.overflow = v ? 'hidden' : ''
})

onMounted(() => {
    document.addEventListener('keydown', onKeydown)
    if (props.show) document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
    document.removeEventListener('keydown', onKeydown)
    document.body.style.overflow = ''
})
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="bem-saintek modal-overlay fixed inset-0 z-[80] flex items-start justify-center overflow-y-auto p-4 py-8 font-sans text-white antialiased sm:p-6"
                @click.self="close"
            >
                <Transition
                    enter-active-class="transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    enter-from-class="scale-95 translate-y-4 opacity-0"
                    enter-to-class="scale-100 translate-y-0 opacity-100"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-95 opacity-0"
                >
                    <div
                        v-if="show"
                        role="dialog"
                        aria-modal="true"
                        :aria-label="title"
                        :class="[maxWidth, 'w-full overflow-hidden rounded-2xl border border-white/10 bg-[#070b14]/95 shadow-[0_30px_100px_rgba(0,0,0,0.6)] backdrop-blur-xl']"
                    >
                        <!-- Header popup -->
                        <div class="flex items-start justify-between gap-4 border-b border-white/5 px-6 py-5">
                            <div class="min-w-0">
                                <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">{{ eyebrow }}</p>
                                <h2 class="font-display truncate text-xl font-bold tracking-tight text-white">{{ title }}</h2>
                                <p v-if="subtitle" class="mt-0.5 text-sm text-white/40">{{ subtitle }}</p>
                            </div>
                            <button
                                type="button"
                                @click="close"
                                class="shrink-0 rounded-xl border border-white/10 p-2 text-white/40 transition hover:border-rose-300/30 hover:text-rose-300"
                                title="Tutup (ESC)"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Isi form -->
                        <div class="max-h-[calc(100vh-220px)] overflow-y-auto px-6 py-6 saintek-scroll">
                            <slot />
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
