<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
    prodiList: Array,
    statusList: Array,
    keloasList: Array,
    rolesList: Array,
})

const emit = defineEmits(['success', 'cancel'])

const isEdit = () => !!props.user

const form = useForm({
    name: props.user?.name || '',
    nim: props.user?.nim || '',
    password: '',
    password_confirmation: '',
    role: props.user?.role || 'mahasiswa',
    jenis_kelamin: props.user?.jenis_kelamin || 'laki-laki',
    prodi: props.user?.prodi || props.prodiList?.[0] || '',
    angkatan: props.user?.angkatan || new Date().getFullYear(),
    kelas: props.user?.kelas || '',
    status: props.user?.status || 'aktif',
    desa: props.user?.desa || '',
    kecamatan: props.user?.kecamatan || '',
    kabupaten: props.user?.kabupaten || '',
    provinsi: props.user?.provinsi || '',
    tempat_lahir: props.user?.tempat_lahir || '',
    tanggal_lahir: props.user?.tanggal_lahir || '',
    email: props.user?.email || '',
    nomor_telepon: props.user?.nomor_telepon || '',
    kata_motivasi: props.user?.kata_motivasi || '',
    prestasi: props.user?.prestasi?.length ? [...props.user.prestasi] : [''],
    privasi: props.user?.privasi ?? false,
    foto: null,
})

const fotoInput = ref(null)
const previewUrl = ref(props.user?.foto_url || null)
const fotoError = ref('')

watch(() => props.user, (u) => {
    form.reset()
    form.clearErrors()
    form.name = u?.name || ''
    form.nim = u?.nim || ''
    form.password = ''
    form.password_confirmation = ''
    form.role = u?.role || 'mahasiswa'
    form.jenis_kelamin = u?.jenis_kelamin || 'laki-laki'
    form.prodi = u?.prodi || props.prodiList?.[0] || ''
    form.angkatan = u?.angkatan || new Date().getFullYear()
    form.kelas = u?.kelas || ''
    form.status = u?.status || 'aktif'
    form.desa = u?.desa || ''
    form.kecamatan = u?.kecamatan || ''
    form.kabupaten = u?.kabupaten || ''
    form.provinsi = u?.provinsi || ''
    form.tempat_lahir = u?.tempat_lahir || ''
    form.tanggal_lahir = u?.tanggal_lahir || ''
    form.email = u?.email || ''
    form.nomor_telepon = u?.nomor_telepon || ''
    form.kata_motivasi = u?.kata_motivasi || ''
    form.prestasi = u?.prestasi?.length ? [...u.prestasi] : ['']
    form.privasi = u?.privasi ?? false
    form.foto = null
    previewUrl.value = u?.foto_url || null
})

const onFotoChange = (e) => {
    fotoError.value = ''
    const file = e.target.files?.[0] || null
    if (!file) {
        form.foto = null
        return
    }
    if (!['image/jpeg', 'image/png', 'image/webp', 'image/jpg'].includes(file.type)) {
        fotoError.value = 'Format harus JPG, PNG, atau WebP.'
        form.foto = null
        return
    }
    if (file.size > 1024 * 1024) {
        fotoError.value = 'Ukuran maksimal 1 MB agar hemat storage.'
        form.foto = null
        return
    }
    form.foto = file
    previewUrl.value = URL.createObjectURL(file)
}

const addPrestasi = () => {
    if (form.prestasi.length < 50) form.prestasi.push('')
}

const removePrestasi = (index) => {
    form.prestasi.splice(index, 1)
    if (form.prestasi.length === 0) form.prestasi.push('')
}

const submit = () => {
    form.prestasi = form.prestasi.map((p) => (p ?? '').trim()).filter((p) => p !== '')
    
    if (isEdit()) {
        // Jika backend butuh _method PUT untuk form-data dengan file
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(`/users/${props.user.id}`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    } else {
        form.post('/users', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-6">
            <h3 class="font-display text-lg font-bold text-white">Foto Profil <span class="font-mono text-xs font-normal text-white/30">opsional · max 1 MB</span></h3>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <img v-if="previewUrl" :src="previewUrl" alt="Preview foto" class="h-20 w-20 rounded-2xl border border-white/20 object-cover" />
                <div v-else class="flex h-20 w-20 items-center justify-center rounded-2xl border border-dashed border-white/20 text-2xl text-white/20">?</div>
                <div class="min-w-0 flex-1">
                    <input ref="fotoInput" type="file" accept="image/jpeg,image/png,image/webp,image/jpg" @change="onFotoChange" class="block w-full text-sm text-white/60 file:mr-3 file:cursor-pointer file:rounded-xl file:border file:border-cyan-300/30 file:bg-cyan-300/10 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-cyan-300 hover:file:bg-cyan-300/20" />
                    <p class="mt-1 text-xs text-white/35">JPG/PNG/WebP · maksimal 1 MB. Kosongkan bila belum ada foto.</p>
                    <p v-if="fotoError" class="dash-error">{{ fotoError }}</p>
                    <p v-if="form.errors.foto" class="dash-error">{{ form.errors.foto }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 border-t border-white/5 pt-6">
            <h3 class="font-display text-lg font-bold text-white">Informasi Dasar</h3>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Nama Lengkap *</label>
                    <input v-model="form.name" type="text" class="dash-input" placeholder="Masukkan nama lengkap" required />
                    <p v-if="form.errors.name" class="dash-error">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="dash-label">NIM *</label>
                    <input v-model="form.nim" type="text" class="dash-input font-mono" placeholder="Masukkan NIM" required />
                    <p v-if="form.errors.nim" class="dash-error">{{ form.errors.nim }}</p>
                </div>

                <div>
                    <label class="dash-label">Password <span v-if="isEdit()">(kosongkan jika tidak diubah)</span> *</label>
                    <input v-model="form.password" type="password" class="dash-input" placeholder="Masukkan password" :required="!isEdit()" />
                    <p v-if="form.errors.password" class="dash-error">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="dash-label">Konfirmasi Password *</label>
                    <input v-model="form.password_confirmation" type="password" class="dash-input" placeholder="Ulangi password" :required="!isEdit() || form.password" />
                </div>

                <div>
                    <label class="dash-label">Email</label>
                    <input v-model="form.email" type="email" class="dash-input" placeholder="email@contoh.com" />
                    <p v-if="form.errors.email" class="dash-error">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="dash-label">Nomor Telepon</label>
                    <input v-model="form.nomor_telepon" type="text" class="dash-input" placeholder="08xxxxxxxxxx" />
                    <p v-if="form.errors.nomor_telepon" class="dash-error">{{ form.errors.nomor_telepon }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 border-t border-white/5 pt-6">
            <h3 class="font-display text-lg font-bold text-white">Informasi Akademik</h3>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Role</label>
                    <select v-model="form.role" class="dash-input cursor-pointer">
                        <option v-for="r in rolesList ?? ['mahasiswa', 'pengurus']" :key="r" :value="r" class="capitalize">{{ r }}</option>
                    </select>
                </div>

                <div>
                    <label class="dash-label">Jenis Kelamin</label>
                    <select v-model="form.jenis_kelamin" class="dash-input cursor-pointer">
                        <option value="laki-laki">Laki-laki</option>
                        <option value="perempuan">Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="dash-label">Program Studi *</label>
                    <select v-model="form.prodi" class="dash-input cursor-pointer" required>
                        <option v-for="p in prodiList" :key="p" :value="p">{{ p }}</option>
                    </select>
                    <p v-if="form.errors.prodi" class="dash-error">{{ form.errors.prodi }}</p>
                </div>

                <div>
                    <label class="dash-label">Angkatan *</label>
                    <input v-model="form.angkatan" type="number" min="2000" :max="new Date().getFullYear()" class="dash-input" required />
                    <p v-if="form.errors.angkatan" class="dash-error">{{ form.errors.angkatan }}</p>
                </div>

                <div>
                    <label class="dash-label">Kelas</label>
                    <select v-model="form.kelas" class="dash-input cursor-pointer">
                        <option value="">— Pilih kelas (opsional) —</option>
                        <option v-for="k in kelasList" :key="k" :value="k">{{ k }}</option>
                    </select>
                </div>

                <div>
                    <label class="dash-label">Status</label>
                    <select v-model="form.status" class="dash-input cursor-pointer">
                        <option v-for="s in statusList" :key="s" :value="s">{{ s }}</option>
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-6">
                    <input v-model="form.privasi" type="checkbox" id="privasi" class="dash-check" />
                    <label for="privasi" class="cursor-pointer text-sm font-medium text-white/70">Tampilkan profil publik</label>
                </div>
            </div>
        </div>

        <div class="space-y-6 border-t border-white/5 pt-6">
            <h3 class="font-display text-lg font-bold text-white">Data Pribadi (Opsional)</h3>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Tempat Lahir</label>
                    <input v-model="form.tempat_lahir" type="text" class="dash-input" placeholder="Kota lahir" />
                </div>
                <div>
                    <label class="dash-label">Tanggal Lahir</label>
                    <input v-model="form.tanggal_lahir" type="date" class="dash-input" />
                </div>
                <div>
                    <label class="dash-label">Desa</label>
                    <input v-model="form.desa" type="text" class="dash-input" placeholder="Nama desa" />
                </div>
                <div>
                    <label class="dash-label">Kecamatan</label>
                    <input v-model="form.kecamatan" type="text" class="dash-input" placeholder="Nama kecamatan" />
                </div>
                <div>
                    <label class="dash-label">Kabupaten</label>
                    <input v-model="form.kabupaten" type="text" class="dash-input" placeholder="Nama kabupaten" />
                </div>
                <div>
                    <label class="dash-label">Provinsi</label>
                    <input v-model="form.provinsi" type="text" class="dash-input" placeholder="Nama provinsi" />
                </div>
                <div class="md:col-span-2">
                    <label class="dash-label">Kata Motivasi</label>
                    <textarea v-model="form.kata_motivasi" rows="2" class="dash-input" placeholder="Kata motivasi..."></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="dash-label">Prestasi (satu point per baris)</label>
                    <div class="space-y-3">
                        <div v-for="(point, idx) in form.prestasi" :key="idx" class="flex items-start gap-2">
                            <span class="mt-2.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-300/10 font-mono text-xs font-bold text-cyan-300">{{ idx + 1 }}</span>
                            <input v-model="form.prestasi[idx]" type="text" maxlength="255" class="dash-input" placeholder="Prestasi..." />
                            <button v-if="form.prestasi.length > 1" type="button" @click="removePrestasi(idx)" class="mt-1 shrink-0 rounded-xl border border-red-400/20 p-2 text-red-300 transition hover:bg-red-400/10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                        <button v-if="form.prestasi.length < 50" type="button" @click="addPrestasi" class="dash-btn-ghost !py-2 text-sm">+ Tambah prestasi</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
            <button type="button" @click="emit('cancel')" class="dash-btn-ghost">Batal</button>
            <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                <span v-if="form.processing">Menyimpan...</span>
                <span v-else>{{ isEdit() ? 'Simpan Perubahan' : 'Simpan User' }}</span>
            </button>
        </div>
    </form>
</template>