
<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
    permission: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['success', 'cancel'])

const isEdit = !!props.permission

const form = useForm({
    name: props.permission?.name || '',
    description: props.permission?.description || '',
})

const submit = () => {
    const options = {
        onSuccess: () => emit('success'),
    }

    if (isEdit) {
        form.put(`/permissions/${props.permission.id}`, options)
    } else {
        form.post('/permissions', options)
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-6">

            <!-- NAMA PERMISSION -->
            <div>
                <label class="dash-label">
                    Nama permission *
                    <span class="font-normal text-white/30">
                        (huruf kecil, angka, strip)
                    </span>
                </label>

                <input
                    v-model="form.name"
                    type="text"
                    class="dash-input font-mono"
                    placeholder="kelola-sesuatu"
                    :disabled="isEdit"
                    required
                />

                <p v-if="form.errors.name" class="dash-error">
                    {{ form.errors.name }}
                </p>

                <p v-if="isEdit" class="mt-2 text-xs text-white/30">
                    Nama permission dikunci karena digunakan middleware.
                </p>
            </div>

            <!-- DESKRIPSI -->
            <div>
                <label class="dash-label">Deskripsi</label>

                <textarea
                    v-model="form.description"
                    rows="3"
                    maxlength="255"
                    class="dash-input"
                    placeholder="Permission ini untuk apa..."
                ></textarea>

                <p v-if="form.errors.description" class="dash-error">
                    {{ form.errors.description }}
                </p>
            </div>

            <p v-if="!isEdit" class="text-xs leading-relaxed text-white/30">
                Setelah dibuat, permission bisa ditempelkan ke role melalui halaman
                <Link href="/roles" class="text-cyan-300 hover:underline">
                    Daftar Role → Atur
                </Link>.
            </p>
        </div>

        <!-- BUTTON -->
        <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
            <button
                type="button"
                class="dash-btn-ghost"
                @click="emit('cancel')"
            >
                Batal
            </button>

            <button
                type="submit"
                :disabled="form.processing"
                class="dash-btn-primary"
            >
                {{
                    form.processing
                        ? 'Menyimpan...'
                        : isEdit
                            ? 'Simpan Perubahan'
                            : 'Buat Permission'
                }}
            </button>
        </div>
    </form>
</template>
