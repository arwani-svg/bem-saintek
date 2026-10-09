<script setup>
import Reveal from '@/components/ui/Reveal.vue'

const props = defineProps({
    stats: {
        type: Array,
        default: () => [
            { value: '08', label: 'DEPARTMENTS' },
            { value: '32', label: 'PROJECTS' },
            { value: '1.2K', label: 'STUDENTS' },
        ],
    },
    image: {
        type: String,
        default:
            'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1000&q=85',
    },
})
</script>

<template>
    <section
        id="sambutan"
        class="relative isolate overflow-hidden py-24 lg:py-28 bg-[#03050a]"
    >
        <!-- ================= BACKGROUND ORNAMENT ================= -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <!-- Grid -->
            <div class="absolute inset-0 grid-bg opacity-[0.16]"></div>

            <!-- Radial glows -->
            <div
                class="absolute -top-40 -left-40 w-[500px] h-[500px] rounded-full
                bg-cyan-400/[0.06] blur-[120px]"
            ></div>

            <div
                class="absolute top-1/2 -right-64 w-[600px] h-[600px] rounded-full
                bg-violet-500/[0.05] blur-[140px]"
            ></div>

            <!-- Technical circles -->
            <div
                class="absolute top-20 right-[8%] w-56 h-56 rounded-full
                border border-cyan-300/[0.06]"
            ></div>

            <div
                class="absolute top-24 right-[6%] w-64 h-64 rounded-full
                border border-violet-300/[0.04]"
            ></div>

            <div
                class="absolute top-[42%] right-[13%] w-2 h-2 rounded-full
                bg-cyan-300/40
                shadow-[0_0_20px_rgba(103,232,249,.6)]"
            ></div>

            <div
                class="absolute bottom-20 left-[8%] w-1.5 h-1.5 rounded-full
                bg-violet-300/40
                shadow-[0_0_18px_rgba(196,181,253,.5)]"
            ></div>

            <!-- Decorative horizontal lines -->
            <div
                class="absolute top-[18%] left-0 w-32 h-px
                bg-gradient-to-r from-transparent to-cyan-300/20"
            ></div>

            <div
                class="absolute bottom-[18%] right-0 w-44 h-px
                bg-gradient-to-l from-transparent to-violet-300/20"
            ></div>

            <!-- Corner marks -->
            <div class="absolute top-10 right-10 hidden lg:block opacity-30">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-px bg-cyan-300"></span>
                    <span class="w-1.5 h-1.5 bg-cyan-300"></span>
                </div>

                <div
                    class="mt-2 font-mono text-[7px]
                    tracking-[.35em] text-cyan-300"
                >
                    SYSTEM / 001
                </div>
            </div>

            <div class="absolute bottom-10 left-10 hidden lg:block opacity-20">
                <div
                    class="font-mono text-[7px]
                    tracking-[.35em] text-white"
                >
                    BEM SAINTEK / EST. 2026
                </div>
            </div>
        </div>

        <!-- ================= CONTENT ================= -->
        <div class="relative max-w-7xl mx-auto px-5 lg:px-8">

            <!-- ================= SECTION HEADER ================= -->
            <Reveal variant="up">
                <div class="flex items-center gap-3 mb-11 lg:mb-12">
                    <span class="w-8 h-px bg-cyan-300"></span>

                    <span
                        class="font-mono text-[9px] tracking-[.35em]
                        text-cyan-300 uppercase"
                    >
                        02 / Chairman's Message
                    </span>

                    <span
                        class="ml-auto hidden sm:block
                        font-mono text-[8px]
                        tracking-[.25em] text-white/20"
                    >
                        A MESSAGE FROM THE CHAIRMAN
                    </span>
                </div>
            </Reveal>

            <div
                class="grid lg:grid-cols-[.75fr_1.25fr]
                gap-14 xl:gap-5 items-center"
            >

                <!-- ================= IMAGE ================= -->
                <Reveal variant="right">
                    <div
                        class="relative max-w-[350px]
                        mx-auto lg:mx-0"
                    >
                        <!-- Outer technical frame -->
                        <div
                            class="absolute -inset-4 border
                            border-cyan-300/[0.08]
                            rounded-[2.2rem] rotate-2"
                        ></div>

                        <div
                            class="absolute -inset-4 border
                            border-violet-300/[0.06]
                            rounded-[2.2rem] -rotate-2"
                        ></div>

                        <!-- Corner brackets -->
                        <div
                            class="absolute -top-2 -left-2
                            w-8 h-8 border-l border-t
                            border-cyan-300/40 z-10"
                        ></div>

                        <div
                            class="absolute -top-2 -right-2
                            w-8 h-8 border-r border-t
                            border-cyan-300/20 z-10"
                        ></div>

                        <div
                            class="absolute -bottom-2 -left-2
                            w-8 h-8 border-l border-b
                            border-violet-300/20 z-10"
                        ></div>

                        <div
                            class="absolute -bottom-2 -right-2
                            w-8 h-8 border-r border-b
                            border-violet-300/40 z-10"
                        ></div>

                        <!-- Image -->
                        <div
                            class="image-card group relative
                            aspect-[4/5] rounded-[1.8rem]
                            overflow-hidden border border-white/10
                            bg-white/5"
                        >
                            <img
                                :src="image"
                                alt="Ketua BEM Saintek"
                                class="w-full h-full object-cover grayscale
                                transition-all duration-700
                                group-hover:grayscale-0
                                group-hover:scale-[1.03]"
                                loading="lazy"
                            />

                            <!-- Image overlay -->
                            <div
                                class="absolute inset-0
                                bg-gradient-to-t
                                from-[#03050a]
                                via-[#03050a]/10
                                to-transparent"
                            ></div>

                            <div
                                class="absolute inset-0
                                bg-gradient-to-br
                                from-cyan-300/[0.08]
                                via-transparent
                                to-violet-400/[0.08]"
                            ></div>

                            <!-- Image metadata -->
                            <div class="absolute top-4 left-4">
                                <div
                                    class="font-mono text-[7px]
                                    text-cyan-300
                                    tracking-[.35em]"
                                >
                                    PORTRAIT_001
                                </div>

                                <div
                                    class="mt-1 font-mono text-[6px]
                                    text-white/30
                                    tracking-[.2em]"
                                >
                                    BEM SAINTEK
                                </div>
                            </div>

                            <!-- Bottom identity -->
                            <div class="absolute bottom-5 left-5 right-5">
                                <div
                                    class="font-mono text-[8px]
                                    text-cyan-300
                                    tracking-[.3em]"
                                >
                                    CHAIRMAN'S MESSAGE
                                </div>

                                <div
                                    class="font-display text-2xl
                                    font-bold tracking-[-.05em]
                                    text-white mt-1"
                                >
                                    THE MOVEMENT
                                </div>

                                <div class="flex items-center gap-2 mt-3">
                                    <span
                                        class="w-9 h-px
                                        bg-cyan-300/60"
                                    ></span>

                                    <span
                                        class="font-mono text-[7px]
                                        text-white/30
                                        tracking-[.2em]"
                                    >
                                        LEAD • SERVE • MOVE
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Floating coordinate -->
                        <div
                            class="absolute -right-7 top-1/3
                            hidden xl:block
                            font-mono text-[7px]
                            text-white/20
                            tracking-[.25em]
                            [writing-mode:vertical-rl]"
                        >
                            -7.XXXX / 112.XXXX
                        </div>
                    </div>
                </Reveal>

                <!-- ================= MESSAGE ================= -->
                <Reveal variant="left">
                    <div>

                        <!-- Eyebrow -->
                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-7 h-px bg-cyan-300"></span>

                            <span
                                class="font-mono text-[8px]
                                text-cyan-300
                                tracking-[.35em]"
                            >
                                THE CHAIRMAN
                            </span>
                        </div>

                        <!-- Heading -->
                        <h2
                            class="font-display text-4xl md:text-5xl
                            xl:text-6xl
                            font-bold tracking-[-.065em]
                            leading-[.88] text-white"
                        >
                            Governor
                            <br />

                            <span class="text-white/15">
                                Bem's message
                            </span>
                        </h2>
                        <!-- Governor Bem's message -->


                        <!-- Message -->
                        <div class="relative mt-7 pl-6 md:pl-7">

                            <!-- Vertical line -->
                            <div
                                class="absolute left-0 top-1 bottom-1 w-px
                                bg-gradient-to-b
                                from-cyan-300
                                via-cyan-300/30
                                to-transparent"
                            ></div>

                            <!-- Small line marker -->
                            <div
                                class="absolute -left-[2px] top-0
                                w-[5px] h-7
                                bg-cyan-300
                                shadow-[0_0_15px_rgba(103,232,249,.5)]"
                            ></div>

                            <!-- Message content -->
                            <div
                                class="space-y-4 max-w-2xl
                                text-white/40
                                leading-[1.75]
                                text-[13px] md:text-[14px]"
                            >
                                <p>
                                    Selamat datang di ruang yang kami bangun
                                    bersama—ruang untuk bertemu, bertukar
                                    gagasan, menyampaikan keresahan, dan
                                    menemukan keberanian untuk bergerak.
                                </p>

                                <p>
                                    BEM SAINTEK bukan sekadar organisasi.
                                    Ia adalah ruang belajar yang mempertemukan
                                    berbagai latar belakang, perspektif, dan
                                    mimpi. Di dalamnya, setiap mahasiswa
                                    memiliki kesempatan untuk mengambil peran
                                    dan memberikan dampak.
                                </p>

                                <p class="text-white/65">
                                    Saya percaya bahwa perubahan tidak selalu
                                    dimulai dari sesuatu yang besar. Terkadang,
                                    ia lahir dari satu gagasan sederhana,
                                    satu percakapan, atau satu langkah kecil
                                    yang dilakukan bersama-sama.
                                </p>

                                <p>
                                    Karena itu, mari kita jadikan periode ini
                                    bukan hanya tentang apa yang ingin kita
                                    capai, tetapi juga tentang bagaimana kita
                                    tumbuh, belajar, dan meninggalkan sesuatu
                                    yang berarti bagi mereka yang datang
                                    setelah kita.
                                </p>
                            </div>
                        </div>

                        <!-- ================= SIGNATURE ================= -->
                        <div
                            class="mt-7 pl-6 md:pl-7"
                        >
                            <div
                                class="font-display text-base
                                text-white/75 italic"
                            >
                                Ahmad Tahqiq Rofiq
                              
                            </div>

                            <div
                                class="font-mono text-[12px]
                                tracking-[.3em]
                                text-white/50 mt-1"
                            >
                                Gubernur BEM SAINTEK
                            </div>
                        </div>

                        <!-- ================= STATS FOOTER ================= -->
                        <div
                            class="grid grid-cols-3
                            gap-4 mt-9 pt-5
                            border-t border-white/[0.07]"
                        >
                            <div
                                v-for="s in stats"
                                :key="s.label"
                                class="group"
                            >
                                <div
                                    class="font-display text-2xl md:text-3xl
                                    font-bold tracking-[-.05em]
                                    text-white
                                    group-hover:text-cyan-300
                                    transition-colors"
                                >
                                    {{ s.value }}
                                </div>

                                <div
                                    class="text-[7px] md:text-[8px]
                                    text-white/25 mt-1.5
                                    font-mono tracking-[.13em]"
                                >
                                    {{ s.label }}
                                </div>
                            </div>
                        </div>

                    </div>
                </Reveal>
            </div>
        </div>

        <!-- Bottom decorative text -->
        <div
            class="absolute bottom-4 left-1/2
            -translate-x-1/2
            font-mono text-[6px]
            tracking-[.6em] text-white/[0.08]
            whitespace-nowrap pointer-events-none"
        >
            IDEAS / COURAGE / COLLABORATION / IMPACT
        </div>
    </section>
</template>
