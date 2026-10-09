<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

defineProps({
    user: Object,
})

const form = useForm({
    nama: '',
    deskripsi: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    is_open: true,
})

const submit = () => {
    form.post('/admin/gelombang')
}
</script>

<template>
    <ModalPage
        title="Buat Periode"
        subtitle="Buka pendaftaran dari tanggal ke tanggal"
        eyebrow="SAINTEK.OS // GELOMBANG"
        size="md"
        backHref="/admin/pendaftaran?tab=periode"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <div>
                    <label class="dash-label">Nama Periode *</label>
                    <input v-model="form.nama" type="text" class="dash-input" placeholder="cth: Gelombang 1 — 2026" required />
                    <p v-if="form.errors.nama" class="dash-error">{{ form.errors.nama }}</p>
                </div>
                <div>
                    <label class="dash-label">Deskripsi</label>
                    <textarea v-model="form.deskripsi" rows="2" class="dash-input" placeholder="Catatan singkat untuk pendaftar..."></textarea>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Tanggal Mulai *</label>
                        <input v-model="form.tanggal_mulai" type="date" class="dash-input" required />
                        <p v-if="form.errors.tanggal_mulai" class="dash-error">{{ form.errors.tanggal_mulai }}</p>
                    </div>
                    <div>
                        <label class="dash-label">Tanggal Selesai *</label>
                        <input v-model="form.tanggal_selesai" type="date" class="dash-input" required />
                        <p v-if="form.errors.tanggal_selesai" class="dash-error">{{ form.errors.tanggal_selesai }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input v-model="form.is_open" type="checkbox" id="is_open" class="dash-check" />
                    <label for="is_open" class="cursor-pointer text-sm font-medium text-white/70">Langsung buka periode ini (periode lain otomatis tertutup)</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/admin/pendaftaran?tab=periode" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan Periode</span>
                </button>
            </div>
        </form>
    </ModalPage>
</template>
