<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    pendaftaran: Object,
    prodiList: Array,
    user: Object,
})

const asList = (list, raw) => {
    if (Array.isArray(list) && list.length > 0) return [...list]
    if (typeof raw === 'string' && raw.trim() !== '') return raw.trim().split(/\r\n|\r|\n/).map((v) => v.trim()).filter((v) => v !== '')
    return ['']
}

const mk = (l, r) => {
    const a = asList(l, r)
    return a.length ? a : ['']
}

const form = useForm({
    nama: props.pendaftaran.nama,
    nim: props.pendaftaran.nim,
    prodi: props.pendaftaran.prodi,
    semester: props.pendaftaran.semester,
    tempat_lahir: props.pendaftaran.tempat_lahir || '',
    tanggal_lahir: props.pendaftaran.tanggal_lahir ? String(props.pendaftaran.tanggal_lahir).substring(0, 10) : '',
    asrama: props.pendaftaran.asrama || '',
    email: props.pendaftaran.email || '',
    tujuan: props.pendaftaran.tujuan || '',
    riwayat_sekolah: mk(props.pendaftaran.riwayat_sekolah_list, props.pendaftaran.riwayat_sekolah),
    riwayat_madrasah: mk(props.pendaftaran.riwayat_madrasah_list, props.pendaftaran.riwayat_madrasah),
    riwayat_organisasi: mk(props.pendaftaran.riwayat_organisasi_list, props.pendaftaran.riwayat_organisasi),
})

const addItem = (key) => {
    if (form[key].length < 30) form[key].push('')
}

const removeItem = (key, index) => {
    form[key].splice(index, 1)
    if (form[key].length === 0) form[key].push('')
}

const clean = (arr) => (arr ?? []).map((v) => (v ?? '').trim()).filter((v) => v !== '')

const submit = () => {
    form.riwayat_sekolah = clean(form.riwayat_sekolah)
    form.riwayat_madrasah = clean(form.riwayat_madrasah)
    form.riwayat_organisasi = clean(form.riwayat_organisasi)
    form.put(`/pendaftaran/${props.pendaftaran.id}`)
}
</script>

<template>
    <ModalPage
        title="Edit Pendaftaran"
        :subtitle="`Perbarui data ${pendaftaran.nama}`"
        eyebrow="SAINTEK.OS // PENDAFTARAN"
        size="xl"
        :backHref="`/pendaftaran/${pendaftaran.id}`"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <h3 class="font-display text-lg font-bold text-white">Data Diri</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Nama Lengkap *</label>
                        <input v-model="form.nama" type="text" class="dash-input" required />
                        <p v-if="form.errors.nama" class="dash-error">{{ form.errors.nama }}</p>
                    </div>
                    <div>
                        <label class="dash-label">NPM / NIM *</label>
                        <input v-model="form.nim" type="text" class="dash-input font-mono" required />
                        <p v-if="form.errors.nim" class="dash-error">{{ form.errors.nim }}</p>
                    </div>
                    <div>
                        <label class="dash-label">Program Studi *</label>
                        <select v-model="form.prodi" class="dash-input cursor-pointer" required>
                            <option v-for="p in prodiList" :key="p" :value="p">{{ p }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="dash-label">Semester * (1–14)</label>
                        <input v-model="form.semester" type="number" min="1" max="14" class="dash-input" required />
                    </div>
                    <div>
                        <label class="dash-label">Tempat Lahir</label>
                        <input v-model="form.tempat_lahir" type="text" class="dash-input" />
                    </div>
                    <div>
                        <label class="dash-label">Tanggal Lahir</label>
                        <input v-model="form.tanggal_lahir" type="date" class="dash-input" />
                    </div>
                    <div>
                        <label class="dash-label">Asrama</label>
                        <input v-model="form.asrama" type="text" class="dash-input" />
                    </div>
                    <div>
                        <label class="dash-label">Email</label>
                        <input v-model="form.email" type="email" class="dash-input" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="dash-label">Tujuan Gabung BEM</label>
                        <textarea v-model="form.tujuan" rows="3" class="dash-input"></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-6 border-t border-white/5 pt-6">
                <h3 class="font-display text-lg font-bold text-white">Riwayat (point dinamis)</h3>
                <div v-for="group in [
                    { key: 'riwayat_sekolah', title: 'Riwayat Pendidikan Sekolah' },
                    { key: 'riwayat_madrasah', title: 'Riwayat Pendidikan Madrasah' },
                    { key: 'riwayat_organisasi', title: 'Riwayat Organisasi' },
                ]" :key="group.key">
                    <label class="dash-label">{{ group.title }}</label>
                    <div class="space-y-3">
                        <div v-for="(point, idx) in form[group.key]" :key="idx" class="flex items-start gap-2">
                            <span class="mt-2.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-300/10 font-mono text-xs font-bold text-cyan-300">{{ idx + 1 }}</span>
                            <input v-model="form[group.key][idx]" type="text" maxlength="255" class="dash-input" />
                            <button v-if="form[group.key].length > 1" type="button" @click="removeItem(group.key, idx)" class="mt-1 shrink-0 rounded-xl border border-red-400/20 p-2 text-red-300 transition hover:bg-red-400/10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                        <button v-if="form[group.key].length < 30" type="button" @click="addItem(group.key)" class="dash-btn-ghost !py-2 text-sm">+ Tambah</button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link :href="`/pendaftaran/${pendaftaran.id}`" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Perbarui</span>
                </button>
            </div>
        </form>
    </ModalPage>
</template>
