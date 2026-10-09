<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const visible = ref(false)
const threshold = 600

function onScroll() {
    visible.value = window.scrollY > threshold
}

function goTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true })
    onScroll()
})

onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
    >
        <button
            v-show="visible"
            type="button"
            aria-label="Kembali ke atas"
            title="Kembali ke atas"
            @click="goTop"
            class="group fixed bottom-6 right-6 z-[80] w-11 h-11 rounded-full border border-cyan-300/25 bg-[#03050a]/80 backdrop-blur-xl flex items-center justify-center text-cyan-300 shadow-[0_0_25px_rgba(34,211,238,.15)] hover:bg-cyan-300 hover:text-black hover:border-cyan-300 hover:shadow-[0_0_35px_rgba(34,211,238,.4)] transition cursor-pointer"
        >
            <svg
                class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 15l7-7 7 7"
                />
            </svg>
            <span
                class="absolute -top-8 right-1/2 translate-x-1/2 whitespace-nowrap px-2 py-1 rounded-md bg-white text-black font-mono text-[9px] tracking-[.15em] opacity-0 group-hover:opacity-100 transition pointer-events-none"
            >
                TOP
            </span>
        </button>
    </Transition>
</template>
