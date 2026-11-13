<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
    gelombang: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['success', 'cancel'])

const isEdit = () => !!props.gelombang

const form = useForm({
    nama: props.gelombang?.nama || '',
    deskripsi: props.gelombang?.deskripsi || '',
    tanggal_mulai: props.gelombang?.tanggal_mulai || '',
    tanggal_selesai: props.gelombang?.tanggal_selesai || '',
    is_open: props.gelombang?.is_open ?? true,
})

// Reset isi form setiap ganti target edit / buka create baru
watch(() => props.gelombang, (g) => {
    form.reset()
    form.clearErrors()
    form.nama = g?.nama || ''
    form.deskripsi = g?.deskripsi || ''
    form.tanggal_mulai = g?.tanggal_mulai || ''
    form.tanggal_selesai = g?.tanggal_selesai || ''
    form.is_open = g?.is_open ?? true
})

const submit = () => {
    if (isEdit()) {
        form.put(`/admin/gelombang/${props.gelombang.id}`, {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    } else {
        form.post('/admin/gelombang', {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-6">
            <div>
                <label class="dash-label">Nama Periode *</label>
                <input
                    v-model="form.nama"
                    type="text"
                    class="dash-input"
                    placeholder="cth: Gelombang 1 — 2026"
                    required
                />
                <p v-if="form.errors.nama" class="dash-error">{{ form.errors.nama }}</p>
            </div>

            <div>
                <label class="dash-label">Deskripsi</label>
                <textarea
                    v-model="form.deskripsi"
                    rows="2"
                    class="dash-input resize-none"
                    placeholder="Catatan singkat untuk pendaftar..."
                ></textarea>
                <p v-if="form.errors.deskripsi" class="dash-error">{{ form.errors.deskripsi }}</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Tanggal Mulai *</label>
                    <input
                        v-model="form.tanggal_mulai"
                        type="date"
                        class="dash-input"
                        required
                    />
                    <p v-if="form.errors.tanggal_mulai" class="dash-error">{{ form.errors.tanggal_mulai }}</p>
                </div>

                <div>
                    <label class="dash-label">Tanggal Selesai *</label>
                    <input
                        v-model="form.tanggal_selesai"
                        type="date"
                        class="dash-input"
                        required
                    />
                    <p v-if="form.errors.tanggal_selesai" class="dash-error">{{ form.errors.tanggal_selesai }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input
                    v-model="form.is_open"
                    type="checkbox"
                    id="is_open"
                    class="dash-check"
                />
                <label for="is_open" class="cursor-pointer text-sm font-medium text-white/70">
                    Langsung buka periode ini (periode lain otomatis tertutup)
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
            <button type="button" @click="emit('cancel')" class="dash-btn-ghost">
                Batal
            </button>
            <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                <span v-if="form.processing">Menyimpan...</span>
                <span v-else>{{ isEdit() ? 'Simpan Perubahan' : 'Simpan Periode' }}</span>
            </button>
        </div>
    </form>
</template>