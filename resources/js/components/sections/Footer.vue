<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const departments = computed(() => page.props.navDepartemens ?? [])

const quickLinks = [
    { label: 'Beranda', href: '/' },
    { label: 'Tentang', href: '/#tentang' },
    { label: 'Program Kerja', href: '/program-kerja' },
    { label: 'Kontak', href: '/kontak' },
]
</script>

<template>
    <footer class="border-t border-white/5 pt-20">
        <div class="max-w-7xl mx-auto px-5 lg:px-8">
            <div class="footer-big font-display font-bold text-white/[.035] select-none">SAINTEK</div>

            <div class="grid md:grid-cols-3 gap-10 mt-8">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-cyan-300 text-black flex items-center justify-center font-display font-bold shrink-0">S</div>
                    <div>
                        <div class="font-display font-bold text-sm text-white">BEM SAINTEK</div>
                        <div class="font-mono text-[8px] text-white/20 tracking-[.2em] mt-1">BEYOND LIMITS</div>
                        <p class="text-xs text-white/30 leading-relaxed mt-4 max-w-xs">
                            Badan Eksekutif Mahasiswa Fakultas Sains dan Teknologi. Wadah aspirasi dan perjuangan mahasiswa.
                        </p>
                    </div>
                </div>

                <div>
                    <div class="font-mono text-[9px] text-white/25 tracking-[.25em] mb-4">MENU</div>
                    <div class="flex flex-col gap-2.5">
                        <Link
                            v-for="l in quickLinks"
                            :key="l.href"
                            :href="l.href"
                            class="text-xs text-white/40 hover:text-cyan-300 transition w-fit"
                        >
                            {{ l.label }}
                        </Link>
                    </div>
                </div>

                <div>
                    <div class="font-mono text-[9px] text-white/25 tracking-[.25em] mb-4">DEPARTEMEN</div>
                    <div class="flex flex-col gap-2.5">
                        <Link
                            v-for="d in departments"
                            :key="d.id"
                            :href="`/departemen/${d.id}`"
                            class="text-xs text-white/40 hover:text-cyan-300 transition w-fit"
                        >
                            {{ d.icon }} {{ d.nama }}
                        </Link>
                        <span v-if="!departments.length" class="text-xs text-white/25">KADERISASI • NALAR BAKAT • INFOKOM • EKONOMI • RELASI</span>
                    </div>
                    <div class="flex items-center gap-5 text-xs text-white/25 mt-6">
                        <a href="#" class="hover:text-cyan-300 transition">Instagram</a>
                        <a href="#" class="hover:text-cyan-300 transition">LinkedIn</a>
                        <a href="#" class="hover:text-cyan-300 transition">YouTube</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/5 py-8 mt-12 flex flex-col md:flex-row justify-between gap-3">
                <div class="font-mono text-[9px] text-white/15">© 2026 BEM SAINTEK</div>
                <div class="font-mono text-[9px] text-white/15">BEYOND LIMITS // SAINTEK.OS v2.6</div>
            </div>
        </div>
    </footer>
</template>
