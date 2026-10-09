<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    gelombang: Object,
    isOpen: Boolean,
    user: Object,
    prodiList: Array,
    prefill: Object,
    pendaftaranSaya: Object,
})

const isMahasiswa = props.user?.role === 'mahasiswa'

const form = useForm({
    nama: props.prefill?.nama || '',
    nim: props.prefill?.nim || '',
    prodi: props.prefill?.prodi || props.prodiList?.[0] || '',
    semester: props.prefill?.semester || 1,
    tempat_lahir: props.prefill?.tempat_lahir || '',
    tanggal_lahir: props.prefill?.tanggal_lahir || '',
    asrama: '',
    email: props.prefill?.email || '',
    tujuan: '',
    riwayat_sekolah: [''],
    riwayat_madrasah: [''],
    riwayat_organisasi: [''],
})

const addItem = (key) => {
    if (form[key].length < 30) form[key].push('')
}

const removeItem = (key, index) => {
    form[key].splice(index, 1)
    if (form[key].length === 0) form[key].push('')
}

const clean = (arr) => (arr ?? []).map((v) => (v ?? '').trim()).filter((v) => v !== '')

const submit = () => {
    form.riwayat_sekolah = clean(form.riwayat_sekolah)
    form.riwayat_madrasah = clean(form.riwayat_madrasah)
    form.riwayat_organisasi = clean(form.riwayat_organisasi)
    form.post('/join')
}

const canFill = props.isOpen && isMahasiswa
</script>

<template>
    <Head title="Join With Us - BEM SAINTEK" />

    <div class="bem-saintek relative min-h-screen bg-[#03050a] font-sans text-white antialiased">
        <div class="noise"></div>
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-40 left-1/3 h-[380px] w-[680px] rounded-full bg-pink-400/[0.06] blur-[130px]"></div>
            <div class="absolute -bottom-48 -right-32 h-[380px] w-[380px] rounded-full bg-violet-500/[0.05] blur-[120px]"></div>
        </div>

        <div class="relative mx-auto max-w-3xl px-5 py-8 lg:px-6">
            <Link
                href="/"
                class="group inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 font-mono text-[11px] tracking-[.2em] text-white/60 transition hover:border-pink-300/40 hover:text-white"
            >
                <span class="transition-transform group-hover:-translate-x-1">←</span> KEMBALI
            </Link>

            <div class="mt-8 text-center">
                <p class="font-mono text-[10px] tracking-[.35em] text-pink-300">JOIN WITH US</p>
                <h1 class="font-display mt-3 text-4xl font-bold tracking-tight md:text-5xl">
                    Formulir Pendaftaran <span class="text-white/25">Pengurus.</span>
                </h1>
                <div class="mt-4 flex justify-center">
                    <span v-if="isOpen" class="dash-badge bg-emerald-300/10 text-emerald-300">
                        Dibuka: {{ gelombang.nama }} • {{ gelombang.tanggal_mulai }} — {{ gelombang.tanggal_selesai }}
                    </span>
                    <span v-else class="dash-badge bg-red-400/10 text-red-300">Pendaftaran sedang ditutup</span>
                </div>
                <p v-if="isOpen && gelombang.deskripsi" class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-white/40">
                    {{ gelombang.deskripsi }}
                </p>
            </div>

            <!-- Tutup -->
            <div v-if="!isOpen" class="dash-card mt-8 p-8 text-center">
                <p class="text-4xl">🔒</p>
                <h2 class="font-display mt-3 text-xl font-bold">Belum ada gelombang dibuka</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-white/40">Pendaftaran pengurus BEM saat ini sedang ditutup. Pantau terus info terbaru dari kami ya.</p>
                <Link href="/" class="dash-btn-ghost mt-5">Kembali ke Beranda</Link>
            </div>

            <!-- Tamu: wajib login -->
            <div v-else-if="!user" class="dash-card mt-8 p-8 text-center">
                <p class="text-4xl">👋</p>
                <h2 class="font-display mt-3 text-xl font-bold">Login dulu yuk</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-white/40">Formulir ini khusus mahasiswa yang sudah punya akun. Silakan login untuk mengisi pendaftaran.</p>
                <Link href="/login" class="dash-btn-primary mt-5">Login Sekarang</Link>
            </div>

            <!-- Pengurus nyasar -->
            <div v-else-if="!isMahasiswa" class="dash-card mt-8 p-8 text-center">
                <p class="text-4xl">🛡️</p>
                <h2 class="font-display mt-3 text-xl font-bold">Kamu login sebagai pengurus</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-white/40">Formulir ini khusus mahasiswa. Kelola pendaftar lewat halaman admin.</p>
                <Link href="/admin/pendaftaran" class="dash-btn-primary mt-5">Kelola Pendaftar</Link>
            </div>

            <!-- Sudah daftar di periode ini: tampilkan info, bukan form -->
            <div v-else-if="pendaftaranSaya" class="dash-card mt-8 p-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-emerald-300/30 bg-emerald-300/10 text-3xl">✓</div>
                <h2 class="font-display mt-4 text-xl font-bold">Kamu sudah mendaftar di periode ini</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-white/40">
                    {{ pendaftaranSaya.nama }} <span class="font-mono text-white/60">({{ pendaftaranSaya.nim }})</span>
                    • Status: <span class="font-semibold capitalize text-amber-300">{{ pendaftaranSaya.status }}</span>
                </p>
                <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                    <Link :href="`/join/sukses/${pendaftaranSaya.id}`" class="dash-btn-primary">Lihat Status Pendaftaran</Link>
                    <Link href="/pendaftaran" class="dash-btn-ghost">Ke Pendaftaranku</Link>
                </div>
            </div>

            <!-- Form mahasiswa -->
            <form v-else @submit.prevent="submit" class="mt-8">
                <div class="dash-card space-y-6 p-6">
                    <h3 class="font-display text-lg font-bold text-white">Data Diri</h3>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="dash-label">Nama Lengkap *</label>
                            <input v-model="form.nama" type="text" class="dash-input" placeholder="Nama lengkap" required />
                            <p v-if="form.errors.nama" class="dash-error">{{ form.errors.nama }}</p>
                        </div>
                        <div>
                            <label class="dash-label">NPM / NIM *</label>
                            <input v-model="form.nim" type="text" class="dash-input font-mono" placeholder="NPM / NIM" required />
                            <p v-if="form.errors.nim" class="dash-error">{{ form.errors.nim }}</p>
                        </div>
                        <div>
                            <label class="dash-label">Program Studi *</label>
                            <select v-model="form.prodi" class="dash-input cursor-pointer" required>
                                <option v-for="p in prodiList" :key="p" :value="p">{{ p }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="dash-label">Semester * (1–14)</label>
                            <input v-model="form.semester" type="number" min="1" max="14" class="dash-input" required />
                            <p v-if="form.errors.semester" class="dash-error">{{ form.errors.semester }}</p>
                        </div>
                        <div>
                            <label class="dash-label">Tempat Lahir</label>
                            <input v-model="form.tempat_lahir" type="text" class="dash-input" placeholder="Kota lahir" />
                        </div>
                        <div>
                            <label class="dash-label">Tanggal Lahir</label>
                            <input v-model="form.tanggal_lahir" type="date" class="dash-input" />
                        </div>
                        <div>
                            <label class="dash-label">Asrama</label>
                            <input v-model="form.asrama" type="text" class="dash-input" placeholder="Nama / alamat asrama" />
                        </div>
                        <div>
                            <label class="dash-label">Email</label>
                            <input v-model="form.email" type="email" class="dash-input" placeholder="email@contoh.com" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="dash-label">Tujuan Gabung BEM</label>
                            <textarea v-model="form.tujuan" rows="3" class="dash-input" placeholder="Ceritakan motivasimu..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="dash-card mt-6 space-y-6 p-6">
                    <h3 class="font-display text-lg font-bold text-white">Riwayat</h3>
                    <div v-for="group in [
                        { key: 'riwayat_sekolah', title: 'Riwayat Pendidikan Sekolah', ph: 'cth: SDN 1 Demak (2012–2018)' },
                        { key: 'riwayat_madrasah', title: 'Riwayat Pendidikan Madrasah', ph: 'cth: MTs N 1 Demak (2018–2021)' },
                        { key: 'riwayat_organisasi', title: 'Riwayat Organisasi', ph: 'cth: Ketua OSIS SMA (2022–2023)' },
                    ]" :key="group.key">
                        <label class="dash-label">{{ group.title }} (boleh lebih dari satu)</label>
                        <div class="space-y-3">
                            <div v-for="(point, idx) in form[group.key]" :key="idx" class="flex items-start gap-2">
                                <span class="mt-2.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-pink-300/10 font-mono text-xs font-bold text-pink-300">{{ idx + 1 }}</span>
                                <input v-model="form[group.key][idx]" type="text" maxlength="255" class="dash-input" :placeholder="group.ph" />
                                <button v-if="form[group.key].length > 1" type="button" @click="removeItem(group.key, idx)" class="mt-1 shrink-0 rounded-xl border border-red-400/20 p-2 text-red-300 transition hover:bg-red-400/10" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                            <button v-if="form[group.key].length < 30" type="button" @click="addItem(group.key)" class="dash-btn-ghost !py-2 text-sm">+ Tambah</button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-4">
                    <Link href="/" class="dash-btn-ghost">Kembali</Link>
                    <button type="submit" :disabled="form.processing" class="dash-btn-primary flex-1 justify-center sm:flex-none sm:px-10">
                        <span v-if="form.processing">Mengirim...</span>
                        <span v-else>Kirim Pendaftaran →</span>
                    </button>
                </div>
                <p v-if="form.errors.nama || form.errors.nim" class="mt-3 text-center text-sm text-rose-300">Periksa lagi isian bertanda merah ya.</p>
            </form>

            <p class="mt-10 text-center font-mono text-[10px] tracking-[.4em] text-white/20">BEM SAINTEK • JOIN / CONNECT / CREATE / IMPACT</p>
        </div>
    </div>
</template>
