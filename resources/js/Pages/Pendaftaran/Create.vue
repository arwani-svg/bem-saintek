<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    user: Object,
    prodiList: Array,
    prefill: Object,
})

const form = useForm({
    nama: props.prefill?.nama || '',
    nim: props.prefill?.nim || '',
    prodi: props.prefill?.prodi || props.prodiList?.[0] || '',
    semester: props.prefill?.semester || 1,
    tempat_lahir: props.prefill?.tempat_lahir || '',
    tanggal_lahir: props.prefill?.tanggal_lahir || '',
    asrama: '',
    email: props.prefill?.email || '',
    tujuan: '',
    riwayat_sekolah: [''],
    riwayat_madrasah: [''],
    riwayat_organisasi: [''],
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
    form.post('/pendaftaran')
}
</script>

<template>
    <ModalPage
        title="Daftar Pengurus BEM"
        subtitle="Lengkapi data di bawah. Status awal: pending."
        eyebrow="SAINTEK.OS // PENDAFTARAN"
        size="xl"
        backHref="/pendaftaran"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <h3 class="font-display text-lg font-bold text-white">Data Diri</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Nama Lengkap *</label>
                        <input v-model="form.nama" type="text" class="dash-input" placeholder="Nama lengkap" required />
                        <p v-if="form.errors.nama" class="dash-error">{{ form.errors.nama }}</p>
                    </div>
                    <div>
                        <label class="dash-label">NPM / NIM *</label>
                        <input v-model="form.nim" type="text" class="dash-input font-mono" placeholder="NPM / NIM" required />
                        <p v-if="form.errors.nim" class="dash-error">{{ form.errors.nim }}</p>
                    </div>
                    <div>
                        <label class="dash-label">Program Studi *</label>
                        <select v-model="form.prodi" class="dash-input cursor-pointer" required>
                            <option v-for="p in prodiList" :key="p" :value="p">{{ p }}</option>
                        </select>
                        <p v-if="form.errors.prodi" class="dash-error">{{ form.errors.prodi }}</p>
                    </div>
                    <div>
                        <label class="dash-label">Semester * (1–14)</label>
                        <input v-model="form.semester" type="number" min="1" max="14" class="dash-input" required />
                        <p v-if="form.errors.semester" class="dash-error">{{ form.errors.semester }}</p>
                    </div>
                    <div>
                        <label class="dash-label">Tempat Lahir</label>
                        <input v-model="form.tempat_lahir" type="text" class="dash-input" placeholder="Kota lahir" />
                    </div>
                    <div>
                        <label class="dash-label">Tanggal Lahir</label>
                        <input v-model="form.tanggal_lahir" type="date" class="dash-input" />
                        <p v-if="form.errors.tanggal_lahir" class="dash-error">{{ form.errors.tanggal_lahir }}</p>
                    </div>
                    <div>
                        <label class="dash-label">Asrama</label>
                        <input v-model="form.asrama" type="text" class="dash-input" placeholder="Nama / alamat asrama" />
                    </div>
                    <div>
                        <label class="dash-label">Email</label>
                        <input v-model="form.email" type="email" class="dash-input" placeholder="email@contoh.com" />
                        <p v-if="form.errors.email" class="dash-error">{{ form.errors.email }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="dash-label">Tujuan Gabung BEM</label>
                        <textarea v-model="form.tujuan" rows="3" class="dash-input" placeholder="Ceritakan motivasimu..."></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-6 border-t border-white/5 pt-6">
                <h3 class="font-display text-lg font-bold text-white">Riwayat (point dinamis)</h3>

                <div v-for="group in [
                    { key: 'riwayat_sekolah', title: 'Riwayat Pendidikan Sekolah', ph: 'cth: SDN 1 Demak (2012–2018)' },
                    { key: 'riwayat_madrasah', title: 'Riwayat Pendidikan Madrasah', ph: 'cth: MTs N 1 Demak (2018–2021)' },
                    { key: 'riwayat_organisasi', title: 'Riwayat Organisasi', ph: 'cth: Ketua OSIS SMA (2022–2023)' },
                ]" :key="group.key">
                    <label class="dash-label">{{ group.title }}</label>
                    <div class="space-y-3">
                        <div v-for="(point, idx) in form[group.key]" :key="idx" class="flex items-start gap-2">
                            <span class="mt-2.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-300/10 font-mono text-xs font-bold text-cyan-300">{{ idx + 1 }}</span>
                            <input v-model="form[group.key][idx]" type="text" maxlength="255" class="dash-input" :placeholder="group.ph" />
                            <button v-if="form[group.key].length > 1" type="button" @click="removeItem(group.key, idx)" class="mt-1 shrink-0 rounded-xl border border-red-400/20 p-2 text-red-300 transition hover:bg-red-400/10" title="Hapus">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                        <button v-if="form[group.key].length < 30" type="button" @click="addItem(group.key)" class="dash-btn-ghost !py-2 text-sm">+ Tambah</button>
                    </div>
                    <p v-if="form.errors[group.key]" class="dash-error">{{ form.errors[group.key] }}</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/pendaftaran" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    <span v-if="form.processing">Mengirim...</span>
                    <span v-else>Kirim Pendaftaran</span>
                </button>
            </div>
        </form>
    </ModalPage>
</template>
