<script setup>
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const visible = ref(false)
const kind = ref('success') // success | error
const message = ref('')
const progressKey = ref(0)
let hideTimer = null
let progressTimer = null
const progress = ref(100)

const flash = computed(() => page.props.flash || {})

const show = (type, text) => {
    kind.value = type
    message.value = text
    visible.value = true
    progressKey.value++
    progress.value = 100

    clearTimeout(hideTimer)
    clearInterval(progressTimer)

    const duration = 4200
    const started = Date.now()
    progressTimer = setInterval(() => {
        const elapsed = Date.now() - started
        progress.value = Math.max(0, 100 - (elapsed / duration) * 100)
        if (progress.value <= 0) clearInterval(progressTimer)
    }, 50)

    hideTimer = setTimeout(() => {
        visible.value = false
        clearInterval(progressTimer)
    }, duration)
}

watch(
    flash,
    (val) => {
        if (val?.success) show('success', val.success)
        else if (val?.error) show('error', val.error)
    },
    { immediate: true }
)

const close = () => {
    visible.value = false
    clearTimeout(hideTimer)
    clearInterval(progressTimer)
}

const isSuccess = computed(() => kind.value === 'success')
</script>

<template>
    <Teleport to="body">
        <div class="pointer-events-none fixed right-4 top-4 z-[100] flex w-[min(92vw,380px)] flex-col items-end gap-2 sm:right-6 sm:top-6">
            <Transition
                enter-active-class="transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                enter-from-class="translate-x-8 -translate-y-2 scale-95 opacity-0"
                enter-to-class="translate-x-0 translate-y-0 scale-100 opacity-100"
                leave-active-class="transition-all duration-250 ease-in"
                leave-from-class="translate-x-0 opacity-100 scale-100"
                leave-to-class="translate-x-8 opacity-0 scale-95"
            >
                <div
                    v-if="visible"
                    role="status"
                    :aria-live="kind === 'error' ? 'assertive' : 'polite'"
                    class="pointer-events-auto w-full overflow-hidden rounded-2xl border backdrop-blur-xl"
                    :class="isSuccess
                        ? 'border-emerald-300/25 bg-[#04120c]/95 shadow-[0_20px_60px_rgba(16,185,129,0.25)]'
                        : 'border-rose-300/25 bg-[#180608]/95 shadow-[0_20px_60px_rgba(244,63,94,0.25)]'"
                >
                    <div class="flex items-start gap-3 p-4">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl"
                            :class="isSuccess ? 'bg-emerald-300/15 text-emerald-300' : 'bg-rose-300/15 text-rose-300'"
                        >
                            <svg v-if="isSuccess" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-mono text-[10px] tracking-[.25em]" :class="isSuccess ? 'text-emerald-300/80' : 'text-rose-300/80'">
                                {{ isSuccess ? 'BERHASIL' : 'GAGAL' }}
                            </p>
                            <p class="mt-0.5 text-sm font-medium leading-snug text-white">{{ message }}</p>
                        </div>
                        <button
                            @click="close"
                            aria-label="Tutup notifikasi"
                            class="rounded-lg p-1 text-white/30 transition hover:bg-white/10 hover:text-white"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="h-[3px] w-full bg-white/5">
                        <div
                            :key="progressKey"
                            class="h-full transition-[width] duration-75"
                            :class="isSuccess ? 'bg-emerald-300' : 'bg-rose-300'"
                            :style="{ width: progress + '%' }"
                        ></div>
                    </div>
                </div>
            </Transition>
        </div>
    </Teleport>
</template>
