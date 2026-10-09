<script setup>
import Reveal from '@/components/ui/Reveal.vue'

defineProps({
    email: { type: String, default: 'bem@saintek.ac.id' },
})
</script>

<template>
    <section id="join" class="relative min-h-[90vh] flex items-center overflow-hidden">
        <div class="absolute inset-0 grid-bg opacity-30"></div>
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full border border-cyan-300/[.04]"></div>
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full border border-violet-300/[.05]"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-5 text-center">
            <Reveal variant="up">
                <div class="font-mono text-[10px] text-cyan-300 tracking-[.4em]">10 / THE NEXT CHAPTER</div>
                <h2 class="font-display font-bold text-[clamp(4rem,11vw,10rem)] leading-[.8] tracking-[-.08em] mt-8">
                    <span class="text-white/20">DON'T WAIT</span><br />
                    <span class="gradient-text">BUILD IT.</span>
                </h2>
                <p class="max-w-xl mx-auto text-white/35 mt-10 leading-relaxed">
                    Masa depan tidak datang sendiri. Ia dibangun oleh orang-orang yang bersedia memulai sesuatu hari ini.
                </p>
                <div class="flex justify-center gap-3 mt-10">
                    <a
                        :href="`mailto:${email}`"
                        class="inline-flex items-center gap-3 px-7 py-4 rounded-full bg-white text-black text-sm font-bold hover:bg-cyan-300 transition"
                    >
                        Start a Conversation
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M7 7h10v10" />
                        </svg>
                    </a>
                </div>
                <div class="mt-20 font-mono text-[9px] text-white/15">BEM SAINTEK // BEYOND LIMITS // 2026</div>
            </Reveal>
        </div>
    </section>
</template>
