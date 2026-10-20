<script setup>
import { computed, ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    activeMenu: {
        type: String,
        default: 'dashboard',
    },
    user: {
        type: Object,
        default: null,
    },
    open: {
        type: Boolean,
        default: false,
    },
    collapsed: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['close'])

const logout = () => {
    router.post('/logout')
}

// Role dari Spatie (shared `auth`) + fallback kolom legacy `user.role`.
// Jabatan baru (ketua-bem, bendahara, ...) otomatis dianggap pengurus di menu.
const page = usePage()
const authRoles = computed(() => page.props.auth?.roles ?? [])
const authPermissions = computed(() => page.props.auth?.permissions ?? [])
const hasRole = (...names) => authRoles.value.some((r) => names.includes(r))
const can = (...names) => authPermissions.value.some((p) => names.includes(p))

const isPengurus = computed(
    () => hasRole('pengurus', 'ketua-bem', 'wakil-ketua', 'sekretaris', 'bendahara') || props.user?.role === 'pengurus',
)
const isMahasiswa = computed(() => hasRole('mahasiswa') || props.user?.role === 'mahasiswa')

const groups = computed(() => {
    const list = [
        {
            label: 'UTAMA',
            menus: [
                {
                    label: 'Dashboard',
                    href: '/dashboard',
                    key: 'dashboard',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
                },
            ],
        },
    ]

    // Profil Saya: untuk semua user yang login.
    list[0].menus.push({
        label: 'Profil Saya',
        href: '/profil',
        key: 'profil',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    })

    // if (isMahasiswa.value || isPengurus.value) {
    //     list[0].menus.push({
    //         label: 'Daftar Pengurus',
    //         href: '/pendaftaran',
    //         key: 'pendaftaran',
    //         icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />',
    //     })
    // }

    if (isPengurus.value) {
        list.push({
            label: 'KELOLA',
            menus: [
                {
                    label: 'Manajemen User',
                    href: '/users',
                    key: 'users',
                    perm: 'kelola-pengguna',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                },
                {
                    label: 'Akses & Role',
                    key: 'akses',
                    perm: 'kelola-pengguna',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a3 3 0 012.83 4M15 7a3 3 0 00-5.83 1M15 7V5a2 2 0 00-2-2H6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h7a2 2 0 002-2v-2M15 7a3 3 0 01-2.83 4M9 12l2 2 4-4" />',
                    children: [
                        { label: 'Daftar Role', href: '/roles', key: 'roles' },
                        // { label: 'Tambah Role', href: '/roles/create', key: 'roles-create' },
                        { label: 'Permissions', href: '/permissions', key: 'permissions' },
                        // { label: 'Tambah Permission', href: '/permissions/create', key: 'permissions-create' },
                    ],
                },
                {
                    label: 'Pendaftar BEM',
                    href: '/admin/pendaftaran',
                    key: 'pendaftaran-admin',
                    perm: 'kelola-pendaftaran',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
                },
                {
                    label: 'Departemen',
                    href: '/departemen',
                    key: 'departemen',
                    perm: 'kelola-departemen',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
                },
                {
                    label: 'Program Kerja',
                    href: '/proker',
                    key: 'proker',
                    perm: 'kelola-proker',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
                },
                {
                    label: 'Informasi',
                    href: '/informasi',
                    key: 'informasi',
                    perm: 'kelola-informasi',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />',
                },
                {
                    label: 'Keuangan',
                    key: 'keuangan-group',
                    perm: 'kelola-keuangan',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />',
                    children: [
                        { label: 'Transaksi Kas', href: '/keuangan', key: 'keuangan' },
                        { label: 'Manajemen Iuran', href: '/iuran', key: 'iuran' },
                        { label: 'Rekap & Cetak', href: '/keuangan/rekap', key: 'keuangan-rekap' },
                    ],
                },
                {
                    label: 'Absensi',
                    key: 'absensi-group',
                    perm: 'kelola-absensi',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m6 9l2 2 4-4" />',
                    children: [
                        { label: 'Daftar Kegiatan', href: '/kegiatan', key: 'kegiatan' },
                        { label: 'Rekap Skor', href: '/absensi/rekap', key: 'absensi-rekap' },
                    ],
                },
            ].filter((menu) => !menu.perm || can(menu.perm)),
        })

        // Buang grup KELOLA bila tidak ada menu yang boleh dilihat.
        if (list[list.length - 1].menus.length === 0) {
            list.pop()
        }
    }

    // Activity Log: hanya yang punya permission (read-only).
    if (can('lihat-activity-log')) {
        list.push({
            label: 'SISTEM',
            menus: [
                {
                    label: 'Activity Log',
                    href: '/activity-log',
                    key: 'activity-log',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
                },
            ],
        })
    }

    return list
})

// Dropdown state: terbuka otomatis saat salah satu anaknya aktif.
// PENTING: pakai pencocokan persis (===), bukan startsWith —
// 'roles-create'.startsWith('roles-') dulu bikin Daftar Role ikut nyala.
const openDropdown = ref({})
const isActiveChild = (child) => props.activeMenu === child.key
const isChildActive = (menu) => (menu.children ?? []).some(isActiveChild)
const isOpen = (menu) => openDropdown.value[menu.key] ?? isChildActive(menu)
const toggleDropdown = (menu) => {
    openDropdown.value[menu.key] = !isOpen(menu)
}
</script>

<template>
    <!-- Overlay mobile -->
    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-show="open"
            class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
            @click="$emit('close')"
        ></div>
    </Transition>

    <aside
        :class="[
            open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            collapsed ? 'lg:w-20' : 'lg:w-72',
        ]"
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-white/10 bg-[#05080f]/95 backdrop-blur-xl transition-[width,transform] duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
    >
        <!-- Logo -->
        <div
            class="flex items-center gap-3 border-b border-white/5 px-5 py-5"
            :class="collapsed ? 'lg:justify-center lg:px-3' : ''"
        >
            <div class="relative flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-cyan-300/20 bg-cyan-300/5">
                <div class="absolute inset-0 bg-cyan-300/10 blur-xl"></div>
                <img :src="'/images/saintek.png'" alt="BEM Saintek" class="relative h-10 w-10 object-contain" />
            </div>
            <div :class="collapsed ? 'lg:hidden' : ''" class="transition-opacity duration-200">
                <div class="font-display font-bold tracking-tight text-white">
                    BEM <span class="text-cyan-300">SAINTEK</span>
                </div>
                <div class="font-mono text-[8px] uppercase tracking-[.3em] text-white/30">
                    Admin Terminal
                </div>
            </div>
        </div>

        <!-- Menu (overflow dibuka saat collapsed agar tooltip tidak kepotong) -->
        <nav class="saintek-scroll flex-1 space-y-6 overflow-y-auto p-4" :class="collapsed ? 'lg:overflow-visible lg:p-3' : ''">
            <div v-for="group in groups" :key="group.label">
                <p
                    class="mb-2 px-4 font-mono text-[10px] tracking-[.3em] text-white/25 transition-all"
                    :class="collapsed ? 'lg:hidden' : ''"
                >
                    {{ group.label }}
                </p>
                <!-- divider mungil saat collapsed -->
                <div v-if="collapsed" class="mx-auto mb-2 hidden h-px w-8 bg-white/10 lg:block"></div>
                <div class="space-y-1.5">
                    <template v-for="menu in group.menus" :key="menu.key">
                        <!-- Menu dropdown (punya anak) -->
                        <div v-if="menu.children">
                            <button
                                @click="toggleDropdown(menu)"
                                :class="[
                                    isChildActive(menu)
                                        ? 'border-cyan-300/30 bg-cyan-300/10 text-white shadow-[0_0_24px_rgba(34,211,238,0.12)]'
                                        : 'border-transparent text-white/45 hover:border-white/5 hover:bg-white/[0.04] hover:text-white',
                                    collapsed ? 'lg:justify-center lg:px-0' : '',
                                    'group relative flex w-full items-center gap-3 rounded-xl border px-4 py-2.5 text-sm font-medium transition-all duration-200',
                                ]"
                            >
                                <svg
                                    class="h-5 w-5 shrink-0 transition-transform duration-200 group-hover:scale-110"
                                    :class="isChildActive(menu) ? 'text-cyan-300' : 'text-white/30 group-hover:text-white/70'"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    v-html="menu.icon"
                                />
                                <span :class="collapsed ? 'lg:hidden' : ''" class="truncate">{{ menu.label }}</span>
                                <svg
                                    :class="(collapsed ? 'lg:hidden ' : '') + (isOpen(menu) ? 'rotate-180 ' : '')"
                                    class="ml-auto h-4 w-4 shrink-0 text-white/40 transition-transform duration-200"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <!-- Tooltip saat collapsed (desktop saja) -->
                                <span
                                    v-if="collapsed"
                                    class="pointer-events-none absolute left-full z-[60] ml-3 hidden origin-left scale-95 whitespace-nowrap rounded-lg border border-white/10 bg-[#0a0f1a] px-3 py-1.5 text-xs font-semibold text-white opacity-0 shadow-xl transition-all duration-200 group-hover:scale-100 group-hover:opacity-100 lg:group-hover:block"
                                >
                                    {{ menu.label }}
                                    <span class="absolute -left-1 top-1/2 h-2 w-2 -translate-y-1/2 rotate-45 border-b border-l border-white/10 bg-[#0a0f1a]"></span>
                                </span>
                            </button>
                            <div v-show="isOpen(menu)" :class="collapsed ? 'lg:hidden' : ''" class="mb-1 ml-5 mt-1 space-y-1 border-l border-white/10 pl-3">
                                <Link
                                    v-for="child in menu.children"
                                    :key="child.key"
                                    :href="child.href"
                                    @click="$emit('close')"
                                    :class="[
                                        isActiveChild(child)
                                            ? 'bg-cyan-300/10 text-white'
                                            : 'text-white/45 hover:bg-white/[0.04] hover:text-white',
                                        'flex items-center gap-2 rounded-lg px-3 py-2 text-[13px] font-medium transition-all duration-200',
                                    ]"
                                >
                                    <span
                                        :class="isActiveChild(child) ? 'bg-cyan-300' : 'bg-white/20'"
                                        class="h-1 w-1 shrink-0 rounded-full"
                                    ></span>
                                    <span class="truncate">{{ child.label }}</span>
                                </Link>
                            </div>
                        </div>
                        <!-- Menu biasa -->
                        <Link
                            v-else
                            :href="menu.href"
                            @click="$emit('close')"
                            :class="[
                                activeMenu === menu.key
                                    ? 'border-cyan-300/30 bg-cyan-300/10 text-white shadow-[0_0_24px_rgba(34,211,238,0.12)]'
                                    : 'border-transparent text-white/45 hover:border-white/5 hover:bg-white/[0.04] hover:text-white',
                                collapsed ? 'lg:justify-center lg:px-0' : '',
                                'group relative flex items-center gap-3 rounded-xl border px-4 py-2.5 text-sm font-medium transition-all duration-200',
                            ]"
                        >
                            <svg
                                class="h-5 w-5 shrink-0 transition-transform duration-200 group-hover:scale-110"
                                :class="activeMenu === menu.key ? 'text-cyan-300' : 'text-white/30 group-hover:text-white/70'"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                v-html="menu.icon"
                            />
                            <span :class="collapsed ? 'lg:hidden' : ''" class="truncate">{{ menu.label }}</span>
                            <span
                                v-if="activeMenu === menu.key"
                                :class="collapsed ? 'lg:hidden' : ''"
                                class="ml-auto h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-300 shadow-[0_0_10px_rgba(103,232,249,0.9)]"
                            ></span>
                            <!-- active dot versi collapsed -->
                            <span
                                v-if="activeMenu === menu.key && collapsed"
                                class="absolute -right-[13px] hidden h-6 w-1 rounded-full bg-cyan-300 shadow-[0_0_10px_rgba(103,232,249,0.9)] lg:block"
                            ></span>

                            <!-- Tooltip saat collapsed (desktop saja) -->
                            <span
                                v-if="collapsed"
                                class="pointer-events-none absolute left-full z-[60] ml-3 hidden origin-left scale-95 whitespace-nowrap rounded-lg border border-white/10 bg-[#0a0f1a] px-3 py-1.5 text-xs font-semibold text-white opacity-0 shadow-xl transition-all duration-200 group-hover:scale-100 group-hover:opacity-100 lg:group-hover:block"
                            >
                                {{ menu.label }}
                                <span class="absolute -left-1 top-1/2 h-2 w-2 -translate-y-1/2 rotate-45 border-b border-l border-white/10 bg-[#0a0f1a]"></span>
                            </span>
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Bawah: user + lihat website -->
        <div class="space-y-3 border-t border-white/5 p-4" :class="collapsed ? 'lg:p-3' : ''">
            <a
                href="/"
                :class="collapsed ? 'lg:justify-center lg:px-0' : ''"
                class="group relative flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2.5 font-mono text-[11px] tracking-widest text-white/50 transition hover:border-cyan-300/30 hover:text-cyan-300"
            >
                <span :class="collapsed ? 'lg:hidden' : ''">← LIHAT WEBSITE</span>
                <span v-if="collapsed" class="hidden text-sm lg:block">↗</span>
                <span
                    v-if="collapsed"
                    class="pointer-events-none absolute left-full z-[60] ml-3 hidden whitespace-nowrap rounded-lg border border-white/10 bg-[#0a0f1a] px-3 py-1.5 font-sans text-xs font-semibold normal-case tracking-normal text-white opacity-0 shadow-xl transition-all duration-200 group-hover:opacity-100 lg:group-hover:block"
                >
                    Lihat Website
                </span>
            </a>
            <Link
                v-if="user"
                href="/profil"
                @click="$emit('close')"
                :class="collapsed ? 'lg:justify-center lg:px-2' : ''"
                class="group relative flex items-center gap-3 rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 transition hover:border-cyan-300/25 hover:bg-cyan-300/5"
                title="Lihat profil saya"
            >
                <img
                    v-if="user.foto_url"
                    :src="user.foto_url"
                    :alt="user.name"
                    class="h-9 w-9 shrink-0 rounded-full border border-white/20 object-cover"
                />
                <div
                    v-else
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 text-sm font-bold text-black"
                >
                    {{ (user.name || '?').charAt(0).toUpperCase() }}
                </div>
                <div :class="collapsed ? 'lg:hidden' : ''" class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-white">{{ user.name }}</p>
                    <p class="font-mono text-[10px] uppercase tracking-widest text-cyan-300/70">{{ user.role }}</p>
                </div>
                <svg
                    :class="collapsed ? 'lg:hidden' : ''"
                    class="h-4 w-4 shrink-0 text-white/20 transition group-hover:translate-x-0.5 group-hover:text-cyan-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span
                    v-if="collapsed"
                    class="pointer-events-none absolute left-full z-[60] ml-3 hidden whitespace-nowrap rounded-lg border border-white/10 bg-[#0a0f1a] px-3 py-1.5 text-xs font-semibold text-white opacity-0 shadow-xl transition-all duration-200 group-hover:opacity-100 lg:group-hover:block"
                >
                    {{ user.name }}
                    <span class="block font-mono text-[10px] font-normal uppercase tracking-widest text-cyan-300/70">{{ user.role }}</span>
                </span>
            </Link>
            <button
                @click="logout"
                :class="collapsed ? 'lg:justify-center lg:px-0' : ''"
                class="group relative flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-rose-300/20 bg-rose-300/5 px-4 py-2.5 text-sm font-semibold text-rose-300 transition hover:border-rose-300/40 hover:bg-rose-300/10 active:scale-[0.98]"
            >
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span :class="collapsed ? 'lg:hidden' : ''">Keluar</span>
                <span
                    v-if="collapsed"
                    class="pointer-events-none absolute left-full z-[60] ml-3 hidden whitespace-nowrap rounded-lg border border-white/10 bg-[#0a0f1a] px-3 py-1.5 text-xs font-semibold text-white opacity-0 shadow-xl transition-all duration-200 group-hover:opacity-100 lg:group-hover:block"
                >
                    Keluar
                </span>
            </button>
        </div>
    </aside>
</template>
