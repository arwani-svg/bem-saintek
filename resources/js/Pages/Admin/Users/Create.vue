<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    prodiList: Array,
    statusList: Array,
    kelasList: Array,
    rolesList: Array,
})

const form = useForm({
    name: '',
    nim: '',
    password: '',
    password_confirmation: '',
    role: 'mahasiswa',
    jenis_kelamin: 'laki-laki',
    prodi: props.prodiList?.[0] || '',
    angkatan: new Date().getFullYear(),
    kelas: '',
    status: 'aktif',
    desa: '',
    kecamatan: '',
    kabupaten: '',
    provinsi: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    email: '',
    nomor_telepon: '',
    kata_motivasi: '',
    prestasi: [''],
    privasi: false,
})

const addPrestasi = () => {
    if (form.prestasi.length < 50) form.prestasi.push('')
}

const removePrestasi = (index) => {
    form.prestasi.splice(index, 1)
    if (form.prestasi.length === 0) form.prestasi.push('')
}

const submit = () => {
    form.prestasi = form.prestasi.map((p) => (p ?? '').trim()).filter((p) => p !== '')
    form.post('/users')
}
</script>

<template>
    <ModalPage
        title="Tambah User"
        subtitle="Buat akun baru untuk mahasiswa atau pengurus"
        eyebrow="SAINTEK.OS // USERS"
        size="xl"
        backHref="/users"
    >
        <form @submit.prevent="submit">
            <div class="space-y-6">
                <h3 class="font-display text-lg font-bold text-white">Informasi Dasar</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Nama Lengkap *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="dash-input"
                            placeholder="Masukkan nama lengkap"
                            required
                        />
                        <p v-if="form.errors.name" class="dash-error">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="dash-label">NIM *</label>
                        <input
                            v-model="form.nim"
                            type="text"
                            class="dash-input font-mono"
                            placeholder="Masukkan NIM"
                            required
                        />
                        <p v-if="form.errors.nim" class="dash-error">{{ form.errors.nim }}</p>
                    </div>

                    <div>
                        <label class="dash-label">Password *</label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="dash-input"
                            placeholder="Masukkan password"
                            required
                        />
                        <p v-if="form.errors.password" class="dash-error">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="dash-label">Konfirmasi Password *</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="dash-input"
                            placeholder="Ulangi password"
                            required
                        />
                    </div>

                    <div>
                        <label class="dash-label">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="dash-input"
                            placeholder="email@contoh.com"
                        />
                        <p v-if="form.errors.email" class="dash-error">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="dash-label">Nomor Telepon</label>
                        <input
                            v-model="form.nomor_telepon"
                            type="text"
                            class="dash-input"
                            placeholder="08xxxxxxxxxx"
                        />
                        <p v-if="form.errors.nomor_telepon" class="dash-error">{{ form.errors.nomor_telepon }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-6 border-t border-white/5 pt-6">
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
                        <input
                            v-model="form.angkatan"
                            type="number"
                            min="2000"
                            :max="new Date().getFullYear()"
                            class="dash-input"
                            placeholder="cth: 2024"
                            required
                        />
                        <p class="mt-1 text-xs text-white/35">Semester dihitung otomatis (Ganjil: Sep–Feb, Genap: Mar–Agu)</p>
                        <p v-if="form.errors.angkatan" class="dash-error">{{ form.errors.angkatan }}</p>
                    </div>

                    <div>
                        <label class="dash-label">Kelas</label>
                        <select v-model="form.kelas" class="dash-input cursor-pointer">
                            <option value="">— Pilih kelas (opsional) —</option>
                            <option v-for="k in kelasList" :key="k" :value="k">{{ k }}</option>
                        </select>
                        <p v-if="form.errors.kelas" class="dash-error">{{ form.errors.kelas }}</p>
                    </div>

                    <div>
                        <label class="dash-label">Status</label>
                        <select v-model="form.status" class="dash-input cursor-pointer">
                            <option v-for="s in statusList" :key="s" :value="s">{{ s }}</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3 pt-6">
                        <input
                            v-model="form.privasi"
                            type="checkbox"
                            id="privasi"
                            class="dash-check"
                        />
                        <label for="privasi" class="cursor-pointer text-sm font-medium text-white/70">Tampilkan profil publik</label>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-6 border-t border-white/5 pt-6">
                <h3 class="font-display text-lg font-bold text-white">Data Pribadi (Opsional)</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                                <input
                                    v-model="form.prestasi[idx]"
                                    type="text"
                                    maxlength="255"
                                    class="dash-input"
                                    :placeholder="`Prestasi ke-${idx + 1} (cth: Juara 1 Lomba Karya Tulis 2024)`"
                                />
                                <button
                                    v-if="form.prestasi.length > 1"
                                    type="button"
                                    @click="removePrestasi(idx)"
                                    class="mt-1 shrink-0 rounded-xl border border-red-400/20 p-2 text-red-300 transition hover:bg-red-400/10"
                                    title="Hapus point ini"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            <button
                                v-if="form.prestasi.length < 50"
                                type="button"
                                @click="addPrestasi"
                                class="dash-btn-ghost !py-2 text-sm"
                            >
                                + Tambah prestasi
                            </button>
                        </div>
                        <p v-if="form.errors.prestasi" class="dash-error">{{ form.errors.prestasi }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/users" class="dash-btn-ghost">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan User</span>
                </button>
            </div>
        </form>
    </ModalPage>
</template>
