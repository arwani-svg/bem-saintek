
<script setup>
import { computed, ref } from 'vue'
import Modal from '@/components/dashboard/Modal.vue'
import EditProfil from '@/Pages/Profil/Edit.vue'

const props = defineProps({
    profile: {
        type: Object,
        required: true,
    },
    editHref: {
        type: String,
        default: '/profil/edit',
    },
    editLabel: {
        type: String,
        default: 'Edit Profil',
    },
    prestasiNote: {
        type: String,
        default: '',
    },
    canEditPrestasi: {
        type: Boolean,
        default: false,
    },
})

const showModal = ref(false)

const openEdit = () => {
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const prestasiList = computed(() => {
    if (
        Array.isArray(props.profile?.prestasi_list) &&
        props.profile.prestasi_list.length > 0
    ) {
        return props.profile.prestasi_list
    }

    const raw = props.profile?.prestasi ?? ''

    if (typeof raw !== 'string' || raw.trim() === '') return []

    const trimmed = raw.trim()

    if (trimmed.startsWith('[')) {
        try {
            const decoded = JSON.parse(trimmed)

            if (Array.isArray(decoded)) {
                return decoded
                    .map(v => String(v))
                    .filter(v => v.trim() !== '')
            }
        } catch (e) {
            // Lanjut parsing sebagai teks biasa.
        }
    }

    return trimmed
        .split(/\r\n|\r|\n/)
        .map(v => v.trim())
        .filter(v => v !== '')
})

const alamatLengkap = computed(() => {
    const parts = [
        props.profile.desa,
        props.profile.kecamatan,
        props.profile.kabupaten,
        props.profile.provinsi,
    ].filter(Boolean)

    return parts.length > 0 ? parts.join(', ') : '-'
})

const ttl = computed(() => {
    const tempat = props.profile.tempat_lahir || ''
    const tanggal = props.profile.tanggal_lahir
        ? String(props.profile.tanggal_lahir).substring(0, 10)
        : ''

    if (!tempat && !tanggal) return '-'

    return [tempat, tanggal].filter(Boolean).join(', ')
})

const initial = computed(() =>
    (props.profile?.name?.charAt(0) || 'M').toUpperCase()
)

const progressPersen = computed(() => {
    const sem = Number(props.profile?.semester) || 1
    return Math.min(100, Math.round((sem / 8) * 100))
})

const stats = computed(() => [
    {
        label: 'Semester',
        value: props.profile?.semester ?? '-',
        sub: `Angkatan ${props.profile?.angkatan ?? '-'}`,
        icon: 'cap',
    },
    {
        label: 'Prestasi',
        value: prestasiList.value.length,
        sub: 'Point tercatat',
        icon: 'award',
    },
    {
        label: 'Kelas',
        value: props.profile?.kelas || '-',
        sub: props.profile?.prodi || 'Prodi',
        icon: 'book',
    },
    {
        label: 'Status',
        value: props.profile?.status
            ? String(props.profile.status).toUpperCase()
            : '-',
        sub: props.profile?.role || 'Mahasiswa',
        icon: 'check',
    },
])
</script>



<template>
    <div>
        <!-- ================= HERO ================= -->
        <section class="relative mb-4 overflow-hidden rounded-2xl border border-white/10 xl:mb-6">
            <!-- latar tema kita: cyan -> violet di atas base gelap -->
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-400/20 via-sky-500/10 to-violet-500/25"></div>
            <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-cyan-400/10 blur-2xl xl:h-72 xl:w-72"></div>
            <div class="pointer-events-none absolute -bottom-28 -left-20 h-64 w-64 rounded-full bg-violet-500/10 blur-2xl xl:h-80 xl:w-80"></div>

            <div class="relative p-4 sm:p-6 xl:p-8">
                <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center xl:gap-6">
                    <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-start xl:gap-5">
                        <!-- Avatar: foto profil bila ada, fallback inisial -->
                        <div class="relative">
                            <img
                                v-if="profile.foto_url"
                                :src="profile.foto_url"
                                :alt="profile.name"
                                class="h-24 w-24 rounded-3xl border border-white/20 object-cover shadow-xl xl:h-28 xl:w-28"
                            />
                            <div
                                v-else
                                class="flex h-24 w-24 items-center justify-center rounded-3xl border border-white/20 bg-gradient-to-br from-cyan-300 to-violet-500 font-display text-3xl font-bold text-black shadow-xl xl:h-28 xl:w-28 xl:text-4xl"
                            >
                                {{ initial }}
                            </div>
                            <span class="absolute bottom-2 right-2 h-4 w-4 rounded-full border-4 border-[#0a0f1a] bg-emerald-400 xl:h-5 xl:w-5"></span>
                        </div>

                        <div class="text-center sm:text-left">
                            <div class="mb-2 flex flex-wrap justify-center gap-1.5 sm:justify-start xl:gap-2">
                                <span class="dash-badge bg-cyan-300/10 capitalize text-cyan-300">{{ profile.status || 'aktif' }}</span>
                                <span class="dash-badge bg-violet-300/10 capitalize text-violet-300">{{ profile.role || 'mahasiswa' }}</span>
                            </div>

                            <h1 class="font-display text-2xl font-bold tracking-tight text-white sm:text-3xl xl:text-4xl">
                                {{ profile.name }}
                            </h1>
                            <p class="mb-2 font-mono text-xs text-white/50 xl:mb-3 xl:text-sm">
                                {{ profile.nim }} • {{ profile.prodi || '-' }}
                            </p>

                            <div class="flex flex-wrap justify-center gap-3 text-xs text-white/50 sm:justify-start xl:gap-4 xl:text-sm">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-cyan-300 xl:h-4 xl:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    BEM Saintek
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-cyan-300 xl:h-4 xl:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Angkatan {{ profile.angkatan ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- <Link :href="editHref" class="dash-btn-primary shrink-0">
                        <svg class="h-3.5 w-3.5 xl:h-4 xl:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        {{ editLabel }}
                    </Link> -->

                   <button
                        type="button"
                        @click="openEdit"
                        class="dash-btn-primary shrink-0"
                    >
                        <svg
                            class="h-3.5 w-3.5 xl:h-4 xl:w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>

                        {{ editLabel }}
                    </button>
                </div>
            </div>
        </section>

        <!-- ================= STATISTIK ================= -->
        <section class="mb-4 grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-4 xl:mb-6 xl:gap-4">
            <div v-for="s in stats" :key="s.label" class="dash-card p-3 xl:p-5">
                <div class="mb-2 flex items-center justify-between xl:mb-3">
                    <div class="rounded-lg bg-cyan-300/10 p-2 text-cyan-300 xl:rounded-xl xl:p-2.5">
                        <svg v-if="s.icon === 'cap'" class="h-4 w-4 xl:h-5 xl:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 13.5c0 2.485-4.03 4.5-9 4.5s-9-2.015-9-4.5c0-.98.21-1.9.598-2.722L12 14z" /></svg>
                        <svg v-else-if="s.icon === 'award'" class="h-4 w-4 xl:h-5 xl:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                        <svg v-else-if="s.icon === 'book'" class="h-4 w-4 xl:h-5 xl:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        <svg v-else class="h-4 w-4 xl:h-5 xl:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="hidden font-mono text-[10px] text-white/30 sm:block xl:text-[11px]">{{ s.sub }}</span>
                </div>
                <p class="font-display text-lg font-bold text-white xl:text-2xl">{{ s.value }}</p>
                <p class="text-xs text-white/40 xl:text-sm">{{ s.label }}</p>
            </div>
        </section>

        <!-- ================= GRID UTAMA ================= -->
        <section class="grid grid-cols-1 gap-4 xl:grid-cols-3 xl:gap-6">
            <!-- KIRI -->
            <div class="space-y-4 xl:col-span-2 xl:space-y-6">
                <!-- IDENTITAS -->
                <div class="dash-card overflow-hidden">
                    <div class="flex items-center justify-between border-b border-white/5 px-4 py-4 xl:px-6 xl:py-5">
                        <div>
                            <h3 class="font-display text-base font-bold text-white xl:text-lg">Identitas Diri</h3>
                            <p class="mt-1 text-xs text-white/40 xl:text-sm">Informasi identitas pribadi</p>
                        </div>
                        <div class="rounded-lg bg-cyan-300/10 p-2 text-cyan-300 xl:rounded-xl xl:p-2.5">
                            <svg class="h-4 w-4 xl:h-5 xl:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83-2" /></svg>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-x-6 gap-y-4 p-4 md:grid-cols-2 xl:gap-x-8 xl:gap-y-6 xl:p-6">
                        <div><p class="dash-label">Nama Lengkap</p><p class="font-semibold text-white">{{ profile.name }}</p></div>
                        <div><p class="dash-label">NIM</p><p class="font-mono font-semibold text-white">{{ profile.nim }}</p></div>
                        <div><p class="dash-label">Tempat, Tanggal Lahir</p><p class="font-semibold text-white">{{ ttl }}</p></div>
                        <div><p class="dash-label">Jenis Kelamin</p><p class="font-semibold capitalize text-white">{{ profile.jenis_kelamin || '-' }}</p></div>
                        <div><p class="dash-label">Role</p><p class="font-semibold capitalize text-white">{{ profile.role || '-' }}</p></div>
                        <div><p class="dash-label">Status</p><p class="font-semibold capitalize text-white">{{ profile.status || '-' }}</p></div>
                    </div>
                </div>

                <!-- AKADEMIK -->
                <div class="dash-card overflow-hidden">
                    <div class="flex items-center justify-between border-b border-white/5 px-4 py-4 xl:px-6 xl:py-5">
                        <div>
                            <h3 class="font-display text-base font-bold text-white xl:text-lg">Informasi Akademik</h3>
                            <p class="mt-1 text-xs text-white/40 xl:text-sm">Data akademik dan perkuliahan</p>
                        </div>
                        <div class="rounded-lg bg-violet-300/10 p-2 text-violet-300 xl:rounded-xl xl:p-2.5">
                            <svg class="h-4 w-4 xl:h-5 xl:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 13.5c0 2.485-4.03 4.5-9 4.5s-9-2.015-9-4.5c0-.98.21-1.9.598-2.722L12 14z" /></svg>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-x-6 gap-y-4 p-4 md:grid-cols-2 xl:gap-x-8 xl:gap-y-6 xl:p-6">
                        <div><p class="dash-label">Program Studi</p><p class="font-semibold text-white">{{ profile.prodi || '-' }}</p></div>
                        <div><p class="dash-label">Angkatan</p><p class="font-semibold text-white">{{ profile.angkatan ?? '-' }}</p></div>
                        <div><p class="dash-label">Semester</p><p class="font-semibold text-white">{{ profile.semester ?? '-' }}</p></div>
                        <div><p class="dash-label">Kelas</p><p class="font-semibold text-white">{{ profile.kelas || '-' }}</p></div>
                    </div>
                </div>

                <!-- ALAMAT -->
                <div class="dash-card overflow-hidden">
                    <div class="flex items-center justify-between border-b border-white/5 px-4 py-4 xl:px-6 xl:py-5">
                        <div>
                            <h3 class="font-display text-base font-bold text-white xl:text-lg">Alamat</h3>
                            <p class="mt-1 text-xs text-white/40 xl:text-sm">Informasi tempat tinggal</p>
                        </div>
                        <div class="rounded-lg bg-emerald-300/10 p-2 text-emerald-300 xl:rounded-xl xl:p-2.5">
                            <svg class="h-4 w-4 xl:h-5 xl:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                    </div>
                    <div class="p-4 xl:p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:gap-6">
                            <div>
                                <p class="dash-label">Alamat Lengkap</p>
                                <p class="font-semibold leading-relaxed text-white">{{ alamatLengkap }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><p class="dash-label">Kabupaten</p><p class="font-semibold text-white">{{ profile.kabupaten || '-' }}</p></div>
                                <div><p class="dash-label">Provinsi</p><p class="font-semibold text-white">{{ profile.provinsi || '-' }}</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KANAN -->
            <div class="space-y-4 xl:space-y-6">
                <!-- KONTAK -->
                <div class="dash-card overflow-hidden">
                    <div class="border-b border-white/5 px-4 py-4 xl:px-6 xl:py-5">
                        <h3 class="font-display text-base font-bold text-white xl:text-lg">Kontak</h3>
                        <p class="mt-1 text-xs text-white/40 xl:text-sm">Informasi kontak</p>
                    </div>
                    <div class="space-y-4 p-4 xl:space-y-5 xl:p-6">
                        <div class="flex items-start gap-4">
                            <div class="rounded-xl bg-cyan-300/10 p-3 text-cyan-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-white/40">Email</p>
                                <p class="truncate font-semibold text-white">{{ profile.email || '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="rounded-xl bg-emerald-300/10 p-3 text-emerald-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div>
                                <p class="text-xs text-white/40">No. Handphone</p>
                                <p class="font-semibold text-white">{{ profile.nomor_telepon || '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PROGRESS STUDI (tanpa kolom baru di DB) -->
                <div class="dash-card overflow-hidden">
                    <div class="border-b border-white/5 px-4 py-4 xl:px-6 xl:py-5">
                        <h3 class="font-display text-base font-bold text-white xl:text-lg">Progress Studi</h3>
                        <p class="mt-1 text-xs text-white/40 xl:text-sm">Estimasi semester {{ profile.semester ?? '-' }} dari 8</p>
                    </div>
                    <div class="p-4 xl:p-6">
                        <div class="mb-3 flex items-end justify-between">
                            <div>
                                <p class="font-display text-3xl font-bold text-cyan-300">{{ progressPersen }}%</p>
                                <p class="text-xs text-white/40">Perjalanan studi</p>
                            </div>
                            <svg class="h-6 w-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                        <div class="progress-bar"><span :style="{ width: progressPersen + '%' }"></span></div>
                        <div class="mt-3 flex justify-between font-mono text-xs text-white/30">
                            <span>Semester {{ profile.semester ?? '-' }}</span>
                            <span>Target 8</span>
                        </div>
                    </div>
                </div>

                <!-- KATA MOTIVASI -->
                <div class="dash-card space-y-2 p-4 xl:space-y-3 xl:p-6">
                    <h3 class="font-display text-base font-bold text-white xl:text-lg">Kata Motivasi</h3>
                    <p v-if="profile.kata_motivasi" class="text-xs italic leading-relaxed text-white/70 xl:text-sm">"{{ profile.kata_motivasi }}"</p>
                    <p v-else class="text-xs text-white/30 xl:text-sm">Belum ada kata motivasi.</p>
                </div>

                <!-- PRESTASI -->
                <div class="dash-card space-y-2 p-4 xl:space-y-3 xl:p-6">
                    <h3 class="font-display text-base font-bold text-white xl:text-lg">
                        Prestasi
                        <span class="ml-1 font-mono text-[10px] font-normal text-cyan-300 xl:text-xs">{{ prestasiList.length }} point</span>
                    </h3>
                    <ol v-if="prestasiList.length > 0" class="space-y-2.5">
                        <li v-for="(point, idx) in prestasiList" :key="idx" class="flex items-start gap-2.5 rounded-xl border border-white/5 bg-white/[0.02] p-3 text-sm text-white/80">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-300/10 font-mono text-xs font-bold text-cyan-300">{{ idx + 1 }}</span>
                            <span>{{ point }}</span>
                        </li>
                    </ol>
                    <p v-else class="text-sm text-white/30">Belum ada prestasi.</p>
                    <p v-if="prestasiNote" class="text-xs text-white/30">{{ prestasiNote }}</p>
                </div>
            </div>
        </section>
    </div>
    <Modal
    :show="showModal"
    title="Edit Profil"
    subtitle="Perbarui informasi profil kamu"
    eyebrow="SAINTEK.OS // PROFIL"
    size="xl"
    @close="closeModal"
    @update:show="showModal = $event"
>
    <EditProfil
        :profile="profile"
        :canEditPrestasi="canEditPrestasi"
        @success="closeModal"
        @cancel="closeModal"
    />
</Modal>
</template>