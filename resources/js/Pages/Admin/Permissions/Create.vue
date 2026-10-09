<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

defineProps({
    user: Object,
})

const form = useForm({ name: '', description: '' })

const submit = () => {
    form.post('/permissions')
}
</script>

<template>
    <ModalPage
        title="Tambah Permission"
        subtitle="Daftarkan hak akses baru untuk ditempel ke role"
        eyebrow="SAINTEK.OS // ACCESS"
        size="md"
        backHref="/permissions"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <div>
                    <label class="dash-label">Nama permission * <span class="font-normal text-white/30">(huruf kecil, angka, strip)</span></label>
                    <input v-model="form.name" type="text" class="dash-input font-mono" placeholder="kelola-sesuatu" required />
                    <p v-if="form.errors.name" class="dash-error">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="dash-label">Deskripsi</label>
                    <textarea v-model="form.description" rows="2" maxlength="255" class="dash-input" placeholder="Permission ini untuk apa..."></textarea>
                    <p v-if="form.errors.description" class="dash-error">{{ form.errors.description }}</p>
                </div>
                <p class="text-xs leading-relaxed text-white/30">Setelah dibuat, tempelkan ke role lewat halaman <Link href="/roles" class="text-cyan-300 hover:underline">Daftar Role → Atur</Link>.</p>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/permissions" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    {{ form.processing ? 'Menyimpan...' : 'Buat Permission' }}
                </button>
            </div>
        </form>
    </ModalPage>
</template>
