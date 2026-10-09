<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    informasi: Object,
    jenisList: Object,
    jenisArtikel: Array,
    user: Object,
})

const form = useForm({
    _method: 'put',
    jenis: props.informasi.jenis,
    judul: props.informasi.judul,
    ringkasan: props.informasi.ringkasan || '',
    konten: props.informasi.konten || '',
    gambar: null,
    hapus_gambar: false,
    tanggal_publikasi: props.informasi.tanggal_publikasi || '',
    is_published: props.informasi.is_published,
    is_unggulan: props.informasi.is_unggulan,
})

const previewUrl = ref(null)
const isArtikel = computed(() => props.jenisArtikel.includes(form.jenis))
const isGambarOnly = computed(() => !isArtikel.value)

const onFileChange = (e) => {
    const file = e.target.files[0] || null
    form.gambar = file
    previewUrl.value = file ? URL.createObjectURL(file) : null
    if (file) form.hapus_gambar = false
}

const submit = () => {
    // Pakai POST + _method=put agar upload file multipart tetap terbaca Laravel
    form.post(`/informasi/${props.informasi.id}`)
}
</script>

<template>
    <ModalPage
        title="Edit Informasi"
        :subtitle="`Perbarui ${informasi.judul}`"
        eyebrow="SAINTEK.OS // INFORMASI"
        size="xl"
        backHref="/informasi"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Jenis Informasi *</label>
                        <select v-model="form.jenis" class="dash-input cursor-pointer">
                            <option v-for="(label, key) in jenisList" :key="key" :value="key">{{ label }}</option>
                        </select>
                        <p v-if="form.errors.jenis" class="dash-error">{{ form.errors.jenis }}</p>
                    </div>

                    <div>
                        <label class="dash-label">Tanggal Publikasi</label>
                        <input v-model="form.tanggal_publikasi" type="date" class="dash-input" />
                        <p v-if="form.errors.tanggal_publikasi" class="dash-error">{{ form.errors.tanggal_publikasi }}</p>
                    </div>
                </div>

                <div class="rounded-xl border p-3 text-sm leading-relaxed"
                    :class="isArtikel
                        ? 'border-cyan-300/20 bg-cyan-300/5 text-cyan-200/80'
                        : 'border-violet-300/20 bg-violet-300/5 text-violet-200/80'">
                    <span v-if="isArtikel">📰 Mode {{ jenisList[form.jenis] }}: isi <b>narasi / konten panjang</b> + gambar. Ringkasan dipakai sebagai cuplikan di daftar.</span>
                    <span v-else>🖼️ Mode {{ jenisList[form.jenis] }}: cukup <b>gambar + keterangan singkat</b> penjelas gambar.</span>
                </div>

                <div>
                    <label class="dash-label">Judul *</label>
                    <input v-model="form.judul" type="text" class="dash-input" required />
                    <p v-if="form.errors.judul" class="dash-error">{{ form.errors.judul }}</p>
                </div>

                <div>
                    <label class="dash-label">
                        {{ isGambarOnly ? 'Keterangan Singkat *' : 'Ringkasan / Cuplikan' }}
                    </label>
                    <textarea v-model="form.ringkasan" rows="3" class="dash-input resize-none"></textarea>
                    <p v-if="form.errors.ringkasan" class="dash-error">{{ form.errors.ringkasan }}</p>
                </div>

                <div v-if="isArtikel">
                    <label class="dash-label">Narasi / Konten Berita *</label>
                    <textarea v-model="form.konten" rows="8" class="dash-input"></textarea>
                    <p v-if="form.errors.konten" class="dash-error">{{ form.errors.konten }}</p>
                </div>

                <div>
                    <label class="dash-label">
                        Gambar {{ isGambarOnly && !informasi.gambar_url ? '*' : '' }}
                    </label>
                    <div v-if="informasi.gambar_url && !previewUrl && !form.hapus_gambar" class="mb-3">
                        <img :src="informasi.gambar_url" alt="Gambar saat ini" class="max-h-56 rounded-xl border border-white/10 object-cover" />
                        <label class="mt-2 flex cursor-pointer items-center gap-2 text-sm text-rose-300">
                            <input v-model="form.hapus_gambar" type="checkbox" class="dash-check" />
                            Hapus gambar saat ini
                        </label>
                    </div>
                    <input
                        type="file"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        @change="onFileChange"
                        class="dash-file"
                    />
                    <p class="dash-hint">Kosongkan jika tidak ingin mengganti gambar. Maks 2MB.</p>
                    <p v-if="form.errors.gambar" class="dash-error">{{ form.errors.gambar }}</p>
                    <img v-if="previewUrl" :src="previewUrl" alt="Pratinjau baru" class="mt-3 max-h-56 rounded-xl border border-white/10 object-cover" />
                </div>

                <div class="flex flex-col gap-3 pt-1">
                    <label class="flex cursor-pointer items-center gap-3">
                        <input v-model="form.is_published" type="checkbox" class="dash-check" />
                        <span class="text-sm font-medium text-white/70">Publikasikan <span class="font-normal text-white/35">(matikan untuk jadikan draft)</span></span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3">
                        <input v-model="form.is_unggulan" type="checkbox" class="dash-check" />
                        <span class="text-sm font-medium text-white/70">Jadikan sorotan ⭐</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/informasi" class="dash-btn-ghost">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </ModalPage>
</template>
