<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    iuran: Object,
    user: Object,
})

const form = useForm({
    nama: props.iuran.nama,
    nominal: String(props.iuran.nominal),
    jatuh_tempo: props.iuran.jatuh_tempo ? String(props.iuran.jatuh_tempo).substring(0, 10) : '',
    deskripsi: props.iuran.deskripsi || '',
    status: props.iuran.status,
})

const rupiahPreview = computed(() => {
    const n = Number(String(form.nominal).replace(/[^0-9]/g, '') || 0)
    return 'Rp ' + n.toLocaleString('id-ID')
})

const submit = () => {
    form.nominal = Number(String(form.nominal).replace(/[^0-9]/g, '') || 0)
    form.put(`/iuran/${props.iuran.id}`)
}
</script>

<template>
    <ModalPage
        title="Edit Iuran"
        subtitle="Nominal baru hanya berlaku untuk peserta baru"
        eyebrow="SAINTEK.OS // MANAJEMEN IURAN"
        size="lg"
        :backHref="`/iuran/${iuran.id}`"
    >
        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label class="dash-label">Nama Iuran *</label>
                <input v-model="form.nama" type="text" maxlength="150" class="dash-input" required />
                <p v-if="form.errors.nama" class="dash-error">{{ form.errors.nama }}</p>
            </div>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="dash-label">Nominal per orang (Rp) *</label>
                    <input v-model="form.nominal" type="text" inputmode="numeric" class="dash-input font-mono" required />
                    <p class="mt-1 font-mono text-xs text-cyan-300">{{ rupiahPreview }}</p>
                </div>
                <div>
                    <label class="dash-label">Jatuh tempo</label>
                    <input v-model="form.jatuh_tempo" type="date" class="dash-input" />
                </div>
            </div>
            <div>
                <label class="dash-label">Status</label>
                <select v-model="form.status" class="dash-input cursor-pointer">
                    <option value="aktif">Aktif — masih ditagih</option>
                    <option value="selesai">Selesai — stop penagihan</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="dash-label">Deskripsi</label>
                <textarea v-model="form.deskripsi" rows="2" maxlength="1000" class="dash-input"></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link :href="`/iuran/${iuran.id}`" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </ModalPage>
</template>
