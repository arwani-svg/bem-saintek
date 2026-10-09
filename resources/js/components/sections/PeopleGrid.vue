<script setup>
import Reveal from '@/components/ui/Reveal.vue'

const props = defineProps({
    images: {
        type: Array,
        default: () => [
            'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=700&q=85',
            'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=700&q=85',
            'https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=700&q=85',
            'https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&w=700&q=85',
        ],
    },
})
</script>

<template>
    <section
        id="join"
        class="relative isolate overflow-hidden py-28 lg:py-36 bg-[#03050a]"
    >
        <!-- ================= BACKGROUND ================= -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">

            <!-- Grid -->
            <div
                class="absolute inset-0 grid-bg opacity-[0.12]"
            ></div>

            <!-- Cyan Glow -->
            <div
                class="absolute -left-40 top-1/3
                w-[500px] h-[500px]
                rounded-full
                bg-cyan-400/[0.05]
                blur-[130px]"
            ></div>

            <!-- Violet Glow -->
            <div
                class="absolute -right-40 -bottom-40
                w-[500px] h-[500px]
                rounded-full
                bg-violet-500/[0.05]
                blur-[130px]"
            ></div>

            <!-- Decorative circles -->
            <div
                class="absolute top-20 right-[10%]
                w-48 h-48 rounded-full
                border border-cyan-300/[0.05]"
            ></div>

            <div
                class="absolute top-16 right-[8%]
                w-56 h-56 rounded-full
                border border-violet-300/[0.035]"
            ></div>

            <!-- Horizontal lines -->
            <div
                class="absolute top-[30%] left-0
                w-32 h-px
                bg-gradient-to-r
                from-transparent to-cyan-300/20"
            ></div>

            <div
                class="absolute bottom-[25%] right-0
                w-40 h-px
                bg-gradient-to-l
                from-transparent to-violet-300/20"
            ></div>
        </div>


        <!-- ================= CONTENT ================= -->
        <div class="relative max-w-5xl mx-auto px-5 lg:px-6">

            <div
                class="grid lg:grid-cols-[.9fr_1fr]
                gap-12 lg:gap-16
                items-center"
            >

                <!-- ================= TEXT ================= -->
                <Reveal variant="right">
                    <div>

                        <!-- Eyebrow -->
                        <div
                            class="flex items-center gap-3 mb-6"
                        >
                            <span
                                class="w-8 h-px bg-pink-300"
                            ></span>

                            <span
                                class="font-mono text-[9px]
                                text-pink-300
                                tracking-[.35em]"
                            >
                                06 / JOIN THE MOVEMENT
                            </span>
                        </div>


                        <!-- Heading -->
                        <h2
                            class="font-display
                            text-5xl md:text-6xl
                            xl:text-7xl
                            font-bold
                            tracking-[-.065em]
                            leading-[.86]
                            text-white"
                        >
                            JOIN
                            <br />

                            <span class="text-white/20">
                                WITH US.
                            </span>
                        </h2>


                        <!-- Description -->
                        <div
                            class="relative
                            mt-8 pl-6"
                        >

                            <!-- Vertical line -->
                            <div
                                class="absolute
                                left-0 top-1 bottom-1
                                w-px
                                bg-gradient-to-b
                                from-pink-300
                                via-pink-300/30
                                to-transparent"
                            ></div>

                            <!-- Marker -->
                            <div
                                class="absolute
                                -left-[2px] top-0
                                w-[5px] h-7
                                bg-pink-300
                                shadow-[0_0_15px_rgba(249,168,212,.5)]"
                            ></div>


                            <div
                                class="space-y-4
                                text-[12px] md:text-[13px]
                                leading-[1.8]
                                text-white/40
                                max-w-md"
                            >
                                <p>
                                    BEM SAINTEK bukan hanya tentang organisasi.
                                    Ini adalah ruang untuk bertemu dengan orang
                                    baru, mengembangkan diri, menyampaikan
                                    gagasan, dan menciptakan sesuatu yang
                                    berarti bersama.
                                </p>

                                <p>
                                    Kalau kamu punya ide, keresahan, semangat
                                    untuk belajar, atau sekadar ingin mencoba
                                    sesuatu yang baru — mungkin sudah waktunya
                                    kamu menjadi bagian dari gerakan ini.
                                </p>

                                <p class="text-white/60">
                                    Tidak harus hebat untuk memulai.
                                    Cukup berani untuk mengambil langkah pertama.
                                </p>
                            </div>
                        </div>


                        <!-- CTA -->
                        <a
                            href="/join"
                            class="group
                            inline-flex items-center
                            gap-4 mt-8
                            px-5 py-3
                            rounded-full
                            border border-pink-300/30
                            bg-pink-300/[0.06]
                            text-pink-200
                            font-mono text-[10px]
                            tracking-[.2em]
                            hover:bg-pink-300
                            hover:text-[#03050a]
                            hover:border-pink-300
                            transition-all duration-300"
                        >
                            JOIN BEM SAINTEK

                            <span
                                class="flex items-center
                                justify-center
                                w-6 h-6
                                rounded-full
                                border border-current
                                group-hover:translate-x-1
                                transition-transform"
                            >
                                <svg
                                    class="w-3 h-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"
                                    />
                                </svg>
                            </span>
                        </a>


                        <!-- Small identity -->
                        <div
                            class="flex items-center
                            gap-3 mt-7"
                        >
                            <span
                                class="w-1.5 h-1.5
                                rounded-full
                                bg-pink-300
                                shadow-[0_0_10px_rgba(249,168,212,.6)]"
                            ></span>

                            <span
                                class="font-mono text-[7px]
                                tracking-[.25em]
                                text-white/20"
                            >
                                YOUR IDEAS / YOUR VOICE / YOUR MOVEMENT
                            </span>
                        </div>

                    </div>
                </Reveal>


                <!-- ================= IMAGES ================= -->
                <Reveal variant="left">
                    <div
                        class="relative grid
                        grid-cols-2 gap-3
                        max-w-md mx-auto"
                    >

                        <!-- Decorative frame -->
                        <div
                            class="absolute
                            -inset-4
                            border border-pink-300/[0.06]
                            rounded-[2rem]
                            rotate-2"
                        ></div>

                        <div
                            class="absolute
                            -inset-4
                            border border-violet-300/[0.04]
                            rounded-[2rem]
                            -rotate-2"
                        ></div>


                        <!-- Images -->
                        <div
                            v-for="(img, i) in images"
                            :key="img"
                            class="image-card
                            relative
                            rounded-[1.4rem]
                            aspect-[.82]
                            overflow-hidden
                            border border-white/10
                            bg-white/[0.03]"
                            :class="
                                i % 2 === 1
                                    ? 'mt-10'
                                    : i >= 2
                                      ? '-mt-10'
                                      : ''
                            "
                        >

                            <img
                                :src="img"
                                alt="Mahasiswa BEM Saintek"
                                class="w-full h-full
                                object-cover
                                grayscale
                                transition-all
                                duration-700
                                hover:grayscale-0
                                hover:scale-[1.04]"
                                loading="lazy"
                            />

                            <!-- Overlay -->
                            <div
                                class="absolute inset-0
                                bg-gradient-to-t
                                from-[#03050a]/70
                                via-transparent
                                to-transparent"
                            ></div>

                            <!-- Image number -->
                            <div
                                class="absolute
                                top-3 left-3
                                font-mono
                                text-[7px]
                                tracking-[.2em]
                                text-white/40"
                            >
                                0{{ i + 1 }}
                            </div>
                        </div>


                        <!-- Center label -->
                        <div
                            class="absolute
                            left-1/2 top-1/2
                            -translate-x-1/2
                            -translate-y-1/2
                            px-4 py-2
                            rounded-full
                            border border-white/15
                            bg-[#03050a]/70
                            backdrop-blur-md"
                        >
                            <span
                                class="font-mono
                                text-[8px]
                                tracking-[.3em]
                                text-white/70
                                whitespace-nowrap"
                            >
                                BE PART OF IT
                            </span>
                        </div>

                    </div>
                </Reveal>

            </div>
        </div>


        <!-- ================= BOTTOM TEXT ================= -->
        <div
            class="absolute bottom-4
            left-1/2
            -translate-x-1/2
            font-mono text-[6px]
            tracking-[.6em]
            text-white/[0.06]
            whitespace-nowrap"
        >
            JOIN / CONNECT / CREATE / IMPACT
        </div>

    </section>
</template>
