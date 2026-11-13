<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, watch } from 'vue'

const props = defineProps({
    struktural: {
        type: Object,
        default: null,
    },
    users: {
        type: Array,
        default: () => [],
    },
    departemens: {
        type: Array,
        default: () => [],
    },
    periods: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['success', 'cancel'])

const isEdit = () => !!props.struktural

const defaultPeriode = () => {
    const now = new Date()
    const year = now.getFullYear()
    return now.getMonth() >= 8 ? `${year}/${year + 1}` : `${year - 1}/${year}`
}

const emptyForm = () => ({
    user_id: '',
    departemen_id: '',
    jabatan: '',
    periode: props.periods?.[0] || defaultPeriode(),
    urutan: 0,
})

const formFrom = (s) => s
    ? {
        user_id: s.user_id ?? '',
        departemen_id: s.departemen_id ?? '',
        jabatan: s.jabatan ?? '',
        periode: s.periode ?? '',
        urutan: s.urutan ?? 0,
    }
    : emptyForm()

const form = useForm(formFrom(props.struktural))

const jabatanSuggestions = computed(() => [
    'Ketua BEM',
    'Wakil Ketua BEM',
    'Sekretaris',
    'Bendahara',
    'Kepala Departemen',
    'Wakil Kepala Departemen',
    'Staf',
])

watch(() => props.struktural, (s) => {
    form.clearErrors()
    Object.assign(form, formFrom(s))
}, { immediate: true })

const submit = () => {
    if (isEdit()) {
        form.put(`/struktural/${props.struktural.id}`, {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    } else {
        form.post('/struktural', {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="dash-label">Pengurus *</label>
                <select v-model="form.user_id" class="dash-input cursor-pointer" required>
                    <option value="">— Pilih user —</option>
                    <option v-for="u in users" :key="u.id" :value="u.id">
                        {{ u.name }} · {{ u.nim }} · {{ u.prodi }}
                    </option>
                </select>
                <p class="dash-hint">Data nama, foto, NIM, dan prodi otomatis diambil dari akun user.</p>
                <p v-if="form.errors.user_id" class="dash-error">{{ form.errors.user_id }}</p>
            </div>

            <div>
                <label class="dash-label">Jabatan *</label>
                <input
                    v-model="form.jabatan"
                    list="jabatan-options"
                    type="text"
                    class="dash-input"
                    placeholder="cth: Ketua BEM"
                    maxlength="100"
                    required
                />
                <datalist id="jabatan-options">
                    <option v-for="j in jabatanSuggestions" :key="j" :value="j" />
                </datalist>
                <p v-if="form.errors.jabatan" class="dash-error">{{ form.errors.jabatan }}</p>
            </div>

            <div>
                <label class="dash-label">Unit / Departemen</label>
                <select v-model="form.departemen_id" class="dash-input cursor-pointer">
                    <option value="">Pengurus Harian (PH)</option>
                    <option v-for="d in departemens" :key="d.id" :value="d.id">
                        {{ d.icon }} {{ d.nama }}
                    </option>
                </select>
                <p class="dash-hint">Pilih PH bila jabatan ini bukan bagian dari departemen.</p>
                <p v-if="form.errors.departemen_id" class="dash-error">{{ form.errors.departemen_id }}</p>
            </div>

            <div>
                <label class="dash-label">Periode *</label>
                <input
                    v-model="form.periode"
                    list="periode-options"
                    type="text"
                    class="dash-input font-mono"
                    placeholder="cth: 2026/2027"
                    maxlength="20"
                    required
                />
                <datalist id="periode-options">
                    <option v-for="p in periods" :key="p" :value="p" />
                </datalist>
                <p v-if="form.errors.periode" class="dash-error">{{ form.errors.periode }}</p>
            </div>

            <div>
                <label class="dash-label">Urutan Tampil</label>
                <input
                    v-model.number="form.urutan"
                    type="number"
                    min="0"
                    max="65535"
                    class="dash-input"
                />
                <p class="dash-hint">Angka lebih kecil tampil lebih dulu di halaman publik.</p>
                <p v-if="form.errors.urutan" class="dash-error">{{ form.errors.urutan }}</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
            <button type="button" @click="emit('cancel')" class="dash-btn-ghost">
                Batal
            </button>
            <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                <span v-if="form.processing">Menyimpan...</span>
                <span v-else>{{ isEdit() ? 'Simpan Perubahan' : 'Simpan Struktural' }}</span>
            </button>
        </div>
    </form>
</template>
