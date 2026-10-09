<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
    departemen: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['success', 'cancel'])

const isEdit = () => !!props.departemen

const form = useForm({
    nama: props.departemen?.nama || '',
    kepanjangan: props.departemen?.kepanjangan || '',
    deskripsi: props.departemen?.deskripsi || '',
    tujuan: props.departemen?.tujuan || '',
    icon: props.departemen?.icon || '🏛️',
})

// Reset isi form setiap ganti target edit / buka create baru
watch(() => props.departemen, (d) => {
    form.reset()
    form.clearErrors()
    form.nama = d?.nama || ''
    form.kepanjangan = d?.kepanjangan || ''
    form.deskripsi = d?.deskripsi || ''
    form.tujuan = d?.tujuan || ''
    form.icon = d?.icon || '🏛️'
})

const submit = () => {
    if (isEdit()) {
        form.put(`/departemen/${props.departemen.id}`, {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    } else {
        form.post('/departemen', {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Nama Singkat *</label>
                    <input
                        v-model="form.nama"
                        type="text"
                        class="dash-input"
                        placeholder="cth: PSDM"
                        required
                    />
                    <p v-if="form.errors.nama" class="dash-error">{{ form.errors.nama }}</p>
                </div>

                <div>
                    <label class="dash-label">Icon (emoji)</label>
                    <input
                        v-model="form.icon"
                        type="text"
                        maxlength="10"
                        class="dash-input text-xl"
                        placeholder="🏛️"
                    />
                    <p v-if="form.errors.icon" class="dash-error">{{ form.errors.icon }}</p>
                </div>
            </div>

            <div>
                <label class="dash-label">Kepanjangan</label>
                <input
                    v-model="form.kepanjangan"
                    type="text"
                    class="dash-input"
                    placeholder="cth: Pengembangan Sumber Daya Manusia"
                />
                <p v-if="form.errors.kepanjangan" class="dash-error">{{ form.errors.kepanjangan }}</p>
            </div>

            <div>
                <label class="dash-label">Deskripsi</label>
                <textarea
                    v-model="form.deskripsi"
                    rows="4"
                    class="dash-input resize-none"
                    placeholder="Tugas pokok dan fungsi departemen ini..."
                ></textarea>
                <p v-if="form.errors.deskripsi" class="dash-error">{{ form.errors.deskripsi }}</p>
            </div>

            <div>
                <label class="dash-label">Tujuan Departemen</label>
                <textarea
                    v-model="form.tujuan"
                    rows="4"
                    class="dash-input resize-none"
                    placeholder="Tulis satu tujuan per baris"
                ></textarea>
                <p class="dash-hint">Satu baris = satu poin tujuan di halaman publik.</p>
                <p v-if="form.errors.tujuan" class="dash-error">{{ form.errors.tujuan }}</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
            <button type="button" @click="emit('cancel')" class="dash-btn-ghost">
                Batal
            </button>
            <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                <span v-if="form.processing">Menyimpan...</span>
                <span v-else>{{ isEdit() ? 'Simpan Perubahan' : 'Simpan Departemen' }}</span>
            </button>
        </div>
    </form>
</template>
