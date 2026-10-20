<script setup>
import { computed, onMounted, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Sidebar from './Sidebar.vue'
import Breadcrumb from './Breadcrumb.vue'
import Toast from './Toast.vue'

const props = defineProps({
    title: String,
    subtitle: String,
    eyebrow: {
        type: String,
        default: 'SAINTEK.OS // ADMIN',
    },
    activeMenu: {
        type: String,
        default: 'dashboard',
    },
    user: Object,
    breadcrumbs: {
        type: Array,
        default: null,
    },
    backHref: {
        type: String,
        default: null,
    },
    showBack: {
        type: Boolean,
        default: null, // null = otomatis (sembunyi hanya di dashboard)
    },
})

const sidebarOpen = ref(false)
const collapsed = ref(false)

onMounted(() => {
    try {
        collapsed.value = localStorage.getItem('saintek-sidebar-collapsed') === '1'
    } catch (e) {
        collapsed.value = false
    }
})

const toggleCollapse = () => {
    collapsed.value = !collapsed.value
    try {
        localStorage.setItem('saintek-sidebar-collapsed', collapsed.value ? '1' : '0')
    } catch (e) {}
}

const MENU_META = {
    dashboard: { label: 'Dashboard', href: '/dashboard' },
    profil: { label: 'Profil Saya', href: '/profil' },
    users: { label: 'Manajemen User', href: '/users' },
    departemen: { label: 'Departemen', href: '/departemen' },
    proker: { label: 'Program Kerja', href: '/proker' },
    informasi: { label: 'Informasi', href: '/informasi' },
    pendaftaran: { label: 'Daftar Pengurus', href: '/pendaftaran' },
    'pendaftaran-admin': { label: 'Pendaftar BEM', href: '/admin/pendaftaran' },
    roles: { label: 'Daftar Role', href: '/roles' },
    'roles-create': { label: 'Tambah Role', href: '/roles/create' },
    permissions: { label: 'Permissions', href: '/permissions' },
    'permissions-create': { label: 'Tambah Permission', href: '/permissions/create' },
    keuangan: { label: 'Keuangan', href: '/keuangan' },
    'keuangan-rekap': { label: 'Rekap & Cetak', href: '/keuangan/rekap' },
    iuran: { label: 'Manajemen Iuran', href: '/iuran' },
    'iuran-create': { label: 'Buat Iuran', href: '/iuran/create' },
    'iuran-show': { label: 'Detail Iuran', href: '/iuran' },
    kegiatan: { label: 'Daftar Kegiatan', href: '/kegiatan' },
    'kegiatan-create': { label: 'Buat Kegiatan', href: '/kegiatan/create' },
    'kegiatan-show': { label: 'Detail Kegiatan', href: '/kegiatan' },
    'kegiatan-absen': { label: 'Absen Cepat', href: '/kegiatan' },
    'absensi-rekap': { label: 'Rekap Skor', href: '/absensi/rekap' },
    'activity-log': { label: 'Activity Log', href: '/activity-log' },
}

const autoBreadcrumbs = computed(() => {
    if (props.breadcrumbs?.length) return props.breadcrumbs
    const meta = MENU_META[props.activeMenu] || { label: props.title || 'Halaman', href: null }
    if (props.activeMenu === 'dashboard') {
        return [{ label: props.title || 'Dashboard' }]
    }
    const items = [{ label: 'Dashboard', href: '/dashboard' }]
    if (props.title && props.title !== meta.label) {
        items.push({ label: meta.label, href: meta.href })
        items.push({ label: props.title })
    } else {
        items.push({ label: meta.label })
    }
    return items
})

const resolvedBackHref = computed(() => {
    if (props.backHref) return props.backHref
    if (props.activeMenu === 'dashboard') return null
    const meta = MENU_META[props.activeMenu]
    // Kalau lagi di halaman form (judul beda dari label menu), kembali ke list menu tsb.
    // Kalau di halaman list, kembali ke dashboard.
    if (meta && props.title && props.title !== meta.label) return meta.href
    if (meta) return '/dashboard'
    return null
})

const shouldShowBack = computed(() => {
    if (props.showBack !== null) return props.showBack
    return props.activeMenu !== 'dashboard' && !!resolvedBackHref.value
})

const goBack = () => {
    if (resolvedBackHref.value) {
        router.visit(resolvedBackHref.value)
    } else if (window.history.length > 1) {
        window.history.back()
    }
}
</script>

<template>
    <div class="bem-saintek min-h-screen bg-[#03050A] font-sans text-white antialiased">
        <div class="noise"></div>

        <!-- glow latar -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-40 left-1/3 h-[380px] w-[680px] rounded-full bg-cyan-400/[0.05] blur-[130px]"></div>
            <div class="absolute -bottom-48 -right-32 h-[380px] w-[380px] rounded-full bg-violet-500/[0.04] blur-[120px]"></div>
        </div>

        <Sidebar
            :activeMenu="activeMenu"
            :user="user"
            :open="sidebarOpen"
            :collapsed="collapsed"
            @close="sidebarOpen = false"
        />

        <div
            class="relative transition-[padding] duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
            :class="collapsed ? 'lg:pl-20' : 'lg:pl-72'"
        >
            <header class="sticky top-0 z-30 border-b border-white/10 bg-[#03050a]/75 backdrop-blur-xl">
                <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <!-- Toggle mobile: buka drawer -->
                        <button
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 text-white/70 transition hover:border-cyan-300/30 hover:text-white lg:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Buka menu"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Toggle desktop: collapse / expand dengan animasi -->
                        <button
                            class="group hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 text-white/60 transition-all duration-300 hover:border-cyan-300/40 hover:bg-cyan-300/5 hover:text-cyan-300 active:scale-90 lg:flex"
                            @click="toggleCollapse"
                            :aria-label="collapsed ? 'Buka sidebar' : 'Tutup sidebar'"
                            :title="collapsed ? 'Buka sidebar' : 'Tutup sidebar'"
                        >
                            <svg
                                class="h-5 w-5 transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                                :class="collapsed ? 'rotate-180' : ''"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </button>

                        <div class="min-w-0">
                            <slot name="header">
                                <p class="font-mono text-[10px] tracking-[.3em] text-cyan-300">{{ eyebrow }}</p>
                                <h1 class="font-display truncate text-xl font-bold tracking-tight text-white">{{ title }}</h1>
                                <p v-if="subtitle" class="truncate text-sm text-white/40">{{ subtitle }}</p>
                            </slot>
                        </div>
                    </div>
                    <Link
                        v-if="user"
                        href="/profil"
                        class="group flex shrink-0 items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.03] py-1.5 pl-1.5 pr-3 transition hover:border-cyan-300/30 hover:bg-cyan-300/5"
                        title="Lihat profil saya"
                    >
                        <img
                            v-if="user.foto_url"
                            :src="user.foto_url"
                            :alt="user.name"
                            class="h-9 w-9 rounded-xl border border-white/20 object-cover"
                        />
                        <span
                            v-else
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400 to-violet-500 text-sm font-bold text-black"
                        >
                            {{ (user.name || '?').charAt(0).toUpperCase() }}
                        </span>
                        <span class="hidden text-left sm:block">
                            <span class="block max-w-[160px] truncate text-sm font-semibold leading-tight text-white group-hover:text-cyan-200">{{ user.name }}</span>
                            <span class="block font-mono text-[10px] uppercase leading-tight tracking-widest text-white/30">{{ user.role }}</span>
                        </span>
                        <svg class="hidden h-4 w-4 text-white/30 transition group-hover:translate-x-0.5 group-hover:text-cyan-300 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>
            </header>

            <div class="px-5 py-6 sm:px-8 sm:py-8">
                <!-- Breadcrumb dinamis + tombol kembali -->
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <Breadcrumb :items="autoBreadcrumbs" />
                    <button
                        v-if="shouldShowBack"
                        @click="goBack"
                        class="group inline-flex shrink-0 items-center gap-2 rounded-xl border border-white/10 bg-white/[0.03] px-3.5 py-2 font-mono text-[11px] tracking-widest text-white/50 transition-all hover:border-cyan-300/30 hover:text-cyan-300 active:scale-95"
                    >
                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        KEMBALI
                    </button>
                    <Link
                        v-else-if="false"
                        :href="resolvedBackHref"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/10 px-3.5 py-2 font-mono text-[11px] text-white/50"
                    >
                        KEMBALI
                    </Link>
                </div>

                <slot />
            </div>
        </div>

        <!-- Toast global pojok kanan atas (tanpa tombol aksi, auto-hilang) -->
        <Toast />
    </div>
</template>
