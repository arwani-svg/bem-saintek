<!-- <script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
    statusList: Array,
    rolesList: Array,
})

const emit = defineEmits(['success', 'cancel'])

const form = useForm({
    name: props.user?.name || '',
    nim: props.user?.nim || '',
    role: props.user?.role || 'mahasiswa',
    status: props.user?.status || 'aktif',
    prodi: props.user?.prodi || '',
    kelas: props.user?.kelas || '',
})

watch(() => props.user, (u) => {
    form.reset()
    form.clearErrors()
    form.name = u?.name || ''
    form.nim = u?.nim || ''
    form.role = u?.role || 'mahasiswa'
    form.status = u?.status || 'aktif'
    form.prodi = u?.prodi || ''
    form.kelas = u?.kelas || ''
})

const submit = () => {
    if (!props.user) return
    form.put(`/users/${props.user.id}`, {
        preserveScroll: true,
        onSuccess: () => emit('success'),
    })
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Nama Lengkap *</label>
                    <input v-model="form.name" type="text" class="dash-input" required />
                    <p v-if="form.errors.name" class="dash-error">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="dash-label">NIM *</label>
                    <input v-model="form.nim" type="text" class="dash-input" required />
                    <p v-if="form.errors.nim" class="dash-error">{{ form.errors.nim }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Role *</label>
                    <select v-model="form.role" class="dash-input cursor-pointer capitalize">
                        <option v-for="r in rolesList ?? ['mahasiswa', 'pengurus']" :key="r" :value="r">{{ r }}</option>
                    </select>
                    <p v-if="form.errors.role" class="dash-error">{{ form.errors.role }}</p>
                </div>
                <div>
                    <label class="dash-label">Status *</label>
                    <select v-model="form.status" class="dash-input cursor-pointer capitalize">
                        <option v-for="s in statusList ?? ['aktif', 'cuti', 'DO', 'keluar', 'lulus']" :key="s" :value="s">{{ s }}</option>
                    </select>
                    <p v-if="form.errors.status" class="dash-error">{{ form.errors.status }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Program Studi</label>
                    <input v-model="form.prodi" type="text" class="dash-input" />
                    <p v-if="form.errors.prodi" class="dash-error">{{ form.errors.prodi }}</p>
                </div>
                <div>
                    <label class="dash-label">Kelas</label>
                    <input v-model="form.kelas" type="text" class="dash-input" />
                    <p v-if="form.errors.kelas" class="dash-error">{{ form.errors.kelas }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
            <button type="button" @click="emit('cancel')" class="dash-btn-ghost">Batal</button>
            <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                <span v-if="form.processing">Menyimpan...</span>
                <span v-else>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</template> -->