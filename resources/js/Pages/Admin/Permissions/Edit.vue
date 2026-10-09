<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    permission: Object, // {id, name, description}
    user: Object,
})

const form = useForm({ description: props.permission.description || '' })

const submit = () => {
    form.put(`/permissions/${props.permission.id}`)
}
</script>

<template>
    <ModalPage
        :title="`Ubah ${permission.name}`"
        subtitle="Nama dikunci karena dipakai middleware — yang bisa diubah hanya deskripsi"
        eyebrow="SAINTEK.OS // ACCESS"
        size="md"
        backHref="/permissions"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <div>
                    <label class="dash-label">Nama permission</label>
                    <input :value="permission.name" type="text" disabled class="dash-input font-mono opacity-50" />
                </div>
                <div>
                    <label class="dash-label">Deskripsi</label>
                    <textarea v-model="form.description" rows="3" maxlength="255" class="dash-input" placeholder="Permission ini untuk apa..."></textarea>
                    <p v-if="form.errors.description" class="dash-error">{{ form.errors.description }}</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/permissions" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
            </div>
        </form>
    </ModalPage>
</template>
