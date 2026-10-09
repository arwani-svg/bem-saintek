<script setup>
import CountUp from '@/components/ui/CountUp.vue'
import Reveal from '@/components/ui/Reveal.vue'

const stats = [
    {
        value: '500+',
        label: 'Mahasiswa Aktif',
        description: 'Terdaftar sebagai anggota BEM SAINTEK'
    },
    {
        value: '5',
        label: 'Departemen',
        description: 'Organisasi yang terstruktur dan profesional'
    },
    {
        value: '12',
        label: 'Program Kerja',
        description: 'Program unggulan tahun ini'
    },
    {
        value: '95%',
        label: 'Tingkat Kepuasan',
        description: 'Dari survey mahasiswa'
    },
]
</script>

<template>
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <Reveal variant="zoom">
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-8 md:p-12 lg:p-16">
                    <Reveal variant="fade">
                        <div class="text-center mb-12">
                            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                                Capaian Kami
                            </h2>
                            <p class="text-blue-100 max-w-2xl mx-auto">
                                Berbagai pencapaian yang telah diraih oleh BEM SAINTEK
                                dalam melayani dan mengembangkan mahasiswa.
                            </p>
                        </div>
                    </Reveal>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                        <Reveal
                            v-for="(stat, index) in stats"
                            :key="index"
                            variant="up"
                            :delay="index * 130"
                            class="text-center"
                        >
                            <div class="text-4xl sm:text-5xl font-bold text-white mb-2">
                                <CountUp :value="stat.value" />
                            </div>
                            <div class="text-lg font-medium text-blue-100 mb-1">
                                {{ stat.label }}
                            </div>
                            <div class="text-sm text-blue-200">
                                {{ stat.description }}
                            </div>
                        </Reveal>
                    </div>

                    <div class="mt-12 pt-8 border-t border-white/20 grid md:grid-cols-3 gap-6">
                        <Reveal variant="right" :delay="0">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white font-medium">Terakreditasi</div>
                                    <div class="text-blue-200 text-sm">Organisasi resmi kampus</div>
                                </div>
                            </div>
                        </Reveal>

                        <Reveal variant="up" :delay="120">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white font-medium">Berprestasi</div>
                                    <div class="text-blue-200 text-sm">Juara nasional dan internasional</div>
                                </div>
                            </div>
                        </Reveal>

                        <Reveal variant="left" :delay="240">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white font-medium">Nasional</div>
                                    <div class="text-blue-200 text-sm">Jangkauan seluruh Indonesia</div>
                                </div>
                            </div>
                        </Reveal>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>
</template>
