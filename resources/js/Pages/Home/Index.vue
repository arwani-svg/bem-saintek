<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Hero from '@/components/sections/Hero.vue'
import Marquee from '@/components/sections/Marquee.vue'
import About from '@/components/sections/About.vue'
import Sambutan from '@/components/sections/sambutan.vue'
// import ValuesDna from '@/components/sections/ValuesDna.vue'
import DepartmentsConstellation from '@/components/sections/DepartmentsConstellation.vue'
// import MissionControl from '@/components/sections/MissionControl.vue'
import PeopleGrid from '@/components/sections/PeopleGrid.vue'
import Programs from '@/components/sections/Programs.vue'
import HomeInformasi from '@/components/sections/HomeInformasi.vue'
// import CommandTerminal from '@/components/sections/CommandTerminal.vue'
// import Archive from '@/components/sections/Archive.vue'
// import JoinCta from '@/components/sections/JoinCta.vue'
import Footer from '@/components/sections/Footer.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

defineProps({
    programs: {
        type: Array,
        default: () => [],
    },
    informasis: {
        type: Array,
        default: () => [],
    },
})

// Custom cursor ala desain asli (hanya device ber-mouse, disembunyikan di mobile via CSS)
const dot = ref(null)
const ring = ref(null)
let mx = 0
let my = 0
let rx = 0
let ry = 0
let raf = 0

function onMove(e) {
    mx = e.clientX
    my = e.clientY
    if (dot.value) {
        dot.value.style.left = mx + 'px'
        dot.value.style.top = my + 'px'
    }
}

function loop() {
    rx += (mx - rx) * 0.15
    ry += (my - ry) * 0.15
    if (ring.value) {
        ring.value.style.left = rx + 'px'
        ring.value.style.top = ry + 'px'
    }
    raf = requestAnimationFrame(loop)
}

onMounted(() => {
    if (window.matchMedia('(pointer: fine)').matches) {
        window.addEventListener('mousemove', onMove)
        loop()
    }
})

onUnmounted(() => {
    window.removeEventListener('mousemove', onMove)
    cancelAnimationFrame(raf)
})
</script>

<template>
    <Head title="Beranda">
        <meta
            name="description"
            content="BEM SAINTEK — Beyond Limits. Science, Technology, Collaboration and Impact."
        />
    </Head>

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>

        <div
            ref="dot"
            class="pointer-events-none fixed z-[999] hidden md:block w-[5px] h-[5px] rounded-full bg-white -translate-x-1/2 -translate-y-1/2 shadow-[0_0_15px_#22d3ee]"
        ></div>
        <div
            ref="ring"
            class="pointer-events-none fixed z-[999] hidden md:block w-8 h-8 rounded-full border border-cyan-300/50 -translate-x-1/2 -translate-y-1/2"
        ></div>

        <Navbar />

        <main>
            <Hero />
            <Marquee />
            <Sambutan />
            <About />
            <!-- <ValuesDna /> -->
            <DepartmentsConstellation />
            <!-- <MissionControl /> -->
            <PeopleGrid />
            <Programs :programs="programs" />
            <HomeInformasi :informasis="informasis" />
            <!-- <CommandTerminal /> -->
            <!-- <Archive /> -->
            <!-- <JoinCta /> -->
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
