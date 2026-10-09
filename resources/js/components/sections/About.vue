<script setup>
import Reveal from '@/components/ui/Reveal.vue'

defineProps({
    visi: {
        type: String,
        default:
            'Terwujudnya BEM SAINTEK sebagai motor penggerak mahasiswa yang unggul, aspiratif, dan responsif dalam mewujudkan fakultas yang inovatif dan berdaya saing global.',
    },

    misi: {
        type: Array,
        default: () => [
            {
                judul: 'Advokasi Akademik',
                // deskripsi:
                //     'Memperjuangkan aspirasi akademik mahasiswa serta mendorong terciptanya lingkungan belajar yang lebih baik.',
            },
            {
                judul: 'Riset & Inovasi',
                // deskripsi:
                //     'Mendorong budaya riset, kreativitas, dan inovasi mahasiswa Saintek.',
            },
            {
                judul: 'Sinergi & Kolaborasi',
                // deskripsi:
                //     'Membangun kolaborasi antar mahasiswa, HIMA, dan seluruh elemen fakultas.',
            },
            {
                judul: 'Pengabdian Masyarakat',
                // deskripsi:
                //     'Menghadirkan kontribusi nyata mahasiswa melalui kegiatan sosial dan pengabdian.',
            },
            {
                judul: 'Pembinaan Karakter',
                // deskripsi:
                //     'Membentuk mahasiswa yang berintegritas, adaptif, dan memiliki jiwa kepemimpinan.',
            },
        ],
    },
})
</script>

<template>
    <section
        id="about"
        class="relative isolate overflow-hidden py-20 lg:py-24 bg-[#03050a]"
    >
        <!-- ================= BACKGROUND ================= -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">

            <!-- Grid -->
            <div
                class="absolute inset-0 grid-bg opacity-[0.11]"
            ></div>

            <!-- Cyan Glow -->
            <div
                class="absolute -top-40 -left-40
                w-[450px] h-[450px]
                rounded-full
                bg-cyan-400/[0.045]
                blur-[120px]"
            ></div>

            <!-- Violet Glow -->
            <div
                class="absolute -bottom-40 -right-40
                w-[450px] h-[450px]
                rounded-full
                bg-violet-500/[0.045]
                blur-[120px]"
            ></div>

            <!-- Technical circles -->
            <div
                class="absolute top-20 right-[9%]
                w-44 h-44 rounded-full
                border border-cyan-300/[0.05]"
            ></div>

            <div
                class="absolute top-16 right-[7%]
                w-52 h-52 rounded-full
                border border-violet-300/[0.035]"
            ></div>

            <!-- Decorative horizontal lines -->
            <div
                class="absolute top-[25%] left-0
                w-24 h-px
                bg-gradient-to-r
                from-transparent to-cyan-300/20"
            ></div>

            <div
                class="absolute bottom-[20%] right-0
                w-32 h-px
                bg-gradient-to-l
                from-transparent to-violet-300/20"
            ></div>

            <!-- Small glowing dots -->
            <div
                class="absolute top-[40%] right-[13%]
                w-1.5 h-1.5 rounded-full
                bg-cyan-300/40
                shadow-[0_0_15px_rgba(103,232,249,.5)]"
            ></div>

            <div
                class="absolute bottom-[22%] left-[10%]
                w-1 h-1 rounded-full
                bg-violet-300/40"
            ></div>

            <!-- Top technical label -->
            <div
                class="absolute top-8 left-8
                hidden lg:block opacity-25"
            >
                <div class="flex items-center gap-2">
                    <span
                        class="w-1.5 h-1.5 bg-cyan-300"
                    ></span>

                    <span
                        class="w-7 h-px bg-cyan-300"
                    ></span>
                </div>

                <div
                    class="mt-2 font-mono text-[7px]
                    tracking-[.35em] text-cyan-300"
                >
                    SYSTEM / 001
                </div>
            </div>

            <!-- Bottom technical label -->
            <div
                class="absolute bottom-8 right-8
                hidden lg:block opacity-15"
            >
                <div
                    class="font-mono text-[7px]
                    tracking-[.35em] text-white"
                >
                    BEM SAINTEK / EST. 2026
                </div>
            </div>
        </div>


        <!-- ================= MAIN CONTENT ================= -->
        <div
            class="relative max-w-5xl mx-auto
            px-5 lg:px-6"
        >

            <!-- ================= HEADER ================= -->
            <Reveal variant="up">
                <div
                    class="flex items-center gap-3
                    mb-9 lg:mb-11"
                >
                    <span
                        class="w-8 h-px bg-cyan-300"
                    ></span>

                    <span
                        class="font-mono text-[9px]
                        tracking-[.35em]
                        text-cyan-300"
                    >
                        02 / ABOUT US
                    </span>

                    <span
                        class="ml-auto hidden sm:block
                        font-mono text-[8px]
                        tracking-[.25em]
                        text-white/15"
                    >
                        WHO WE ARE
                    </span>
                </div>
            </Reveal>


            <!-- ================= TWO COLUMN ================= -->
            <div
                class="grid lg:grid-cols-2
                gap-10 lg:gap-14
                items-start"
            >

                <!-- ================= LEFT ================= -->
                <Reveal variant="right">
                    <div>

                        <!-- Label -->
                        <div
                            class="flex items-center
                            gap-3 mb-5"
                        >
                            <span
                                class="w-7 h-px
                                bg-cyan-300"
                            ></span>

                            <span
                                class="font-mono
                                text-[8px]
                                tracking-[.35em]
                                text-cyan-300"
                            >
                                TENTANG KAMI
                            </span>
                        </div>


                        <!-- Heading -->
                        <h2
                            class="font-display
                            text-4xl md:text-5xl
                            xl:text-6xl
                            font-bold
                            tracking-[-.065em]
                            leading-[.88]
                            text-white"
                        >
                            APA ITU
                            <br />

                            <span
                                class="text-white/15"
                            >
                                BEM SAINTEK?
                            </span>
                        </h2>


                        <!-- Description -->
                        <div
                            class="relative
                            mt-8 pl-6"
                        >

                            <!-- Vertical Line -->
                            <div
                                class="absolute
                                left-0 top-1 bottom-1
                                w-px
                                bg-gradient-to-b
                                from-cyan-300
                                via-cyan-300/30
                                to-transparent"
                            ></div>

                            <!-- Line Marker -->
                            <div
                                class="absolute
                                -left-[2px] top-0
                                w-[5px] h-7
                                bg-cyan-300
                                shadow-[0_0_15px_rgba(103,232,249,.5)]"
                            ></div>


                            <!-- Text -->
                            <div
                                class="space-y-4
                                text-[12px]
                                md:text-[13px]
                                leading-[1.8]
                                text-white/40"
                            >
                                <p>
                                    BEM SAINTEK adalah organisasi eksekutif
                                    mahasiswa tingkat fakultas yang menjadi
                                    ruang bagi mahasiswa Sains dan Teknologi
                                    untuk menyampaikan aspirasi,
                                    mengembangkan potensi, dan menciptakan
                                    gerakan yang berdampak.
                                </p>

                                <p>
                                    Kami hadir sebagai penghubung antara
                                    mahasiswa, HIMA, dan fakultas untuk
                                    membangun lingkungan akademik yang
                                    aspiratif, kolaboratif, dan inovatif.
                                </p>

                                <p
                                    class="text-white/60"
                                >
                                    Bagi kami, BEM bukan hanya tentang
                                    menjalankan program kerja. BEM adalah
                                    tentang belajar untuk memimpin, berani
                                    menyuarakan, dan bergerak bersama.
                                </p>
                            </div>
                        </div>


                        <!-- Identity -->
                        <div
                            class="flex items-center
                            gap-3 mt-7"
                        >
                            <span
                                class="w-1.5 h-1.5
                                rounded-full
                                bg-cyan-300
                                shadow-[0_0_10px_rgba(103,232,249,.6)]"
                            ></span>

                            <span
                                class="font-mono
                                text-[7px]
                                tracking-[.25em]
                                text-white/20"
                            >
                                BEM SAINTEK /
                                STUDENT MOVEMENT
                            </span>
                        </div>

                    </div>
                </Reveal>


                <!-- ================= RIGHT ================= -->
                <Reveal variant="left">
                    <div>

                        <!-- ================= VISI ================= -->
                        <div
                            class="relative
                            rounded-[1.5rem]
                            border border-white/10
                            bg-white/[0.025]
                            p-6 md:p-7
                            overflow-hidden"
                        >

                            <!-- Glow -->
                            <div
                                class="absolute
                                -top-20 -right-20
                                w-40 h-40
                                rounded-full
                                bg-cyan-300/[0.05]
                                blur-[60px]"
                            ></div>


                            <!-- Card Header -->
                            <div
                                class="relative
                                flex items-center
                                justify-between"
                            >
                                <div
                                    class="flex items-center
                                    gap-3"
                                >
                                    <span
                                        class="w-6 h-px
                                        bg-cyan-300"
                                    ></span>

                                    <span
                                        class="font-mono
                                        text-[8px]
                                        tracking-[.3em]
                                        text-cyan-300"
                                    >
                                        OUR VISION
                                    </span>
                                </div>

                                <span
                                    class="font-mono
                                    text-[7px]
                                    tracking-[.25em]
                                    text-white/15"
                                >
                                    VISION_001
                                </span>
                            </div>


                            <!-- Vision -->
                            <blockquote
                                class="relative
                                font-display
                                text-md md:text-md
                                leading-[1.45]
                                tracking-tight
                                text-white
                                mt-6"
                            >
                                “{{ visi }}”
                            </blockquote>

                        </div>


                        <!-- ================= MISI ================= -->
                        <div class="mt-8">

                            <!-- Mission Header -->
                            <div
                                class="flex items-center
                                gap-3 mb-5"
                            >
                                <span
                                    class="w-6 h-px
                                    bg-violet-300"
                                ></span>

                                <span
                                    class="font-mono
                                    text-[8px]
                                    tracking-[.3em]
                                    text-violet-300"
                                >
                                    OUR MISSION
                                </span>
                            </div>


                            <!-- Mission List -->
                            <div class="space-y-2.5">

                                <div
                                    v-for="(m, i) in misi"
                                    :key="m.judul"
                                    class="group
                                    flex gap-3.5
                                    p-3.5
                                    rounded-xl
                                    border border-white/[0.055]
                                    bg-white/[0.012]
                                    hover:border-cyan-300/20
                                    hover:bg-cyan-300/[0.025]
                                    transition-all duration-300"
                                >

                                    <!-- Number -->
                                    <div
                                        class="shrink-0
                                        font-mono
                                        text-[8px]
                                        tracking-[.15em]
                                        text-cyan-300/60
                                        pt-0.5"
                                    >
                                        0{{ i + 1 }}
                                    </div>


                                    <!-- Mission -->
                                    <div>

                                        <h3
                                            class="font-display
                                            text-[13px]
                                            font-bold
                                            text-white"
                                        >
                                            {{ m.judul }}
                                        </h3>

                                        <p
                                            class="text-[10px]
                                            md:text-[11px]
                                            leading-[1.6]
                                            text-white/30
                                            mt-1"
                                        >
                                            {{ m.deskripsi }}
                                        </p>

                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>
                </Reveal>

            </div>
        </div>


        <!-- ================= BOTTOM ================= -->
        <div
            class="absolute bottom-3
            left-1/2
            -translate-x-1/2
            font-mono text-[6px]
            tracking-[.6em]
            text-white/[0.06]
            whitespace-nowrap"
        >
            SCIENCE / TECHNOLOGY /
            COLLABORATION / IMPACT
        </div>

    </section>
</template>
