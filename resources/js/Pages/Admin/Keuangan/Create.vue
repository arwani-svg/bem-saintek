<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    kategoriMasuk: Array,
    kategoriKeluar: Array,
    user: Object,
})

const form = useForm({
    tipe: 'masuk',
    kategori: props.kategoriMasuk[0] || 'Kas Anggota',
    kategori_lain: '',
    keterangan: '',
    jumlah: '',
    tanggal: new Date().toISOString().substring(0, 10),
})

const daftarKategori = computed(() => (form.tipe === 'masuk' ? props.kategoriMasuk : props.kategoriKeluar))
const isLainnya = computed(() => form.kategori === 'Lainnya')

const pilihTipe = (tipe) => {
    form.tipe = tipe
    form.kategori = tipe === 'masuk' ? props.kategoriMasuk[0] : props.kategoriKeluar[0]
    form.kategori_lain = ''
}

const rupiahPreview = computed(() => {
    const n = Number(String(form.jumlah).replace(/[^0-9]/g, '') || 0)
    return 'Rp ' + n.toLocaleString('id-ID')
})

const submit = () => {
    form.jumlah = Number(String(form.jumlah).replace(/[^0-9]/g, '') || 0)
    form.post('/keuangan')
}
</script>

<template>
    <ModalPage
        title="Catat Transaksi"
        subtitle="Uang masuk atau uang keluar kas BEM"
        eyebrow="SAINTEK.OS // KEUANGAN"
        size="lg"
        backHref="/keuangan"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <!-- Tipe -->
                <div>
                    <label class="dash-label">Tipe Transaksi *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button" @click="pilihTipe('masuk')"
                            :class="form.tipe === 'masuk' ? 'border-emerald-300/50 bg-emerald-300/10 text-emerald-200' : 'border-white/10 text-white/40 hover:border-white/25'"
                            class="rounded-xl border px-4 py-3 text-sm font-bold transition"
                        >
                            + Uang Masuk
                        </button>
                        <button
                            type="button" @click="pilihTipe('keluar')"
                            :class="form.tipe === 'keluar' ? 'border-rose-300/50 bg-rose-300/10 text-rose-200' : 'border-white/10 text-white/40 hover:border-white/25'"
                            class="rounded-xl border px-4 py-3 text-sm font-bold transition"
                        >
                            − Uang Keluar
                        </button>
                    </div>
                    <p v-if="form.errors.tipe" class="dash-error">{{ form.errors.tipe }}</p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="dash-label">Jumlah (Rp) *</label>
                        <input v-model="form.jumlah" type="text" inputmode="numeric" class="dash-input font-mono" placeholder="cth: 500000" required />
                        <p class="mt-1 font-mono text-xs text-cyan-300">{{ rupiahPreview }}</p>
                        <p v-if="form.errors.jumlah" class="dash-error">{{ form.errors.jumlah }}</p>
                    </div>
                    <div>
                        <label class="dash-label">Tanggal *</label>
                        <input v-model="form.tanggal" type="date" class="dash-input" required />
                        <p v-if="form.errors.tanggal" class="dash-error">{{ form.errors.tanggal }}</p>
                    </div>
                </div>

                <div>
                    <label class="dash-label">Kategori *</label>
                    <select v-model="form.kategori" class="dash-input cursor-pointer">
                        <option v-for="k in daftarKategori" :key="k" :value="k">{{ k }}</option>
                    </select>
                    <p v-if="form.errors.kategori" class="dash-error">{{ form.errors.kategori }}</p>
                </div>

                <div v-if="isLainnya">
                    <label class="dash-label">Nama Kategori Lainnya *</label>
                    <input v-model="form.kategori_lain" type="text" maxlength="100" class="dash-input" placeholder="cth: Iuran bazar" />
                </div>

                <div>
                    <label class="dash-label">Keterangan</label>
                    <textarea v-model="form.keterangan" rows="2" maxlength="255" class="dash-input" placeholder="cth: Sponsorship Toko ABC untuk Makrab"></textarea>
                    <p v-if="form.errors.keterangan" class="dash-error">{{ form.errors.keterangan }}</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/keuangan" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Transaksi' }}
                </button>
            </div>
        </form>
    </ModalPage>
</template>
