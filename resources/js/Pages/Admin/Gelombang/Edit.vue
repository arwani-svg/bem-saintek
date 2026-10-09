<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    gelombang: Object,
    user: Object,
})

const form = useForm({
    nama: props.gelombang.nama,
    deskripsi: props.gelombang.deskripsi || '',
    tanggal_mulai: props.gelombang.tanggal_mulai || '',
    tanggal_selesai: props.gelombang.tanggal_selesai || '',
    is_open: !!props.gelombang.is_open,
})

const submit = () => {
    form.put(`/admin/gelombang/${props.gelombang.id}`)
}
</script>

<template>
    <ModalPage
        title="Edit Periode"
        :subtitle="`Perbarui ${gelombang.nama}`"
        eyebrow="SAINTEK.OS // GELOMBANG"
        size="md"
        backHref="/admin/pendaftaran?tab=periode"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <div>
                    <label class="dash-label">Nama Periode *</label>
                    <input v-model="form.nama" type="text" class="dash-input" required />
                </div>
                <div>
                    <label class="dash-label">Deskripsi</label>
                    <textarea v-model="form.deskripsi" rows="2" class="dash-input"></textarea>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Tanggal Mulai *</label>
                        <input v-model="form.tanggal_mulai" type="date" class="dash-input" required />
                    </div>
                    <div>
                        <label class="dash-label">Tanggal Selesai *</label>
                        <input v-model="form.tanggal_selesai" type="date" class="dash-input" required />
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input v-model="form.is_open" type="checkbox" id="is_open" class="dash-check" />
                    <label for="is_open" class="cursor-pointer text-sm font-medium text-white/70">Buka periode ini (periode lain otomatis tertutup)</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/admin/pendaftaran?tab=periode" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Perbarui Periode</span>
                </button>
            </div>
        </form>
    </ModalPage>
</template>
