<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    pendaftaran: Object,
    prodiList: Array,
    statusList: Array,
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
    status: props.pendaftaran.status,
    catatan_seleksi: props.pendaftaran.catatan_seleksi || '',
})

const addItem = (key) => {
    if (form[key].length < 30) form[key].push('')
}

const removeItem = (key, index) => {
    form[key].splice(index, 1)
    if (form[key].length === 0) form[key].push('')
}

const clean = (arr) => (arr ?? []).map((v) => (v ?? '').trim()).filter((v) => v !== '')

const submit = (status = null) => {
    if (status) form.status = status
    form.riwayat_sekolah = clean(form.riwayat_sekolah)
    form.riwayat_madrasah = clean(form.riwayat_madrasah)
    form.riwayat_organisasi = clean(form.riwayat_organisasi)
    form.put(`/pendaftaran/${props.pendaftaran.id}`)
}
</script>

<template>
    <ModalPage
        title="Kelola Pendaftar"
        :subtitle="`Seleksi ${pendaftaran.nama}`"
        eyebrow="SAINTEK.OS // SELEKSI"
        size="xl"
        :backHref="`/pendaftaran/${pendaftaran.id}`"
    >
        <form @submit.prevent="submit()" class="space-y-6">
            <div class="space-y-6">
                <h3 class="font-display text-lg font-bold text-white">Data Pendaftar</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Nama Lengkap *</label>
                        <input v-model="form.nama" type="text" class="dash-input" required />
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
                        <label class="dash-label">Tujuan</label>
                        <textarea v-model="form.tujuan" rows="3" class="dash-input"></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-6 border-t border-white/5 pt-6">
                <h3 class="font-display text-lg font-bold text-white">Hasil Seleksi</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Status *</label>
                        <select v-model="form.status" class="dash-input cursor-pointer" required>
                            <option v-for="s in statusList" :key="s" :value="s">{{ s }}</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="dash-label">Catatan Seleksi</label>
                    <textarea v-model="form.catatan_seleksi" rows="2" class="dash-input" placeholder="Alasan diterima / ditolak..."></textarea>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="submit('diterima')" class="dash-btn-primary !bg-emerald-300 hover:!bg-emerald-200">Terima</button>
                    <button type="button" @click="submit('ditolak')" class="dash-btn-ghost !border-red-400/30 !text-red-300">Tolak</button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link :href="`/pendaftaran/${pendaftaran.id}`" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan</span>
                </button>
            </div>
        </form>
    </ModalPage>
</template>
