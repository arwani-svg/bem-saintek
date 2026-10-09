<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

// === EDIT DI SINI: info kontak resmi, tinggal ganti ===
const infoKontak = [
    {
        icon: '📍',
        judul: 'Sekretariat',
        isi: 'Gedung Student Center Lt. 1, Fakultas Sains dan Teknologi',
        sub: '[Nama Universitas] • [Alamat Lengkap]',
        warna: 'from-blue-500 to-blue-600',
    },
    {
        icon: '✉️',
        judul: 'Email',
        isi: 'bemsaintek@kampus.ac.id',
        sub: 'Respon maks. 1×24 jam kerja',
        warna: 'from-indigo-500 to-indigo-600',
    },
    {
        icon: '💬',
        judul: 'WhatsApp',
        isi: '+62 812-3456-7890',
        sub: 'Senin–Jumat, 09.00–16.00 WIB',
        warna: 'from-emerald-500 to-teal-600',
    },
    {
        icon: '🕘',
        judul: 'Jam Pelayanan',
        isi: 'Senin–Jumat, 09.00–16.00',
        sub: 'Sabtu: by appointment',
        warna: 'from-amber-500 to-orange-600',
    },
]

const sosmed = [
    { nama: 'Instagram', handle: '@bemsaintek', icon: '📸', href: '#' },
    { nama: 'TikTok', handle: '@bemsaintek', icon: '🎵', href: '#' },
    { nama: 'YouTube', handle: 'BEM Saintek Official', icon: '▶️', href: '#' },
    { nama: 'LinkedIn', handle: 'BEM Saintek', icon: '💼', href: '#' },
]

const faq = [
    { q: 'Bagaimana cara menyampaikan aspirasi?', a: 'Isi form di halaman ini dengan kategori "Aspirasi / Advokasi", atau kirim DM ke Instagram @bemsaintek. Semua laporan masuk ke database advokasi dan dibahas tiap pekan.' },
    { q: 'Bagaimana cara mengajukan sponsorship / kerja sama?', a: 'Pilih kategori "Kerja Sama / Sponsorship" pada form, lampirkan proposal (link Google Drive) di kolom pesan. Tim Eksternal akan menghubungimu maks. 3 hari kerja.' },
    { q: 'Apakah boleh meminjam sekretariat / inventaris BEM?', a: 'Boleh untuk HIMA & UKM Saintek. Pilih kategori "Peminjaman", sebutkan tanggal & kebutuhan alat. Konfirmasi via WhatsApp sekretariat.' },
]

// Form (front-end only, siap disambung ke backend)
const form = ref({ nama: '', email: '', kategori: 'Aspirasi / Advokasi', pesan: '' })
const terkirim = ref(false)
const kategoriList = ['Aspirasi / Advokasi', 'Kerja Sama / Sponsorship', 'Peminjaman', 'Pers / Media', 'Lainnya']

function kirimPesan() {
    if (!form.value.nama || !form.value.email || !form.value.pesan) return
    terkirim.value = true
}

const inputClass = 'w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/25 outline-none transition focus:border-cyan-300/40 focus:ring-2 focus:ring-cyan-300/20'
</script>

<template>
    <Head title="Kontak" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans antialiased">
        <div class="noise"></div>
        <Navbar />

        <main>
            <PageHero
                label="HUBUNGI KAMI"
                description="Sekretariat BEM SAINTEK terbuka untuk aspirasi mahasiswa, ajakan kolaborasi, sponsorship, sampai peminjaman inventaris."
                :crumbs="[{ label: 'BERANDA', href: '/' }, { label: 'KONTAK' }]"
            >
                <template #title>
                    Ada Aspirasi? <span class="gradient-text">Sampaikan Saja.</span>
                </template>
            </PageHero>

            <!-- Info kontak -->
            <section class="relative py-10">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="info in infoKontak"
                            :key="info.judul"
                            class="rounded-2xl border border-white/10 bg-white/[0.025] p-6 transition-all hover:-translate-y-0.5 hover:border-cyan-300/20"
                        >
                            <div :class="`bg-gradient-to-br ${info.warna} mb-4 flex h-12 w-12 items-center justify-center rounded-xl text-2xl shadow-md`">{{ info.icon }}</div>
                            <h3 class="font-display font-bold text-white">{{ info.judul }}</h3>
                            <p class="mt-1 text-sm font-medium text-white/70">{{ info.isi }}</p>
                            <p class="mt-1 text-xs text-white/30">{{ info.sub }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Form + samping -->
            <section class="relative pb-16">
                <div class="mx-auto grid max-w-7xl items-start gap-6 px-5 lg:grid-cols-5 lg:px-8">
                    <!-- Form -->
                    <div class="glass rounded-2xl p-6 md:p-8 lg:col-span-3">
                        <div class="mb-1 font-mono text-[10px] tracking-[.3em] text-cyan-300">01 / KIRIM PESAN</div>
                        <h2 class="font-display text-xl font-bold text-white">Formulir Aspirasi</h2>
                        <p class="mb-6 mt-1 text-sm text-white/30">Tersimpan di front-end dulu — nanti tinggal sambungkan ke backend / Google Form / WhatsApp.</p>

                        <div v-if="terkirim" class="rounded-2xl border border-emerald-300/20 bg-emerald-300/5 p-6 text-center">
                            <div class="mb-2 text-4xl">✅</div>
                            <h3 class="font-display font-bold text-emerald-300">Pesan terkirim!</h3>
                            <p class="mb-4 mt-1 text-sm text-white/40">Terima kasih, {{ form.nama }}. Tim kami akan menghubungimu via {{ form.email }}.</p>
                            <button @click="terkirim = false; form = { nama: '', email: '', kategori: 'Aspirasi / Advokasi', pesan: '' }" class="cursor-pointer text-sm font-semibold text-emerald-300 hover:underline">Kirim pesan lain →</button>
                        </div>

                        <form v-else @submit.prevent="kirimPesan" class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-white/60">Nama Lengkap *</label>
                                    <input v-model="form.nama" type="text" required placeholder="Nama kamu" :class="inputClass" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-white/60">Email / No. WA *</label>
                                    <input v-model="form.email" type="text" required placeholder="email@kampus.ac.id" :class="inputClass" />
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-white/60">Kategori</label>
                                <select v-model="form.kategori" :class="[inputClass, 'cursor-pointer [&>option]:bg-[#0a0f1a]']">
                                    <option v-for="k in kategoriList" :key="k" :value="k">{{ k }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-white/60">Pesan *</label>
                                <textarea v-model="form.pesan" required rows="5" placeholder="Tulis aspirasi / kebutuhanmu sedetail mungkin…" :class="[inputClass, 'resize-none']"></textarea>
                            </div>
                            <button type="submit" class="w-full cursor-pointer rounded-full bg-white px-8 py-3 text-sm font-bold text-black transition hover:bg-cyan-300 sm:w-auto">Kirim Pesan →</button>
                        </form>
                    </div>

                    <!-- Samping: sosmed + peta -->
                    <div class="space-y-6 lg:col-span-2">
                        <div class="rounded-2xl border border-white/10 bg-white/[0.025] p-6">
                            <h3 class="font-display mb-4 font-bold text-white">Ikuti Media Sosial</h3>
                            <div class="space-y-3">
                                <a v-for="s in sosmed" :key="s.nama" :href="s.href" class="group flex items-center gap-3 rounded-xl border border-white/5 bg-white/[0.03] p-3 transition-colors hover:border-cyan-300/20">
                                    <span class="text-2xl">{{ s.icon }}</span>
                                    <span>
                                        <span class="block text-sm font-semibold text-white group-hover:text-cyan-300">{{ s.nama }}</span>
                                        <span class="block text-xs text-white/30">{{ s.handle }}</span>
                                    </span>
                                </a>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.025]">
                            <div class="flex h-48 flex-col items-center justify-center border-b border-white/5 bg-white/[0.02] p-6 text-center">
                                <div class="mb-2 text-4xl">🗺️</div>
                                <p class="text-sm font-semibold text-white">Peta Lokasi Sekretariat</p>
                                <p class="mt-1 font-mono text-[11px] text-white/30">GANTI BLOK INI DENGAN EMBED GOOGLE MAPS</p>
                            </div>
                            <div class="p-5">
                                <p class="font-mono text-[11px] text-white/30">🚶 2 MENIT DARI GERBANG UTAMA • 🅿️ PARKIR MOTOR BASEMENT SC</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section class="relative border-y border-white/5 bg-white/[0.015] py-16">
                <div class="mx-auto max-w-4xl px-5 lg:px-8">
                    <div class="mb-10 text-center">
                        <div class="mb-4 font-mono text-[10px] tracking-[.3em] text-cyan-300">02 / SERING DITANYAKAN</div>
                        <h2 class="font-display text-2xl font-bold text-white sm:text-3xl">Sebelum Menghubungi…</h2>
                    </div>
                    <div class="space-y-4">
                        <details v-for="f in faq" :key="f.q" class="group rounded-2xl border border-white/10 bg-white/[0.025] transition-colors open:border-cyan-300/20">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5 text-sm font-semibold text-white">
                                {{ f.q }}
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/[0.05] text-white/50 transition-transform group-open:rotate-45">＋</span>
                            </summary>
                            <p class="px-5 pb-5 text-sm leading-relaxed text-white/40">{{ f.a }}</p>
                        </details>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="relative py-16">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="flex flex-col justify-between gap-6 rounded-2xl border border-cyan-300/20 bg-gradient-to-r from-cyan-300/10 to-violet-500/10 p-8 md:flex-row md:items-center md:p-12">
                        <div>
                            <h3 class="font-display text-2xl font-bold text-white">Butuh respon cepat?</h3>
                            <p class="mt-1 text-white/40">Chat WhatsApp sekretariat di jam pelayanan — biasanya dibalas &lt; 1 jam.</p>
                        </div>
                        <a href="#" class="shrink-0 rounded-full bg-white px-6 py-3 text-sm font-bold text-black transition hover:bg-cyan-300">💬 Chat WhatsApp</a>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
