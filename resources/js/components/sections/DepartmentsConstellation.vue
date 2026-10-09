<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Reveal from '@/components/ui/Reveal.vue'

const page = usePage()
const navDepartemens = computed(() => page.props.navDepartemens ?? [])

// Fallback jika data dari database belum tersedia
const fallback = [
    {
        key: 'RISTEK',
        desc: 'Mendorong riset, teknologi, digitalisasi, dan inovasi mahasiswa menjadi solusi yang relevan.',
    },
    {
        key: 'PSDM',
        desc: 'Membangun kapasitas mahasiswa melalui leadership, mentoring, pelatihan, dan pengembangan potensi.',
    },
    {
        key: 'KOMINFO',
        desc: 'Mengelola komunikasi, informasi, publikasi, branding, dan media digital organisasi.',
    },
    {
        key: 'SOSMAS',
        desc: 'Menghubungkan ilmu mahasiswa dengan kebutuhan masyarakat melalui pengabdian dan aksi sosial.',
    },
    {
        key: 'EKRAF',
        desc: 'Membuka ruang kreativitas, kewirausahaan, dan pengembangan potensi ekonomi mahasiswa.',
    },
    {
        key: 'ADVOKASI',
        desc: 'Mengawal aspirasi, hak, dan kesejahteraan mahasiswa secara responsif dan berbasis data.',
    },
]

// Warna border node digilir mengikuti jumlah departemen
const palette = [
    'border-cyan-300/30',
    'border-violet-300/30',
    'border-blue-300/30',
    'border-pink-300/30',
    'border-yellow-300/30',
    'border-green-300/30',
]

const nodes = computed(() => {
    if (navDepartemens.value.length) {
        return navDepartemens.value.map((d, i) => ({
            id: d.id,
            key: d.nama,
            icon: d.icon,
            // Utamakan deskripsi dari database
            desc:
                d.deskripsi ||
                d.kepanjangan ||
                'Departemen BEM SAINTEK.',
            border: palette[i % palette.length],
        }))
    }

    return fallback.map((f, i) => ({
        id: null,
        icon: null,
        key: f.key,
        desc: f.desc,
        border: palette[i % palette.length],
    }))
})

const selected = ref({
    key: 'Ekosistem SAINTEK',
    desc: 'Pilih salah satu departemen untuk melihat perannya.',
    id: null,
})

function select(n) {
    selected.value = {
        key: n.key,
        desc: n.desc,
        id: n.id ?? null,
    }
}

// Posisi node disebar merata mengelilingi pusat (elips) —
// rapi untuk jumlah departemen berapa pun dan nama sepanjang apa pun,
// karena nama tampil sebagai caption di bawah lingkaran ikon.
function nodePos(i, total) {
    const angle = (-90 + (360 / total) * i) * (Math.PI / 180)
    const x = 50 + 37 * Math.cos(angle)
    const y = 50 + 40 * Math.sin(angle)
    return `left:${x.toFixed(2)}%;top:${y.toFixed(2)}%;transform:translate(-50%,-50%)`
}

// Garis dari pusat ke tiap node
function lineStyle(i, total) {
    const deg = -90 + (360 / total) * i
    return `transform:rotate(${deg}deg)`
}
</script>

<template>
    <section
        id="departments"
        class="relative isolate overflow-hidden py-14 lg:py-20 bg-[#03050a]"
    >
        <!-- ================= BACKGROUND ================= -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">

            <div
                class="absolute inset-0 grid-bg opacity-[0.12]"
            ></div>

            <div
                class="absolute -top-40 -right-40
                w-[500px] h-[500px]
                rounded-full
                bg-cyan-400/[0.045]
                blur-[130px]"
            ></div>

            <div
                class="absolute -bottom-40 -left-40
                w-[500px] h-[500px]
                rounded-full
                bg-violet-500/[0.045]
                blur-[130px]"
            ></div>

            <div
                class="absolute top-[20%] right-[8%]
                w-48 h-48 rounded-full
                border border-cyan-300/[0.04]"
            ></div>

            <div
                class="absolute bottom-[15%] left-[8%]
                w-32 h-32 rounded-full
                border border-violet-300/[0.035]"
            ></div>

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
        <div
            class="relative max-w-5xl mx-auto
            px-5 lg:px-6"
        >

            <!-- ================= HEADER ================= -->
            <Reveal variant="up">
                <div
                    class="flex items-center gap-3
                    mb-8 lg:mb-10"
                >
                    <span
                        class="w-8 h-px bg-cyan-300"
                    ></span>

                    <span
                        class="font-mono text-[9px]
                        text-cyan-300
                        tracking-[.35em]"
                    >
                        04 / OUR DEPARTMENTS
                    </span>

                    <span
                        class="ml-auto hidden sm:block
                        font-mono text-[8px]
                        tracking-[.25em]
                        text-white/15"
                    >
                        THE ECOSYSTEM
                    </span>
                </div>
            </Reveal>


            <!-- ================= MAIN GRID ================= -->
            <div
                class="grid lg:grid-cols-[.7fr_1.3fr]
                gap-12 lg:gap-16
                items-center"
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
                                class="font-mono text-[8px]
                                text-cyan-300
                                tracking-[.35em]"
                            >
                                EXPLORE THE TEAM
                            </span>
                        </div>


                        <!-- Heading -->
                        <h2
                            class="font-display
                            text-5xl md:text-6xl
                            xl:text-7xl
                            font-bold
                            tracking-[-.065em]
                            leading-[.87]
                            text-white"
                        >
                            EXPLORE
                            <br />

                            <span class="text-white/20">
                                OUR
                            </span>

                            <br />

                            <span class="gradient-text">
                                DEPARTMENTS.
                            </span>
                        </h2>


                        <!-- Description -->
                        <div
                            class="relative
                            mt-8 pl-6"
                        >

                            <div
                                class="absolute
                                left-0 top-1 bottom-1
                                w-px
                                bg-gradient-to-b
                                from-cyan-300
                                via-cyan-300/30
                                to-transparent"
                            ></div>

                            <div
                                class="absolute
                                -left-[2px] top-0
                                w-[5px] h-7
                                bg-cyan-300
                                shadow-[0_0_15px_rgba(103,232,249,.5)]"
                            ></div>

                            <p
                                class="text-[12px]
                                md:text-[13px]
                                text-white/40
                                leading-[1.8]
                                max-w-sm"
                            >
                                Setiap departemen memiliki peran,
                                karakter, dan cara bergeraknya sendiri.
                                Bersama-sama mereka membentuk satu
                                ekosistem BEM SAINTEK.
                            </p>
                        </div>


                        <!-- Selected Info -->
                        <div
                            class="mt-8 p-5
                            rounded-2xl
                            border border-white/[0.07]
                            bg-white/[0.02]
                            backdrop-blur-sm
                            min-h-[150px]"
                        >

                            <div
                                class="font-mono text-[8px]
                                text-cyan-300
                                tracking-[.25em]"
                            >
                                SELECT DEPARTMENT
                            </div>

                            <h3
                                class="font-display
                                text-xl
                                font-bold
                                mt-2
                                text-white"
                            >
                                {{ selected.key }}
                            </h3>

                            <p
                                class="text-[11px]
                                text-white/35
                                mt-2
                                leading-relaxed"
                            >
                                {{ selected.desc }}
                            </p>

                            <Link
                                v-if="selected.id"
                                :href="`/departemen/${selected.id}`"
                                class="inline-flex
                                items-center
                                gap-2
                                mt-3
                                font-mono
                                text-[9px]
                                tracking-[.15em]
                                text-cyan-300
                                hover:text-white
                                transition"
                            >
                                EXPLORE DEPARTMENT

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
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </Link>
                        </div>

                    </div>
                </Reveal>


                <!-- ================================================= -->
                <!-- DESKTOP CONSTELLATION -->
                <!-- ================================================= -->
                <Reveal variant="left">
                    <div
                        class="hidden md:block
                        relative
                        h-[560px] lg:h-[600px]"
                    >

                        <!-- Lines -->
                        <div
                            v-for="(n, i) in nodes"
                            :key="'line-' + n.key"
                            class="constellation-line
                            w-[37%] left-[50%] top-[50%]"
                            :style="lineStyle(i, nodes.length)"
                        ></div>


                        <!-- Center -->
                        <button
                            class="constellation-node
                            node-center
                            border border-cyan-300/50
                            text-white"
                            style="
                                background:
                                radial-gradient(
                                    circle,
                                    rgba(34,211,238,.12),
                                    rgba(255,255,255,.02) 55%,
                                    transparent
                                )
                            "
                            @click="
                                selected = {
                                    key: 'BEM SAINTEK',
                                    desc: 'Pusat koordinasi yang menghubungkan seluruh departemen dan memastikan seluruh gerakan berjalan menuju arah yang sama.',
                                    id: null
                                }
                            "
                        >
                            <span>
                                BEM<br />
                                SAINTEK
                            </span>
                        </button>


                        <!-- Nodes: lingkaran ikon + caption nama di bawahnya -->
                        <button
                            v-for="(n, i) in nodes"
                            :key="n.key"
                            class="group absolute
                            flex w-[110px]
                            flex-col items-center
                            gap-2"
                            :style="nodePos(i, nodes.length)"
                            @click="select(n)"
                        >
                            <span
                                class="flex h-[76px] w-[76px]
                                items-center justify-center
                                rounded-full border
                                bg-white/[0.02]
                                text-[26px]
                                transition-all duration-300
                                group-hover:scale-110"
                                :class="[
                                    n.border,
                                    selected.key === n.key
                                        ? '!border-cyan-300 bg-cyan-300/10 scale-110 shadow-[0_0_35px_rgba(34,211,238,0.3)]'
                                        : '',
                                ]"
                            >
                                {{ n.icon || '✦' }}
                            </span>

                            <span
                                class="text-center
                                font-display text-[10px]
                                font-bold uppercase
                                leading-tight tracking-wider
                                transition-colors"
                                :class="
                                    selected.key === n.key
                                        ? 'text-white'
                                        : 'text-white/55 group-hover:text-white'
                                "
                            >
                                {{ n.key }}
                            </span>
                        </button>

                    </div>
                </Reveal>


                <!-- ================================================= -->
                <!-- MOBILE DEPARTMENT LIST -->
                <!-- ================================================= -->
                <Reveal variant="up">
                    <div
                        class="md:hidden
                        mt-2"
                    >

                        <div
                            class="font-mono text-[8px]
                            text-white/20
                            tracking-[.3em]
                            mb-4"
                        >
                            SELECT A DEPARTMENT
                        </div>


                        <div class="space-y-2.5">

                            <button
                                v-for="(n, i) in nodes"
                                :key="n.key"
                                class="mobile-department"
                                :class="{
                                    'mobile-active':
                                        selected.key === n.key,
                                }"
                                @click="select(n)"
                            >

                                <!-- Number -->
                                <span
                                    class="mobile-number"
                                >
                                    0{{ i + 1 }}
                                </span>


                                <!-- Icon -->
                                <span
                                    v-if="n.icon"
                                    class="mr-1 text-base leading-none"
                                >
                                    {{ n.icon }}
                                </span>


                                <!-- Name -->
                                <span
                                    class="mobile-name"
                                >
                                    {{ n.key }}
                                </span>


                                <!-- Arrow -->
                                <span
                                    class="mobile-arrow"
                                >
                                    <svg
                                        class="w-3.5 h-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M5 12h14m-5-5 5 5-5 5"
                                        />
                                    </svg>
                                </span>

                            </button>

                        </div>


                        <!-- Mobile Selected Description -->
                        <div
                            class="mt-5
                            p-5
                            rounded-2xl
                            border border-cyan-300/10
                            bg-cyan-300/[0.025]"
                        >

                            <div
                                class="flex items-center
                                justify-between"
                            >
                                <span
                                    class="font-mono
                                    text-[8px]
                                    tracking-[.25em]
                                    text-cyan-300"
                                >
                                    {{ selected.key }}
                                </span>

                                <span
                                    class="w-1.5 h-1.5
                                    rounded-full
                                    bg-cyan-300
                                    shadow-[0_0_10px_rgba(103,232,249,.6)]"
                                ></span>
                            </div>

                            <p
                                class="text-[11px]
                                text-white/35
                                leading-relaxed
                                mt-3"
                            >
                                {{ selected.desc }}
                            </p>

                            <Link
                                v-if="selected.id"
                                :href="`/departemen/${selected.id}`"
                                class="inline-flex
                                items-center gap-2
                                mt-4
                                font-mono text-[8px]
                                tracking-[.15em]
                                text-cyan-300"
                            >
                                EXPLORE

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
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </Link>

                        </div>

                    </div>
                </Reveal>

            </div>
        </div>


        <!-- Bottom decoration -->
        <div
            class="absolute bottom-4
            left-1/2
            -translate-x-1/2
            font-mono text-[6px]
            tracking-[.6em]
            text-white/[0.06]
            whitespace-nowrap
            pointer-events-none"
        >
            {{ nodes.map((n) => n.key).join(' / ').toUpperCase() }}
        </div>

    </section>
</template>


<style scoped>
/* =========================
   DESKTOP NODE POSITIONS (nonaktif — posisi kini dihitung dinamis) ======
========================= */

.node-1 {
    left: 50%;
    top: 5%;
    transform: translateX(-50%);
}

.node-2 {
    left: 12%;
    top: 24%;
}

.node-3 {
    right: 12%;
    top: 24%;
}

.node-4 {
    left: 5%;
    bottom: 18%;
}

.node-5 {
    right: 5%;
    bottom: 18%;
}

.node-6 {
    left: 50%;
    bottom: 2%;
    transform: translateX(-50%);
}


/* =========================
   MOBILE DEPARTMENTS
========================= */

.mobile-department {
    position: relative;

    width: 100%;

    display: flex;
    align-items: center;

    min-height: 54px;

    padding: 0 15px;

    border: 1px solid rgba(255,255,255,.07);

    border-radius: 14px;

    background:
        linear-gradient(
            90deg,
            rgba(255,255,255,.025),
            rgba(255,255,255,.01)
        );

    color: rgba(255,255,255,.75);

    text-align: left;

    transition:
        border-color .3s ease,
        background .3s ease,
        transform .3s ease;
}

.mobile-department:hover,
.mobile-department:focus {
    border-color: rgba(103,232,249,.25);

    background:
        linear-gradient(
            90deg,
            rgba(103,232,249,.06),
            rgba(255,255,255,.015)
        );

    transform: translateX(3px);
}


/* Active department */

.mobile-department.mobile-active {
    border-color: rgba(103,232,249,.35);

    background:
        linear-gradient(
            90deg,
            rgba(103,232,249,.09),
            rgba(103,232,249,.015)
        );
}


/* Number */

.mobile-number {
    width: 30px;

    font-family: monospace;

    font-size: 8px;

    letter-spacing: .15em;

    color: rgba(103,232,249,.55);
}


/* Department name */

.mobile-name {
    flex: 1;

    font-family: var(--font-display, sans-serif);

    font-size: 13px;

    font-weight: 700;

    letter-spacing: -.01em;

    color: rgba(255,255,255,.85);
}


/* Arrow */

.mobile-arrow {
    display: flex;

    align-items: center;

    justify-content: center;

    color: rgba(255,255,255,.2);

    transition:
        color .3s ease,
        transform .3s ease;
}

.mobile-department:hover .mobile-arrow,
.mobile-department.mobile-active .mobile-arrow {
    color: rgb(103,232,249);

    transform: translateX(3px);
}


/* =========================
   MOBILE RESPONSIVE
========================= */

@media (max-width: 700px) {

    .mobile-department {
        min-height: 56px;
    }

}
</style>
