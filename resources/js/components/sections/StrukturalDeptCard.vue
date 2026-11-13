<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
    departemen: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        default: 0,
    },
    periode: {
        type: String,
        default: '',
    },
})

const isOpen = ref(false)

const kode = computed(() => String(props.index + 1).padStart(2, '0'))

const pengurus = computed(() => [...(props.departemen.pengurus ?? [])]
    .sort((a, b) => (a.urutan ?? 0) - (b.urutan ?? 0)))

const head = computed(() => pengurus.value.find((p) => /kepala|ketua/i.test(p.jabatan ?? '')) ?? pengurus.value[0])

const members = computed(() => pengurus.value.filter((p) => p.id !== head.value?.id))

const initials = computed(() => (head.value?.user?.name || '?')
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part.charAt(0).toUpperCase())
    .join(''))
</script>

<template>
    <article class="struct-frame">
        <div class="struct-frame-inner flex h-full flex-col">
            <div class="flex items-center justify-between px-5 pb-3 pt-4">
                <span class="font-mono text-[10px] tracking-[.25em] text-cyan-300">
                    {{ departemen.icon || '◆' }} // {{ kode }}
                </span>
                <span class="font-mono text-[10px] text-white/25">{{ periode || '—' }}</span>
            </div>

            <div class="struct-photo-wrap mx-4 rounded-xl">
                <img
                    v-if="head?.user?.foto_url"
                    :src="head.user.foto_url"
                    :alt="`Foto ${head.user.name}`"
                    loading="lazy"
                    class="struct-photo struct-photo--dept"
                />
                <div v-else class="struct-photo-fallback struct-photo-fallback--dept">
                    <span class="font-display text-5xl font-black text-white/15">{{ initials }}</span>
                </div>

                <div class="struct-shine"></div>
                <div class="struct-photo-fade"></div>

                <span
                    v-if="head?.jabatan"
                    class="absolute bottom-3 left-1/2 z-[2] max-w-[90%] -translate-x-1/2 truncate whitespace-nowrap rounded-full border border-white/15 bg-black/50 px-3 py-1 font-mono text-[9px] tracking-[.25em] text-cyan-300 backdrop-blur"
                >
                    {{ head.jabatan }}
                </span>
            </div>

            <div class="flex flex-1 flex-col p-5 text-center">
                <h3 class="font-display text-lg font-bold">{{ departemen.nama }}</h3>
                <p v-if="departemen.kepanjangan" class="mt-1 font-mono text-[9px] tracking-[.12em] text-white/30">
                    {{ departemen.kepanjangan }}
                </p>
                <p class="mt-2 text-sm text-white/60">{{ head?.user?.name || 'Belum diisi' }}</p>
                <p class="mt-0.5 text-xs text-cyan-300/70">{{ head?.user?.prodi || '—' }}</p>

                <div class="mt-auto pt-4">
                    <button
                        v-if="members.length > 0"
                        type="button"
                        class="w-full rounded-xl border border-white/10 py-2.5 font-mono text-[11px] tracking-[.15em] text-white/50 transition hover:border-cyan-300/40 hover:text-white"
                        :aria-expanded="isOpen"
                        @click="isOpen = !isOpen"
                    >
                        {{ isOpen ? 'ANGGOTA −' : 'ANGGOTA +' }}
                    </button>
                    <div v-else class="rounded-xl border border-white/10 py-2.5 font-mono text-[11px] tracking-[.15em] text-white/35">
                        {{ pengurus.length }} PENGURUS
                    </div>

                    <div class="struct-members" :class="{ open: isOpen }">
                        <div class="struct-members-inner">
                            <div class="mt-2 rounded-xl bg-black/20 px-4 py-2 text-left">
                                <div
                                    v-for="member in members"
                                    :key="member.id"
                                    class="border-b border-white/10 py-2 last:border-0"
                                >
                                    <p class="text-sm text-white/70">{{ member.user?.name || '-' }}</p>
                                    <p class="mt-0.5 font-mono text-[9px] tracking-[.08em] text-white/35">
                                        {{ member.jabatan }} · {{ member.user?.prodi || '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</template>
