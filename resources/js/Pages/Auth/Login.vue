<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    nim: '',
    password: '',
})

const showPassword = ref(false)

const submit = () => {
    form.post('/login')
}

const inputClass = 'w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 sm:py-3 font-mono text-sm text-white placeholder-white/25 outline-none transition focus:border-cyan-300/40 focus:ring-2 focus:ring-cyan-300/20'
</script>

<template>
    <Head title="Login Admin - BEM SAINTEK" />

    <div class="bem-saintek relative flex min-h-dvh items-center justify-center overflow-hidden bg-[#03050A] px-4 py-6 font-sans antialiased sm:px-5 sm:py-12">
        <div class="noise"></div>

        <!-- background ala beranda -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="grid-bg absolute inset-0 opacity-[0.11]"></div>
            <div class="absolute -top-40 left-1/2 h-[420px] w-[720px] -translate-x-1/2 rounded-full bg-cyan-400/[0.07] blur-[130px]"></div>
            <div class="absolute -bottom-48 -left-32 h-[380px] w-[380px] rounded-full bg-violet-500/[0.06] blur-[120px]"></div>
            <div class="absolute -bottom-48 -right-32 h-[380px] w-[380px] rounded-full bg-cyan-400/[0.05] blur-[120px]"></div>
        </div>

        <div class="relative m-auto w-full max-w-md overflow-hidden rounded-3xl border border-white/10 bg-white/[0.025] backdrop-blur-xl sm:rounded-[2rem] lg:max-w-4xl">
            <div class="grid lg:grid-cols-2">
                <!-- KIRI: branding (dibuat ringkas di mobile agar form langsung terlihat) -->
                <div class="relative flex flex-col justify-between overflow-hidden border-b border-white/5 p-5 sm:p-8 lg:border-b-0 lg:border-r lg:p-10">
                    <div class="pointer-events-none absolute -top-24 -left-24 h-64 w-64 rounded-full bg-cyan-300/[0.07] blur-[80px]"></div>

                    <div>
                        <Link href="/" class="flex w-fit items-center gap-3">
                            <div class="relative flex h-9 w-9 items-center justify-center overflow-hidden rounded-xl border border-cyan-300/20 bg-cyan-300/5 sm:h-11 sm:w-11">
                                <div class="absolute inset-0 bg-cyan-300/10 blur-xl"></div>
                                <img :src="'/images/saintek.png'" alt="BEM Saintek" class="relative h-9 w-9 object-contain sm:h-11 sm:w-11" />
                            </div>
                            <div>
                                <div class="font-display font-bold tracking-tight text-white">
                                    BEM <span class="text-cyan-300">SAINTEK</span>
                                </div>
                                <div class="font-mono text-[8px] uppercase tracking-[.3em] text-white/30">
                                    Beyond Limits
                                </div>
                            </div>
                        </Link>

                        <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/5 px-3 py-1 font-mono text-[9px] tracking-[.25em] text-cyan-300 sm:px-4 sm:py-1.5 sm:text-[10px] lg:mt-8">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-cyan-300"></span>
                            SECURE ACCESS
                        </div>

                        <h1 class="font-display mt-3 text-2xl font-bold leading-[1.05] tracking-tight text-white sm:mt-5 sm:text-3xl lg:text-4xl">
                            SAINTEK<span class="text-white/20">.OS</span>
                            <span class="gradient-text"> Admin Terminal</span>
                        </h1>
                        <p class="mt-3 hidden max-w-sm text-sm leading-relaxed text-white/40 sm:mt-4 sm:block">
                            Masuk dengan NIM dan password terdaftar untuk mengelola departemen, program kerja, dan konten BEM SAINTEK.
                        </p>

                        <div class="mt-8 hidden space-y-2.5 font-mono text-[11px] text-white/30 lg:block">
                            <div class="flex items-center gap-3">
                                <span class="text-cyan-300">▸</span> KELOLA DEPARTEMEN & PROKER
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-cyan-300">▸</span> PUBLIKASI KONTEN PROFIL
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-cyan-300">▸</span> MONITORING ASPIRASI WARGA
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 hidden items-center justify-between border-t border-white/5 pt-5 font-mono text-[9px] tracking-[.25em] text-white/20 lg:flex">
                        <span>SAINTEK.OS v2.6</span>
                        <span class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300 shadow-[0_0_10px_rgba(110,231,183,.7)]"></span>
                            SYSTEM ONLINE
                        </span>
                    </div>
                </div>

                <!-- KANAN: form -->
                <div class="flex items-center p-5 sm:p-8 lg:p-10">
                    <div class="w-full">
                        <div class="mb-1 font-mono text-[10px] tracking-[.3em] text-cyan-300">01 / AUTHENTICATION</div>
                        <h2 class="font-display text-xl font-bold text-white sm:text-2xl">Masuk ke Akun Anda</h2>
                        <p class="mb-5 mt-1 text-[13px] text-white/30 sm:mb-7 sm:text-sm">Gunakan kredensial yang valid untuk melanjutkan.</p>

                        <form @submit.prevent="submit" class="space-y-3 sm:space-y-4">
                            <div>
                                <label for="nim" class="mb-1.5 block font-mono text-[10px] tracking-[.25em] text-white/40">NIM</label>
                                <input
                                    id="nim"
                                    v-model="form.nim"
                                    type="text"
                                    required
                                    autocomplete="username"
                                    placeholder="Masukkan NIM Anda"
                                    :class="inputClass"
                                />
                                <p v-if="form.errors.nim" class="mt-1.5 font-mono text-[11px] text-rose-300">{{ form.errors.nim }}</p>
                            </div>

                            <div>
                                <label for="password" class="mb-1.5 block font-mono text-[10px] tracking-[.25em] text-white/40">PASSWORD</label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        autocomplete="current-password"
                                        placeholder="••••••••"
                                        :class="[inputClass, 'pr-12']"
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer rounded-lg border border-white/10 bg-white/[0.05] px-2 py-1 font-mono text-[10px] text-white/40 transition hover:border-cyan-300/30 hover:text-cyan-300"
                                    >
                                        {{ showPassword ? 'HIDE' : 'SHOW' }}
                                    </button>
                                </div>
                                <p v-if="form.errors.password" class="mt-1.5 font-mono text-[11px] text-rose-300">{{ form.errors.password }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full cursor-pointer rounded-full bg-white py-3 text-sm font-bold text-black transition hover:bg-cyan-300 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span v-if="form.processing">Memproses…</span>
                                <span v-else>Masuk Sistem →</span>
                            </button>
                        </form>

                        <div class="mt-5 border-t border-white/5 pt-4 text-center sm:mt-6 sm:pt-5">
                            <Link href="/" class="font-mono text-[11px] tracking-widest text-white/30 transition hover:text-cyan-300">← KEMBALI KE HALAMAN UTAMA</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-4 left-1/2 hidden -translate-x-1/2 whitespace-nowrap font-mono text-[9px] tracking-[.5em] text-white/[0.15] sm:block">
            BEYOND LIMITS // RESTRICTED AREA
        </div>
    </div>
</template>
