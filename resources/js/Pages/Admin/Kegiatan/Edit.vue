<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    kegiatan: Object,
    user: Object,
})

const form = useForm({
    nama: props.kegiatan.nama,
    tanggal: String(props.kegiatan.tanggal).substring(0, 10),
    waktu_mulai: props.kegiatan.waktu_mulai || '',
    lokasi: props.kegiatan.lokasi || '',
    poin_alpa: props.kegiatan.poin_alpa,
    deskripsi: props.kegiatan.deskripsi || '',
    status: props.kegiatan.status,
})

const submit = () => {
    form.poin_alpa = Number(form.poin_alpa || 0)
    form.put(`/kegiatan/${props.kegiatan.id}`)
}
</script>

<template>
    <ModalPage
        title="Edit Kegiatan"
        subtitle="Perubahan poin alpa berlaku untuk rekap"
        eyebrow="SAINTEK.OS // ABSENSI"
        size="lg"
        :backHref="`/kegiatan/${kegiatan.id}`"
    >
        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label class="dash-label">Nama Kegiatan *</label>
                <input v-model="form.nama" type="text" maxlength="150" class="dash-input" required />
                <p v-if="form.errors.nama" class="dash-error">{{ form.errors.nama }}</p>
            </div>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="dash-label">Tanggal *</label>
                    <input v-model="form.tanggal" type="date" class="dash-input" required />
                </div>
                <div>
                    <label class="dash-label">Jam mulai</label>
                    <input v-model="form.waktu_mulai" type="time" class="dash-input" />
                </div>
                <div>
                    <label class="dash-label">Lokasi</label>
                    <input v-model="form.lokasi" type="text" maxlength="150" class="dash-input" />
                </div>
                <div>
                    <label class="dash-label">Poin alpa *</label>
                    <input v-model="form.poin_alpa" type="number" min="0" max="1000" class="dash-input font-mono" />
                </div>
            </div>
            <div>
                <label class="dash-label">Status</label>
                <select v-model="form.status" class="dash-input cursor-pointer">
                    <option value="terjadwal">Terjadwal</option>
                    <option value="berlangsung">Berlangsung</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="dash-label">Deskripsi</label>
                <textarea v-model="form.deskripsi" rows="2" maxlength="1000" class="dash-input"></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link :href="`/kegiatan/${kegiatan.id}`" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </ModalPage>
</template>
