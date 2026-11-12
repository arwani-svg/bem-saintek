<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Navbar from '@/components/sections/Navbar.vue'
import Footer from '@/components/sections/Footer.vue'
import PageHero from '@/components/ui/PageHero.vue'
import BackToTop from '@/components/ui/BackToTop.vue'

const infoKontak = [
    {
        nomor: '01',
        judul: 'Sekretariat',
        isi: 'Gedung Student Center, Lantai 1',
        sub: 'Fakultas Sains dan Teknologi',
        icon: '📍',
    },
    {
        nomor: '02',
        judul: 'Email',
        isi: 'bemsaintek@kampus.ac.id',
        sub: 'Untuk keperluan dan pertanyaan resmi',
        icon: '✉',
        href: 'mailto:bemsaintek@kampus.ac.id',
    },
    {
        nomor: '03',
        judul: 'WhatsApp',
        isi: '+62 812-3456-7890',
        sub: 'Senin–Jumat, 09.00–16.00 WIB',
        icon: '↗',
        href: 'https://wa.me/6281234567890',
    },
]

const sosmed = [
    {
        nama: 'Instagram',
        username: '@bemsaintek',
        icon: '◎',
        href: 'https://instagram.com/bemsaintek',
    },
    {
        nama: 'TikTok',
        username: '@bemsaintek',
        icon: '♪',
        href: 'https://tiktok.com/@bemsaintek',
    },
    {
        nama: 'YouTube',
        username: 'BEM Saintek',
        icon: '▷',
        href: '#',
    },
]

const faq = [
    {
        q: 'Bagaimana cara menyampaikan aspirasi mahasiswa?',
        a: 'Kamu dapat mengirimkan aspirasi melalui formulir di halaman ini atau menghubungi akun media sosial resmi BEM Saintek. Jelaskan permasalahan dan informasi pendukung agar pengurus dapat memahami kebutuhanmu.',
    },
    {
        q: 'Bagaimana cara mengajukan kerja sama?',
        a: 'Pilih kategori Kerja Sama / Sponsorship pada formulir, lalu jelaskan bentuk kolaborasi yang ingin diajukan. Kamu juga dapat menyertakan tautan proposal untuk memudahkan proses tindak lanjut.',
    },
    {
        q: 'Apakah mahasiswa dapat meminjam fasilitas sekretariat?',
        a: 'Silakan menghubungi pengurus untuk memastikan ketersediaan tempat atau inventaris. Sertakan tanggal penggunaan, kebutuhan, serta penanggung jawab kegiatan.',
    },
    {
        q: 'Berapa lama pesan akan mendapatkan balasan?',
        a: 'Pesan akan ditanggapi sesuai antrean dan jenis keperluannya. Pertanyaan yang membutuhkan koordinasi dengan bidang terkait mungkin memerlukan waktu lebih lama.',
    },
]

const kategoriList = [
    'Aspirasi Mahasiswa',
    'Kerja Sama / Sponsorship',
    'Peminjaman Fasilitas',
    'Pers dan Media',
    'Pertanyaan Umum',
]

const form = ref({
    nama: '',
    email: '',
    kategori: 'Aspirasi Mahasiswa',
    pesan: '',
})

function kirimPesan() {
    if (
        !form.value.nama.trim() ||
        !form.value.email.trim() ||
        !form.value.pesan.trim()
    ) return

    const nomorWA = '6281234567890'

    const pesan = [
        'Halo BEM Saintek, saya ingin menghubungi pengurus.',
        '',
        `Nama: ${form.value.nama}`,
        `Kontak: ${form.value.email}`,
        `Kategori: ${form.value.kategori}`,
        '',
        'Pesan:',
        form.value.pesan,
    ].join('\n')

    const url = `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesan)}`

    window.open(url, '_blank', 'noopener,noreferrer')
}

const inputClass =
    'mt-2 w-full rounded-xl border border-white/10 bg-[#080C14] px-4 py-3 text-sm text-white placeholder:text-white/25 outline-none transition focus:border-cyan-300/50 focus:ring-2 focus:ring-cyan-300/10'
</script>

<template>
    <Head title="Kontak — BEM Saintek" />

    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans text-white antialiased">
        <div class="noise"></div>

        <Navbar />

        <main>
            <!-- Hero -->
            <PageHero
                label="KONTAK"
                description="Punya pertanyaan, ingin menyampaikan aspirasi, atau tertarik berkolaborasi? Kami siap mendengarkan."
                :crumbs="[
                    { label: 'BERANDA', href: '/' },
                    { label: 'KONTAK' },
                ]"
            >
                <template #title>
                    Mari Terhubung.
                    <span class="gradient-text">Kami Siap Mendengar.</span>
                </template>
            </PageHero>

            <!-- Informasi kontak -->
            <section class="relative py-12 sm:py-16">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="mb-8 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                        <div>
                            <p class="font-mono text-[10px] tracking-[.25em] text-cyan-300">
                                INFORMASI KONTAK
                            </p>

                            <h2 class="mt-3 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                                Temukan cara menghubungi kami.
                            </h2>
                        </div>

                        <p class="max-w-sm text-sm leading-6 text-white/40">
                            Pilih saluran komunikasi yang paling sesuai
                            dengan kebutuhanmu.
                        </p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <article
                            v-for="item in infoKontak"
                            :key="item.nomor"
                            class="group rounded-2xl border border-white/[0.08] bg-white/[0.025] p-6 transition duration-300 hover:border-cyan-300/25 hover:bg-white/[0.04]"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs text-white/25">
                                    {{ item.nomor }}
                                </span>

                                <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/[0.08] bg-white/[0.04] text-lg text-cyan-300 transition group-hover:border-cyan-300/20">
                                    {{ item.icon }}
                                </span>
                            </div>

                            <h3 class="mt-7 text-base font-semibold text-white">
                                {{ item.judul }}
                            </h3>

                            <p class="mt-2 break-words text-sm font-medium leading-6 text-white/75">
                                {{ item.isi }}
                            </p>

                            <p class="mt-1 text-xs leading-5 text-white/35">
                                {{ item.sub }}
                            </p>

                            <a
                                v-if="item.href"
                                :href="item.href"
                                class="mt-5 inline-flex items-center gap-2 text-xs font-semibold text-cyan-300 transition hover:text-cyan-200"
                            >
                                Hubungi kami
                                <span aria-hidden="true">↗</span>
                            </a>
                        </article>
                    </div>
                </div>
            </section>

            <!-- Form dan media sosial -->
            <section class="relative py-12 sm:py-16">
                <div class="mx-auto grid max-w-7xl items-start gap-6 px-5 lg:grid-cols-5 lg:px-8">
                    <!-- Form -->
                    <div class="rounded-2xl border border-white/[0.08] bg-white/[0.025] p-6 sm:p-8 lg:col-span-3">
                        <div class="font-mono text-[10px] tracking-[.25em] text-cyan-300">
                            01 / KIRIM PESAN
                        </div>

                        <h2 class="mt-3 text-xl font-bold text-white sm:text-2xl">
                            Ada yang ingin disampaikan?
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-white/40">
                            Ceritakan kebutuhanmu melalui formulir berikut.
                            Pesan akan disiapkan untuk dikirim melalui WhatsApp.
                        </p>

                        <form
                            class="mt-8 space-y-5"
                            @submit.prevent="kirimPesan"
                        >
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label
                                        for="nama"
                                        class="text-sm font-medium text-white/70"
                                    >
                                        Nama lengkap
                                    </label>

                                    <input
                                        id="nama"
                                        v-model="form.nama"
                                        type="text"
                                        name="nama"
                                        autocomplete="name"
                                        maxlength="100"
                                        required
                                        placeholder="Nama kamu"
                                        :class="inputClass"
                                    />
                                </div>

                                <div>
                                    <label
                                        for="email"
                                        class="text-sm font-medium text-white/70"
                                    >
                                        Email / No. WhatsApp
                                    </label>

                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="text"
                                        name="email"
                                        maxlength="150"
                                        required
                                        placeholder="Kontak yang bisa dihubungi"
                                        :class="inputClass"
                                    />
                                </div>
                            </div>

                            <div>
                                <label
                                    for="kategori"
                                    class="text-sm font-medium text-white/70"
                                >
                                    Kategori pesan
                                </label>

                                <select
                                    id="kategori"
                                    v-model="form.kategori"
                                    name="kategori"
                                    :class="[inputClass, 'cursor-pointer [&>option]:bg-[#080C14]']"
                                >
                                    <option
                                        v-for="kategori in kategoriList"
                                        :key="kategori"
                                        :value="kategori"
                                    >
                                        {{ kategori }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label
                                    for="pesan"
                                    class="text-sm font-medium text-white/70"
                                >
                                    Pesan
                                </label>

                                <textarea
                                    id="pesan"
                                    v-model="form.pesan"
                                    name="pesan"
                                    rows="5"
                                    maxlength="5000"
                                    required
                                    placeholder="Tuliskan pesan atau aspirasi kamu di sini..."
                                    :class="[inputClass, 'resize-y']"
                                ></textarea>

                                <p class="mt-2 text-xs text-white/25">
                                    Maksimal 5.000 karakter.
                                </p>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-cyan-300 px-6 py-3.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300/50 focus:ring-offset-2 focus:ring-offset-[#03050A] sm:w-auto"
                            >
                                Kirim pesan
                                <span aria-hidden="true">↗</span>
                            </button>

                            <p class="text-xs leading-5 text-white/25">
                                WhatsApp akan terbuka dengan isi pesan
                                yang telah disiapkan untuk kamu periksa.
                            </p>
                        </form>
                    </div>

                    <!-- Sisi kanan -->
                    <aside class="space-y-5 lg:col-span-2">
                        <div class="rounded-2xl border border-white/[0.08] bg-white/[0.025] p-6">
                            <div class="font-mono text-[10px] tracking-[.25em] text-cyan-300">
                                02 / MEDIA SOSIAL
                            </div>

                            <h3 class="mt-3 text-lg font-bold text-white">
                                Ikuti aktivitas kami.
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-white/40">
                                Temukan informasi kegiatan, kabar terbaru,
                                dan berbagai program BEM Saintek.
                            </p>

                            <div class="mt-6 space-y-3">
                                <a
                                    v-for="s in sosmed"
                                    :key="s.nama"
                                    :href="s.href"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group flex items-center gap-4 rounded-xl border border-white/[0.06] bg-white/[0.02] p-4 transition hover:border-cyan-300/20 hover:bg-white/[0.04]"
                                >
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/[0.05] text-xl text-cyan-300">
                                        {{ s.icon }}
                                    </span>

                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm font-semibold text-white">
                                            {{ s.nama }}
                                        </span>

                                        <span class="mt-1 block truncate text-xs text-white/35">
                                            {{ s.username }}
                                        </span>
                                    </span>

                                    <span class="text-sm text-white/30 transition group-hover:text-cyan-300">
                                        ↗
                                    </span>
                                </a>
                            </div>
                        </div>

                        <!-- Informasi tambahan -->
                        <div class="rounded-2xl border border-cyan-300/15 bg-cyan-300/[0.04] p-6">
                            <p class="text-sm font-semibold text-cyan-200">
                                Punya ide atau inisiatif?
                            </p>

                            <p class="mt-2 text-sm leading-6 text-white/45">
                                Kami terbuka untuk diskusi, usulan kegiatan,
                                dan kolaborasi yang membawa manfaat bagi
                                mahasiswa Saintek.
                            </p>
                        </div>
                    </aside>
                </div>
            </section>

            <!-- FAQ -->
            <section class="relative border-y border-white/[0.06] bg-white/[0.015] py-14 sm:py-20">
                <div class="mx-auto max-w-4xl px-5 lg:px-8">
                    <div class="mb-9">
                        <p class="font-mono text-[10px] tracking-[.25em] text-cyan-300">
                            03 / PERTANYAAN UMUM
                        </p>

                        <h2 class="mt-3 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                            Sebelum menghubungi kami.
                        </h2>

                        <p class="mt-3 max-w-xl text-sm leading-6 text-white/40">
                            Beberapa informasi yang mungkin bisa membantu
                            menjawab pertanyaanmu lebih cepat.
                        </p>
                    </div>

                    <div class="divide-y divide-white/[0.07] border-y border-white/[0.07]">
                        <details
                            v-for="item in faq"
                            :key="item.q"
                            class="group"
                        >
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-5 py-5 text-sm font-medium leading-6 text-white/85 marker:hidden sm:text-base">
                                {{ item.q }}

                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 text-white/45 transition group-open:rotate-45 group-open:border-cyan-300/25 group-open:text-cyan-300">
                                    +
                                </span>
                            </summary>

                            <p class="max-w-3xl pb-6 text-sm leading-7 text-white/45">
                                {{ item.a }}
                            </p>
                        </details>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="relative py-14 sm:py-20">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="flex flex-col gap-6 rounded-2xl border border-white/[0.08] bg-white/[0.025] p-7 sm:p-10 md:flex-row md:items-center md:justify-between">
                        <div class="max-w-2xl">
                            <p class="font-mono text-[10px] tracking-[.25em] text-cyan-300">
                                TERBUKA UNTUK KOLABORASI
                            </p>

                            <h2 class="mt-3 text-2xl font-bold text-white sm:text-3xl">
                                Mari mulai percakapan.
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-white/40">
                                Sampaikan ide, pertanyaan, atau kebutuhanmu.
                                Kami siap berdiskusi bersama.
                            </p>
                        </div>

                        <a
                            href="https://wa.me/6281234567890"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-200"
                        >
                            Hubungi pengurus
                            <span aria-hidden="true">↗</span>
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
        <BackToTop />
    </div>
</template>
