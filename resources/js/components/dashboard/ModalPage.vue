<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    title: String,
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
        default: 'xl', // sm | md | lg | xl
    },
    backHref: {
        type: String,
        default: null,
    },
})

const maxWidth = computed(() => ({
    sm: 'max-w-md',
    md: 'max-w-xl',
    lg: 'max-w-2xl',
    xl: 'max-w-3xl',
}[props.size] || 'max-w-3xl'))

const onKeydown = (e) => {
    if (e.key === 'Escape' && props.backHref) {
        router.visit(props.backHref)
    }
}

onMounted(() => {
    document.addEventListener('keydown', onKeydown)
    document.body.style.overflow = ''
})

onUnmounted(() => {
    document.removeEventListener('keydown', onKeydown)
})
</script>

<template>
    <Head :title="title" />

    <div class="bem-saintek modal-overlay fixed inset-0 z-[80] overflow-y-auto font-sans text-white antialiased saintek-scroll">
        <div class="noise"></div>
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-40 left-1/3 h-[380px] w-[680px] rounded-full bg-cyan-400/[0.06] blur-[130px]"></div>
            <div class="absolute -bottom-48 -right-32 h-[380px] w-[380px] rounded-full bg-violet-500/[0.05] blur-[120px]"></div>
        </div>

        <div class="relative flex min-h-screen items-start justify-center p-4 py-8 sm:p-6 sm:py-12">
            <div :class="[maxWidth, 'w-full overflow-hidden rounded-2xl border border-white/10 bg-[#070b14]/95 shadow-[0_30px_100px_rgba(0,0,0,0.6)] backdrop-blur-xl']">
                <!-- Header popup -->
                <div class="flex items-start justify-between gap-4 border-b border-white/5 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <Link
                            v-if="backHref"
                            :href="backHref"
                            class="shrink-0 rounded-xl border border-white/10 p-2 text-white/60 transition hover:border-cyan-300/30 hover:text-white"
                            title="Kembali (ESC)"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <div class="min-w-0">
                            <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">{{ eyebrow }}</p>
                            <h1 class="font-display truncate text-xl font-bold tracking-tight text-white">{{ title }}</h1>
                            <p v-if="subtitle" class="mt-0.5 text-sm text-white/40">{{ subtitle }}</p>
                        </div>
                    </div>
                    <Link
                        v-if="backHref"
                        :href="backHref"
                        class="shrink-0 rounded-xl border border-white/10 p-2 text-white/40 transition hover:border-rose-300/30 hover:text-rose-300"
                        title="Tutup"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </Link>
                </div>

                <!-- Isi form -->
                <div class="max-h-[calc(100vh-220px)] overflow-y-auto px-6 py-6 saintek-scroll">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
