<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const navDepartemens = computed(() => page.props.navDepartemens ?? [])

const isOpen = ref(false)
const isProfileOpen = ref(false)
const isDepartemenOpen = ref(false)
const isHimaOpen = ref(false)
const mobileDepartemenOpen = ref(false)
const mobileProfileOpen = ref(false)
const mobileHimaOpen = ref(false)
const scrolled = ref(false)
const progress = ref(0)

const departemenWrapRef = ref(null)
const profileWrapRef = ref(null)
const himaWrapRef = ref(null)

const anchorLinks = [
    { label: 'Home', href: '/' },
    {label: 'Informations', href: '/information' },
    // { label: 'Departments', href: '#departments' },
    // { label: 'Mission', href: '#mission' },
    // { label: 'Programs', href: '#programs' },
    // { label: 'Archive', href: '#archive' },
]

const profileLinks = [
    { label: 'Struktural', href: '/profile/struktural' },
    { label: 'Visi & Misi', href: '/profile/visi-misi' },
    { label: 'Sejarah', href: '/profile/sejarah' },
    { label: 'HIMA', href: '/profile/hima' },
    { label: 'Laboratorium', href: '/profile/laboratorium' },
]

const himaLinks = [
    { label: 'HIMATIF — Teknologi Informasi', href: '/profile/hima/himatif' },
    { label: 'HIMASI — Sistem Informasi', href: '/profile/hima/himasi' },
    { label: 'HIMAKOM — Ilmu Komputer', href: '/profile/hima/himakom' },
    { label: 'HIMARS — Arsitektur', href: '/profile/hima/himars' },
    { label: 'HIMABP — Budidaya Perikanan', href: '/profile/hima/himabp' },
    { label: 'HIMATHP — Teknologi Hasil Perikanan', href: '/profile/hima/himathp' },
]

function toggleDepartemen() {
    isDepartemenOpen.value = !isDepartemenOpen.value
    if (isDepartemenOpen.value) {
        isProfileOpen.value = false
        isHimaOpen.value = false
    }
}

function toggleProfile() {
    isProfileOpen.value = !isProfileOpen.value
    if (isProfileOpen.value) {
        isDepartemenOpen.value = false
        isHimaOpen.value = false
    }
}

function toggleHima() {
    isHimaOpen.value = !isHimaOpen.value
    if (isHimaOpen.value) {
        isDepartemenOpen.value = false
        isProfileOpen.value = false
    }
}

function closeDropdowns() {
    isDepartemenOpen.value = false
    isProfileOpen.value = false
    isHimaOpen.value = false
}

function onDocumentClick(e) {
    const target = e.target
    if (
        isDepartemenOpen.value &&
        departemenWrapRef.value &&
        !departemenWrapRef.value.contains(target)
    ) {
        isDepartemenOpen.value = false
    }
    if (
        isProfileOpen.value &&
        profileWrapRef.value &&
        !profileWrapRef.value.contains(target)
    ) {
        isProfileOpen.value = false
    }
    if (
        isHimaOpen.value &&
        himaWrapRef.value &&
        !himaWrapRef.value.contains(target)
    ) {
        isHimaOpen.value = false
    }
}

function onKeydown(e) {
    if (e.key === 'Escape') closeDropdowns()
}

function onScroll() {
    const y = window.scrollY
    scrolled.value = y > 30
    const total = document.documentElement.scrollHeight - window.innerHeight
    progress.value = total > 0 ? (y / total) * 100 : 0
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true })
    document.addEventListener('click', onDocumentClick)
    document.addEventListener('keydown', onKeydown)
    onScroll()
})

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll)
    document.removeEventListener('click', onDocumentClick)
    document.removeEventListener('keydown', onKeydown)
})
</script>

<template>
    <div
        class="fixed top-0 left-0 h-[2px] z-[200] bg-gradient-to-r from-cyan-300 via-blue-500 to-violet-500 transition-[width]"
        :style="{ width: progress + '%' }"
    ></div>

    <header
        class="fixed top-0 left-0 right-0 z-[100] border-b border-transparent transition-all duration-300"
        :class="scrolled ? 'bg-[#03050a]/75 backdrop-blur-xl border-white/10' : ''"
    >
        <div class="max-w-7xl mx-auto px-5 lg:px-8">
            <nav class="h-20 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl border border-cyan-300/20 bg-cyan-300/5 flex items-center justify-center relative overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-cyan-300/10 blur-xl"></div>
                        <!-- <span class="relative font-display font-bold text-cyan-300">S</span> -->
                <img :src="'/images/saintek.png'" class="w-10 h-10 " />

                    </div>
                    <div>
                        <div class="font-display font-bold tracking-tight text-white">
                            BEM <span class="text-cyan-300">SAINTEK</span>
                        </div>
                        <div class="text-[8px] text-white/30 tracking-[.3em] uppercase font-mono">
                            Beyond Limits
                        </div>
                    </div>
                </a>

                <div class="hidden lg:flex items-center gap-7 text-xs text-white/45">

                    <!-- NAVIGATION START-->
                     <a
                        v-for="link in anchorLinks"
                        :key="link.href"
                        :href="link.href"
                        class="hover:text-white transition"
                        @click="closeDropdowns"
                    >
                        {{ link.label }}
                    </a>
                    <!-- NAVIGATION END-->

                  
                    <!-- PROFIL: klik untuk buka, bukan hover -->
                    <div ref="profileWrapRef" class="relative">
                        <button
                            type="button"
                            class="flex items-center gap-1 hover:text-white transition cursor-pointer"
                            :aria-expanded="isProfileOpen"
                            aria-haspopup="true"
                            @click="toggleProfile"
                        >
                            Profil
                            <svg
                                class="w-3 h-3 transition-transform duration-200"
                                :class="{ 'rotate-180': isProfileOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            v-show="isProfileOpen"
                            class="absolute top-full right-0 pt-3 w-48 z-50"
                        >
                            <div class="rounded-2xl glass !bg-[#0a0f1a]/95 shadow-2xl border border-white/10 p-2">
                                <Link
                                    v-for="link in profileLinks"
                                    :key="link.href"
                                    :href="link.href"
                                    class="block px-4 py-2 text-xs text-white/60 hover:text-white hover:bg-white/5 rounded-xl transition"
                                    @click="closeDropdowns"
                                >
                                    {{ link.label }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- PROGRAM: link langsung -->
                    <Link
                        href="/program-kerja"
                        class="hover:text-white transition"
                        @click="closeDropdowns"
                    >
                        Program
                    </Link>

                    <!-- DEPARTEMEN: klik untuk buka, bukan hover -->
                    <div ref="departemenWrapRef" class="relative">
                        <button
                            type="button"
                            class="flex items-center gap-1 hover:text-white transition cursor-pointer"
                            :aria-expanded="isDepartemenOpen"
                            aria-haspopup="true"
                            @click="toggleDepartemen"
                        >
                            Departemen
                            <svg
                                class="w-3 h-3 transition-transform duration-200"
                                :class="{ 'rotate-180': isDepartemenOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            v-show="isDepartemenOpen"
                            class="absolute top-full left-0 pt-3 w-64 z-50"
                        >
                            <div class="rounded-2xl glass !bg-[#0a0f1a]/95 shadow-2xl border border-white/10 p-2">
                                <Link
                                    v-for="dept in navDepartemens"
                                    :key="dept.id"
                                    :href="`/departemen/${dept.id}`"
                                    class="flex items-center gap-2 px-4 py-2 text-xs text-white/60 hover:text-white hover:bg-white/5 rounded-xl transition"
                                    @click="closeDropdowns"
                                >
                                    <span>{{ dept.icon }}</span> {{ dept.nama }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- HIMA: klik untuk buka, bukan hover -->
                    <div ref="himaWrapRef" class="relative">
                        <button
                            type="button"
                            class="flex items-center gap-1 hover:text-white transition cursor-pointer"
                            :aria-expanded="isHimaOpen"
                            aria-haspopup="true"
                            @click="toggleHima"
                        >
                            HIMA
                            <svg
                                class="w-3 h-3 transition-transform duration-200"
                                :class="{ 'rotate-180': isHimaOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            v-show="isHimaOpen"
                            class="absolute top-full left-0 pt-3 w-64 z-50"
                        >
                            <div class="rounded-2xl glass !bg-[#0a0f1a]/95 shadow-2xl border border-white/10 p-2">
                                <Link
                                    href="/profile/hima"
                                    class="block px-4 py-2.5 text-xs font-bold text-cyan-300 hover:bg-white/5 rounded-xl transition"
                                    @click="closeDropdowns"
                                >
                                    Semua HIMA
                                </Link>
                                <Link
                                    v-for="hima in himaLinks"
                                    :key="hima.href"
                                    :href="hima.href"
                                    class="block px-4 py-2 text-xs text-white/60 hover:text-white hover:bg-white/5 rounded-xl transition"
                                    @click="closeDropdowns"
                                >
                                    {{ hima.label }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    

                     

                </div>

                <a
                    href="/login"
                    class="hidden sm:flex items-center gap-2 px-5 py-2.5 rounded-full bg-white text-black text-xs font-bold hover:bg-cyan-300 transition"
                >
                    Login
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M7 7h10v10" />
                    </svg>
                </a>

                <button
                    class="lg:hidden w-10 h-10 rounded-xl border border-white/10 flex items-center justify-center text-white"
                    @click="isOpen = !isOpen"
                    aria-label="Menu"
                >
                    <svg v-if="!isOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </nav>

            <div v-show="isOpen" class="lg:hidden pb-4">
                <div class="glass rounded-2xl p-3 flex flex-col">
                    <a
                        v-for="link in anchorLinks"
                        :key="link.href"
                        :href="link.href"
                        class="px-4 py-3 text-sm text-white/60 hover:text-white transition"
                        @click="isOpen = false"
                    >
                        {{ link.label }}
                    </a>
                    <div class="h-px bg-white/5 my-1"></div>

                    <!-- Mobile: link Program langsung -->
                    <Link
                        href="/program-kerja"
                        class="px-4 py-3 text-sm font-bold text-white/80 hover:text-white transition"
                        @click="isOpen = false"
                    >
                        Program
                    </Link>

                    <div class="h-px bg-white/5 my-1"></div>

                    <!-- Mobile: accordion Departemen (klik) -->
                    <button
                        type="button"
                        class="flex items-center justify-between px-4 py-3 text-sm font-bold text-white/80 hover:text-white transition cursor-pointer"
                        :aria-expanded="mobileDepartemenOpen"
                        @click="mobileDepartemenOpen = !mobileDepartemenOpen"
                    >
                        Departemen
                        <svg
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': mobileDepartemenOpen }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div v-show="mobileDepartemenOpen" class="flex flex-col pl-2">
                        <Link
                            v-for="dept in navDepartemens"
                            :key="dept.id"
                            :href="`/departemen/${dept.id}`"
                            class="px-4 py-2 text-xs text-white/40"
                            @click="isOpen = false"
                        >
                            {{ dept.icon }} {{ dept.nama }}
                        </Link>
                    </div>

                    <div class="h-px bg-white/5 my-1"></div>

                    <!-- Mobile: accordion HIMA (klik) -->
                    <button
                        type="button"
                        class="flex items-center justify-between px-4 py-3 text-sm font-bold text-white/80 hover:text-white transition cursor-pointer"
                        :aria-expanded="mobileHimaOpen"
                        @click="mobileHimaOpen = !mobileHimaOpen"
                    >
                        HIMA
                        <svg
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': mobileHimaOpen }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div v-show="mobileHimaOpen" class="flex flex-col pl-2">
                        <Link
                            href="/profile/hima"
                            class="px-4 py-2 text-xs font-bold text-cyan-300"
                            @click="isOpen = false"
                        >
                            Semua HIMA
                        </Link>
                        <Link
                            v-for="hima in himaLinks"
                            :key="hima.href"
                            :href="hima.href"
                            class="px-4 py-2 text-xs text-white/40"
                            @click="isOpen = false"
                        >
                            {{ hima.label }}
                        </Link>
                    </div>

                    <div class="h-px bg-white/5 my-1"></div>

                    <!-- Mobile: accordion Profil (klik) -->
                    <button
                        type="button"
                        class="flex items-center justify-between px-4 py-3 text-sm font-bold text-white/80 hover:text-white transition cursor-pointer"
                        :aria-expanded="mobileProfileOpen"
                        @click="mobileProfileOpen = !mobileProfileOpen"
                    >
                        Profil
                        <svg
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': mobileProfileOpen }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div v-show="mobileProfileOpen" class="flex flex-col pl-2">
                        <Link
                            v-for="link in profileLinks"
                            :key="link.href"
                            :href="link.href"
                            class="px-4 py-2 text-xs text-white/40"
                            @click="isOpen = false"
                        >
                            {{ link.label }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
