<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import BackToTop from '@/components/ui/BackToTop.vue'
import Reveal from '@/components/ui/Reveal.vue'
import StrukturalCard from '@/components/sections/StrukturalCard.vue'
import StrukturalDeptCard from '@/components/sections/StrukturalDeptCard.vue'

const props = defineProps({
    periods: {
        type: Array,
        default: () => [],
    },
    selectedPeriode: {
        type: String,
        default: '',
    },
    pengurusHarian: {
        type: Array,
        default: () => [],
    },
    departemens: {
        type: Array,
        default: () => [],
    },
    totalPengurus: {
        type: Number,
        default: 0,
    },
})

const hasData = computed(() => props.pengurusHarian.length > 0 || props.departemens.length > 0)
const totalDepartemen = computed(() => props.departemens.length)

const periodeLabel = computed(() => props.selectedPeriode || '—')
const periodeShort = computed(() => {
    if (!props.selectedPeriode) return '—'

    const [awal, akhir] = props.selectedPeriode.split('/')
    return akhir ? `${awal}/${akhir.slice(-2)}` : props.selectedPeriode
})

const marqueeWords = ['BEYOND LIMITS', 'KADERISASI', 'NALAR BAKAT', 'INFOKOM', 'EKONOMI', 'RELASI', 'BEM SAINTEK']
const heroImage = '/images/ujicobaku.png'

const changePeriode = (event) => {
    router.get('/profile/struktural', { periode: event.target.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const pad = (number) => String(number).padStart(2, '0')
</script>

<template>
    <Head title="Struktural - Profil" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main class="overflow-x-clip">
            <section class="relative overflow-hidden pb-16 pt-28 lg:pb-24 lg:pt-36">
                <div class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div class="grid-bg absolute inset-0 opacity-[0.11]"></div>
                    <div class="absolute -top-32 left-1/4 h-[420px] w-[520px] rounded-full bg-cyan-400/[0.07] blur-[130px]"></div>
                    <div class="absolute bottom-[-14rem] right-[8%] h-[420px] w-[420px] rounded-full bg-violet-500/[0.07] blur-[130px]"></div>
                </div>

                <div class="relative mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="grid items-center gap-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.05fr)] lg:gap-16">
                        <div class="text-left">
                            <nav class="mb-6 flex items-center gap-2 font-mono text-[10px] tracking-[.2em] text-white/30">
                                <Link href="/" class="transition hover:text-cyan-300">BERANDA</Link>
                                <span class="text-white/15">/</span>
                                <span class="text-white/30">PROFIL</span>
                                <span class="text-white/15">/</span>
                                <span class="text-white/60">STRUKTURAL</span>
                            </nav>

                            <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/5 px-4 py-1.5 font-mono text-[10px] tracking-[.25em] text-cyan-300">
                                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-cyan-300"></span>
                                KABINET {{ selectedPeriode || 'BERJALAN' }}
                            </div>

                            <h1 class="font-display text-[2.6rem] font-bold leading-[1.03] tracking-tight sm:text-5xl lg:text-[3.7rem]">
                                <span class="hero-word" style="animation-delay: .15s">Struktur</span>
                                <span class="hero-word gradient-text" style="animation-delay: .3s">BEM&nbsp;SAINTEK.</span>
                            </h1>

                            <p class="mt-5 max-w-lg text-sm leading-relaxed text-white/45 sm:text-[15px]">
                                Bersama Bergerak, Bersama Berdampak — para pengurus yang menggerakkan kabinet,
                                ditata dalam satu struktur yang rapi, terbuka, dan mudah dipelajari siapa saja.
                            </p>

                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="#struktur" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-black transition hover:bg-cyan-300">
                                    Jelajahi Kepengurusan
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>
                                <a href="#departemen" class="inline-flex items-center gap-2 rounded-full border border-white/12 px-6 py-3 text-sm font-medium text-white/70 transition hover:border-cyan-300/40 hover:bg-cyan-300/5 hover:text-white">
                                    Lihat Departemen
                                </a>
                            </div>

                            <div v-if="periods.length > 0" class="mt-6 flex items-center gap-3">
                                <span class="font-mono text-[9px] tracking-[.25em] text-white/30">PERIODE</span>
                                <select
                                    :value="selectedPeriode"
                                    class="glass rounded-full px-4 py-2 font-mono text-[10px] tracking-[.15em] text-white/70 outline-none transition focus:border-cyan-300/50"
                                    @change="changePeriode"
                                >
                                    <option v-for="p in periods" :key="p" :value="p" class="bg-[#070b14]">
                                        {{ p }}
                                    </option>
                                </select>
                            </div>

                            <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-5 border-t border-white/5 pt-7">
                                <div>
                                    <div class="font-display text-2xl font-bold">
                                        {{ periodeShort.split('/')[0] || '—' }}<span class="text-cyan-300">/{{ periodeShort.split('/')[1] || '—' }}</span>
                                    </div>
                                    <div class="mt-1 font-mono text-[9px] tracking-[.25em] text-white/30">KABINET BERJALAN</div>
                                </div>
                                <span class="hidden h-9 w-px bg-white/10 sm:block"></span>
                                <div>
                                    <div class="font-display text-2xl font-bold">{{ pad(totalDepartemen) }}</div>
                                    <div class="mt-1 font-mono text-[9px] tracking-[.25em] text-white/30">DEPARTEMEN</div>
                                </div>
                                <span class="hidden h-9 w-px bg-white/10 sm:block"></span>
                                <div>
                                    <div class="font-display text-2xl font-bold">{{ pad(totalPengurus) }}</div>
                                    <div class="mt-1 font-mono text-[9px] tracking-[.25em] text-white/30">TOTAL PENGURUS</div>
                                </div>
                            </div>
                        </div>

                        <div class="struct-hero-visual">
                            <div class="struct-hero-ambient" aria-hidden="true"></div>
                            <div class="struct-hero-orbit" aria-hidden="true"></div>

                            <div class="struct-hero-stage">
                                <div class="struct-hero-rays" aria-hidden="true"><i class="struct-hero-rays-spin"></i></div>
                                <div class="struct-hero-shine" aria-hidden="true"><i class="struct-hero-shine-sweep"></i></div>
                                <div class="struct-hero-aura-wide" aria-hidden="true">
                                    <img :src="heroImage" alt="" class="struct-hero-aura-wide-img" />
                                </div>
                                <div class="struct-hero-aura" aria-hidden="true">
                                    <img :src="heroImage" alt="" class="struct-hero-aura-img" />
                                </div>
                                <img :src="heroImage" alt="Struktur Organisasi BEM SAINTEK" class="struct-hero-img" />
                            </div>

                            <div class="struct-hero-base" aria-hidden="true"></div>

                            <div class="struct-hero-chip glass absolute -top-4 right-3 rounded-full px-4 py-2 font-mono text-[9px] tracking-[.25em] text-cyan-300 shadow-[0_10px_30px_rgba(0,0,0,.45)] sm:right-6">
                                ✦ BEYOND LIMITS
                            </div>

                            <div class="struct-hero-chip glass absolute -bottom-6 left-3 rounded-2xl px-5 py-4 shadow-[0_18px_44px_rgba(0,0,0,.5)] sm:left-0">
                                <div class="font-mono text-[9px] tracking-[.25em] text-cyan-300">LEBIH DARI SEKADAR</div>
                                <div class="font-display mt-1 text-sm font-bold leading-snug">
                                    Struktur yang transparan,<br />tanggung jawab yang jelas.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="marquee border-y border-white/5 py-4 select-none">
                <div class="marquee-track font-mono text-[10px] tracking-[.3em] text-white/25">
                    <div v-for="n in 2" :key="n" class="flex shrink-0 items-center gap-8 pr-8" aria-hidden="true">
                        <template v-for="word in marqueeWords" :key="`${n}-${word}`">
                            <span class="flex items-center gap-8">
                                <span>{{ word }}</span>
                                <span class="text-cyan-300">✦</span>
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            <div id="struktur">
                <section v-if="!hasData" class="relative py-20">
                    <div class="mx-auto max-w-5xl px-5 lg:px-8">
                        <Reveal>
                            <div class="glass rounded-3xl p-10 text-center sm:p-14">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-cyan-300/20 bg-cyan-300/5 text-2xl">👥</div>
                                <h2 class="font-display mt-5 text-2xl font-bold text-white">Struktur belum tersedia</h2>
                                <p class="mx-auto mt-2 max-w-md text-sm leading-7 text-white/40">
                                    Data kepengurusan belum diisi untuk periode {{ periodeLabel }}. Silakan cek kembali nanti.
                                </p>
                            </div>
                        </Reveal>
                    </div>
                </section>

                <template v-else>
                    <section v-if="pengurusHarian.length > 0" class="relative py-14">
                        <div class="mx-auto max-w-7xl px-5 lg:px-8">
                            <Reveal class="mb-8 text-center">
                                <div class="font-mono text-[10px] tracking-[.3em] text-cyan-300">01 / PENGURUS HARIAN</div>
                                <h2 class="font-display mt-2 text-2xl font-bold tracking-tight sm:text-3xl">
                                    Pengurus <span class="gradient-text">Harian</span>
                                </h2>
                                <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-white/40">
                                    Inti kepengurusan yang mengoordinasikan arah gerak kabinet.
                                </p>
                            </Reveal>

                            <div class="grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                                <Reveal
                                    v-for="(p, index) in pengurusHarian"
                                    :key="p.id"
                                    variant="up"
                                    :delay="index * 60"
                                    class="h-full"
                                >
                                    <StrukturalCard :pengurus="p" />
                                </Reveal>
                            </div>
                        </div>
                    </section>

                    <section v-if="departemens.length > 0" id="departemen" class="relative pb-20 pt-8">
                        <div class="mx-auto max-w-7xl px-5 lg:px-8">
                            <Reveal class="mb-10 text-center">
                                <div class="mb-2 font-mono text-[10px] tracking-[.3em] text-cyan-300">02 / DEPARTEMEN KABINET</div>
                                <h2 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">
                                    {{ departemens.length }} departemen, <span class="gradient-text">satu gerakan.</span>
                                </h2>
                                <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-white/40">
                                    Setiap departemen bergerak dalam bidangnya masing-masing untuk satu tujuan yang sama.
                                </p>
                            </Reveal>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                                <Reveal
                                    v-for="(d, index) in departemens"
                                    :key="d.id"
                                    :delay="index * 60"
                                    class="h-full"
                                >
                                    <StrukturalDeptCard
                                        :departemen="d"
                                        :index="index"
                                        :periode="selectedPeriode"
                                    />
                                </Reveal>
                            </div>
                        </div>
                    </section>

                    <section id="join" class="relative pb-20">
                        <div class="mx-auto max-w-5xl px-5 lg:px-8">
                            <Reveal>
                                <div class="rounded-3xl border border-cyan-300/20 bg-gradient-to-br from-cyan-300/10 via-white/[0.02] to-violet-500/10 p-8 text-center sm:p-12">
                                    <div class="mb-4 font-mono text-[10px] tracking-[.3em] text-cyan-300">OUR COMMITMENT</div>
                                    <h3 class="font-display text-2xl font-bold leading-tight sm:text-4xl">
                                        Bersatu dalam gagasan,<br /><span class="gradient-text">bergerak dalam tindakan.</span>
                                    </h3>
                                    <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-white/40">
                                        Ruang untuk bertumbuh, berkolaborasi, dan memberi manfaat bagi seluruh mahasiswa.
                                    </p>
                                    <div class="mt-7 flex flex-wrap justify-center gap-3">
                                        <Link href="/join" class="rounded-full bg-white px-6 py-3 text-sm font-bold text-black transition hover:bg-cyan-300">
                                            Daftar OPREC →
                                        </Link>
                                        <Link href="/kontak" class="rounded-full border border-white/10 px-6 py-3 text-sm text-white/60 transition hover:border-cyan-300/30 hover:text-white">
                                            Hubungi kami
                                        </Link>
                                    </div>
                                </div>
                            </Reveal>
                        </div>
                    </section>
                </template>
            </div>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
