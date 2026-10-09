<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Hapus data?',
    },
    message: {
        type: String,
        default: 'Data yang dihapus tidak bisa dikembalikan.',
    },
    itemName: {
        type: String,
        default: '',
    },
    confirmText: {
        type: String,
        default: 'Ya, hapus',
    },
    processing: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['confirm', 'cancel', 'update:show'])

const shake = ref(false)

const close = () => {
    if (props.processing) return
    emit('cancel')
    emit('update:show', false)
}

const onConfirm = () => {
    emit('confirm')
}

watch(() => props.show, (v) => {
    if (v) {
        shake.value = false
        document.body.style.overflow = 'hidden'
    } else {
        document.body.style.overflow = ''
    }
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
                class="fixed inset-0 z-[90] flex items-center justify-center bg-black/45 p-4 backdrop-blur-md"
                @click.self="close"
                @keydown.esc="close"
            >
                <Transition
                    enter-active-class="transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    enter-from-class="scale-90 translate-y-4 opacity-0"
                    enter-to-class="scale-100 translate-y-0 opacity-100"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-95 opacity-0"
                >
                    <div
                        v-if="show"
                        role="alertdialog"
                        aria-modal="true"
                        :aria-label="title"
                        class="w-full max-w-sm overflow-hidden rounded-2xl border border-white/10 bg-[#070b14] shadow-[0_30px_100px_rgba(0,0,0,0.6)]"
                        :class="shake ? 'animate-[shake_.3s_ease]' : ''"
                    >
                        <div class="relative p-6 text-center">
                            <div class="pointer-events-none absolute -top-20 left-1/2 h-40 w-64 -translate-x-1/2 rounded-full bg-rose-500/15 blur-[60px]"></div>

                            <div class="relative mx-auto flex h-16 w-16 items-center justify-center">
                                <span class="absolute inset-0 animate-ping rounded-full bg-rose-500/15"></span>
                                <span class="relative flex h-16 w-16 items-center justify-center rounded-full border border-rose-300/30 bg-rose-300/10">
                                    <svg class="h-7 w-7 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </span>
                            </div>

                            <h3 class="font-display mt-4 text-lg font-bold text-white">{{ title }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-white/50">
                                {{ message }}
                                <span v-if="itemName" class="mt-1 block truncate font-semibold text-white">“{{ itemName }}”</span>
                            </p>

                            <div class="mt-6 grid grid-cols-2 gap-3">
                                <button
                                    @click="close"
                                    :disabled="processing"
                                    class="rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-semibold text-white/70 transition hover:border-white/20 hover:text-white disabled:opacity-50"
                                >
                                    Batal
                                </button>
                                <button
                                    @click="onConfirm"
                                    :disabled="processing"
                                    class="group relative overflow-hidden rounded-xl bg-rose-400 px-4 py-2.5 text-sm font-bold text-black transition hover:bg-rose-300 disabled:opacity-60"
                                >
                                    <span v-if="processing" class="flex items-center justify-center gap-2">
                                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                        </svg>
                                        Menghapus...
                                    </span>
                                    <span v-else>{{ confirmText }}</span>
                                </button>
                            </div>
                            <p class="mt-3 font-mono text-[10px] tracking-widest text-white/25">TEKAN ESC UNTUK BATAL</p>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
</style>
