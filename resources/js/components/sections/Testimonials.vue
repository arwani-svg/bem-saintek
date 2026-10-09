<script setup>
import Reveal from '@/components/ui/Reveal.vue'

const cardVariants = ['right', 'up', 'left']

const testimonials = [
    {
        name: 'Ahmad Rizki',
        role: 'Ketua BEM SAINTEK 2023',
        quote: 'BEM SAINTEK memberikan saya pengalaman berharga dalam memimpin dan mengorganisir berbagai kegiatan. Sangat recommended!',
        avatar: 'AR'
    },
    {
        name: 'Sarah Putri',
        role: 'Ketua Departemen PSDM',
        quote: 'Bergabung dengan BEM SAINTEK mengajarkan saya tentang kepemimpinan, kerja tim, dan bagaimana menjadi pribadi yang lebih baik.',
        avatar: 'SP'
    },
    {
        name: 'Muhammad Fadil',
        role: 'Sekretaris Jenderal',
        quote: 'Organisasi ini benar-benar membantu saya mengembangkan soft skill yang dibutuhkan di dunia kerja nanti.',
        avatar: 'MF'
    },
]
</script>

<template>
    <section id="berita" class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <Reveal variant="up">
                <div class="text-center mb-16">
                    <span class="inline-block px-4 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-full mb-4">
                        Testimoni
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Apa Kata Mereka?
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Cerita dan pengalaman dari para anggota dan alumni BEM SAINTEK
                        yang telah merasakan manfaat bergabung dengan organisasi ini.
                    </p>
                </div>
            </Reveal>

            <div class="grid md:grid-cols-3 gap-8">
                <Reveal
                    v-for="(testimonial, index) in testimonials"
                    :key="index"
                    :variant="cardVariants[index % cardVariants.length]"
                    :delay="index * 130"
                >
                    <div
                        class="h-full bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-700"
                    >
                    <div class="flex items-center gap-1 mb-4">
                        <svg
                            v-for="i in 5"
                            :key="i"
                            class="w-5 h-5 text-yellow-400"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 mb-6 italic leading-relaxed">
                        "{{ testimonial.quote }}"
                    </p>

                    <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ testimonial.avatar }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ testimonial.name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ testimonial.role }}</div>
                        </div>
                    </div>
                    </div>
                </Reveal>
            </div>
        </div>
    </section>
</template>
