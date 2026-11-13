<script setup>
defineProps({
    pengurus: {
        type: Object,
        required: true,
    },
})

const initials = (name) => (name || '?')
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part.charAt(0).toUpperCase())
    .join('')
</script>

<template>
    <article class="struct-frame">
        <div class="struct-frame-inner">
            <div class="struct-photo-wrap">
                <img
                    v-if="pengurus.user?.foto_url"
                    :src="pengurus.user.foto_url"
                    :alt="`Foto ${pengurus.user?.name}`"
                    loading="lazy"
                    class="struct-photo"
                />
                <div v-else class="struct-photo-fallback">
                    <span class="font-display text-6xl font-black text-white/15">{{ initials(pengurus.user?.name) }}</span>
                </div>

                <div class="struct-shine"></div>
                <div class="struct-photo-fade"></div>

                <span class="absolute bottom-3 left-1/2 z-[2] max-w-[90%] -translate-x-1/2 truncate whitespace-nowrap rounded-full border border-white/15 bg-black/55 px-3 py-1 font-mono text-[9px] tracking-[.22em] text-cyan-300 backdrop-blur">
                    {{ pengurus.jabatan }}
                </span>
            </div>

            <div class="p-4 text-center">
                <h3 class="font-display font-bold leading-snug text-white">{{ pengurus.user?.name || '-' }}</h3>
                <p class="mt-1 text-xs font-medium text-cyan-300/80">{{ pengurus.user?.prodi || '-' }}</p>
                <p class="mt-1 font-mono text-[10px] tracking-widest text-white/30">{{ pengurus.user?.nim || '-' }}</p>
            </div>
        </div>
    </article>
</template>
