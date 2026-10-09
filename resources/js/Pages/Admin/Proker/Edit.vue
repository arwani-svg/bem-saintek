<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    proker: Object,
    departemens: Array,
    statusList: Object,
    warnaList: Array,
    user: Object,
})

const form = useForm({
    departemen_id: props.proker.departemen_id,
    judul: props.proker.judul,
    deskripsi: props.proker.deskripsi || '',
    kategori: props.proker.kategori || '',
    warna: props.proker.warna,
    status: props.proker.status,
    tanggal_mulai: props.proker.tanggal_mulai ? props.proker.tanggal_mulai.substring(0, 10) : '',
    tanggal_selesai: props.proker.tanggal_selesai ? props.proker.tanggal_selesai.substring(0, 10) : '',
    lokasi: props.proker.lokasi || '',
    penanggung_jawab: props.proker.penanggung_jawab || '',
    is_unggulan: props.proker.is_unggulan,
})

const submit = () => {
    form.put(`/proker/${props.proker.id}`)
}
</script>

<template>
    <ModalPage
        title="Edit Program Kerja"
        :subtitle="`Perbarui ${proker.judul}`"
        eyebrow="SAINTEK.OS // PROKER"
        size="xl"
        backHref="/proker"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <h3 class="font-display text-lg font-bold text-white">Informasi Program</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Departemen *</label>
                        <select v-model="form.departemen_id" class="dash-input cursor-pointer" required>
                            <option v-for="d in departemens" :key="d.id" :value="d.id">
                                {{ d.nama }}{{ d.kepanjangan ? ` — ${d.kepanjangan}` : '' }}
                            </option>
                        </select>
                        <p v-if="form.errors.departemen_id" class="dash-error">{{ form.errors.departemen_id }}</p>
                    </div>

                    <div>
                        <label class="dash-label">Status *</label>
                        <select v-model="form.status" class="dash-input cursor-pointer">
                            <option v-for="(label, key) in statusList" :key="key" :value="key">{{ label }}</option>
                        </select>
                        <p v-if="form.errors.status" class="dash-error">{{ form.errors.status }}</p>
                    </div>
                </div>

                <div>
                    <label class="dash-label">Judul Program *</label>
                    <input v-model="form.judul" type="text" class="dash-input" placeholder="cth: Lomba Olimpiade Sains" required />
                    <p v-if="form.errors.judul" class="dash-error">{{ form.errors.judul }}</p>
                </div>

                <div>
                    <label class="dash-label">Deskripsi</label>
                    <textarea v-model="form.deskripsi" rows="4" class="dash-input resize-none" placeholder="Ceritakan tujuan dan bentuk kegiatan proker ini..."></textarea>
                    <p v-if="form.errors.deskripsi" class="dash-error">{{ form.errors.deskripsi }}</p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Kategori</label>
                        <input v-model="form.kategori" type="text" class="dash-input" placeholder="cth: Akademik (kosong = nama departemen)" />
                        <p v-if="form.errors.kategori" class="dash-error">{{ form.errors.kategori }}</p>
                    </div>

                    <div>
                        <label class="dash-label">Warna Kartu *</label>
                        <select v-model="form.warna" class="dash-input cursor-pointer">
                            <option v-for="w in warnaList" :key="w" :value="w" class="capitalize">{{ w }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="dash-label">Tanggal Mulai</label>
                        <input v-model="form.tanggal_mulai" type="date" class="dash-input" />
                    </div>

                    <div>
                        <label class="dash-label">Tanggal Selesai</label>
                        <input v-model="form.tanggal_selesai" type="date" class="dash-input" />
                        <p v-if="form.errors.tanggal_selesai" class="dash-error">{{ form.errors.tanggal_selesai }}</p>
                    </div>

                    <div>
                        <label class="dash-label">Lokasi</label>
                        <input v-model="form.lokasi" type="text" class="dash-input" placeholder="cth: Aula Fakultas" />
                    </div>

                    <div>
                        <label class="dash-label">Penanggung Jawab</label>
                        <input v-model="form.penanggung_jawab" type="text" class="dash-input" placeholder="cth: [Nama PJ]" />
                    </div>
                </div>

                <label class="flex cursor-pointer items-center gap-3 pt-1">
                    <input v-model="form.is_unggulan" type="checkbox" id="unggulan" class="dash-check" />
                    <span class="text-sm font-medium text-white/70">Tampilkan di halaman depan ⭐</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/proker" class="dash-btn-ghost">
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
