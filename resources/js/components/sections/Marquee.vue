<script setup>
const words = ['SCIENCE', 'TECHNOLOGY', 'COLLABORATION', 'INNOVATION', 'IMPACT', 'FUTURE']
</script>

<template>
    <section class="marquee border-y border-white/5 bg-white/[.012]">
        <div class="py-5">
            <div class="marquee-track">
                <div
                    v-for="n in 2"
                    :key="n"
                    class="flex items-center gap-12 px-6 whitespace-nowrap"
                    aria-hidden="true"
                >
                    <template v-for="w in words" :key="w">
                        <span class="font-display text-sm text-white/25">{{ w }}</span>
                        <span class="text-cyan-300">✦</span>
                    </template>
                </div>
            </div>
        </div>
    </section>
</template>
