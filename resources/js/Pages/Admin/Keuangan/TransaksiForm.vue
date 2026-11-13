
<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, watch } from 'vue'

const props = defineProps({
    transaksi: {
        type: Object,
        default: null,
    },
    kategoriMasuk: Array,
    kategoriKeluar: Array,
})

const emit = defineEmits(['success', 'cancel'])

const isEdit = computed(() => !!props.transaksi)

const isPreset = (tipe, kategori) => {
    const list = tipe === 'masuk'
        ? props.kategoriMasuk
        : props.kategoriKeluar

    return list.includes(kategori)
}

const getInitialData = (transaksi) => {
    const tipe = transaksi?.tipe || 'masuk'
    const kategori = transaksi?.kategori || props.kategoriMasuk[0] || 'Kas Anggota'
    const preset = isPreset(tipe, kategori)

    return {
        tipe,
        kategori: preset ? kategori : 'Lainnya',
        kategori_lain: preset ? '' : kategori,
        keterangan: transaksi?.keterangan || '',
        jumlah: transaksi?.jumlah || '',
        tanggal: transaksi?.tanggal
            ? String(transaksi.tanggal).substring(0, 10)
            : new Date().toISOString().substring(0, 10),
    }
}

const form = useForm(getInitialData(props.transaksi))

// Reset form ketika berpindah create / edit
watch(
    () => props.transaksi,
    (transaksi) => {
        form.reset()
        form.clearErrors()
        Object.assign(form, getInitialData(transaksi))
    }
)

const daftarKategori = computed(() =>
    form.tipe === 'masuk'
        ? props.kategoriMasuk
        : props.kategoriKeluar
)

const isLainnya = computed(() => form.kategori === 'Lainnya')

const pilihTipe = (tipe) => {
    if (form.tipe === tipe) return

    form.tipe = tipe
    form.kategori = tipe === 'masuk'
        ? props.kategoriMasuk[0]
        : props.kategoriKeluar[0]

    form.kategori_lain = ''
    form.clearErrors('kategori', 'kategori_lain')
}

const rupiahPreview = computed(() => {
    const n = Number(String(form.jumlah).replace(/[^0-9]/g, '') || 0)
    return 'Rp ' + n.toLocaleString('id-ID')
})

const submit = () => {
    const payloadJumlah = Number(
        String(form.jumlah).replace(/[^0-9]/g, '') || 0
    )

    form.transform((data) => ({
        ...data,
        jumlah: payloadJumlah,
    }))

    if (isEdit.value) {
        form.put(`/keuangan/${props.transaksi.id}`, {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    } else {
        form.post('/keuangan', {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-6">

            <!-- Tipe Transaksi -->
            <div>
                <label class="dash-label">Tipe Transaksi *</label>

                <div class="grid grid-cols-2 gap-3">
                    <button
                        type="button"
                        @click="pilihTipe('masuk')"
                        :class="form.tipe === 'masuk'
                            ? 'border-emerald-300/50 bg-emerald-300/10 text-emerald-200'
                            : 'border-white/10 text-white/40 hover:border-white/25'"
                        class="rounded-xl border px-4 py-3 text-sm font-bold transition"
                    >
                        + Uang Masuk
                    </button>

                    <button
                        type="button"
                        @click="pilihTipe('keluar')"
                        :class="form.tipe === 'keluar'
                            ? 'border-rose-300/50 bg-rose-300/10 text-rose-200'
                            : 'border-white/10 text-white/40 hover:border-white/25'"
                        class="rounded-xl border px-4 py-3 text-sm font-bold transition"
                    >
                        − Uang Keluar
                    </button>
                </div>

                <p v-if="form.errors.tipe" class="dash-error">
                    {{ form.errors.tipe }}
                </p>
            </div>

            <!-- Jumlah dan Tanggal -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Jumlah (Rp) *</label>

                    <input
                        v-model="form.jumlah"
                        type="text"
                        inputmode="numeric"
                        class="dash-input font-mono"
                        placeholder="cth: 500000"
                        required
                    />

                    <p class="mt-1 font-mono text-xs text-cyan-300">
                        {{ rupiahPreview }}
                    </p>

                    <p v-if="form.errors.jumlah" class="dash-error">
                        {{ form.errors.jumlah }}
                    </p>
                </div>

                <div>
                    <label class="dash-label">Tanggal *</label>

                    <input
                        v-model="form.tanggal"
                        type="date"
                        class="dash-input"
                        required
                    />

                    <p v-if="form.errors.tanggal" class="dash-error">
                        {{ form.errors.tanggal }}
                    </p>
                </div>
            </div>

            <!-- Kategori -->
            <div>
                <label class="dash-label">Kategori *</label>

                <select
                    v-model="form.kategori"
                    class="dash-input cursor-pointer"
                >
                    <option
                        v-for="k in daftarKategori"
                        :key="k"
                        :value="k"
                    >
                        {{ k }}
                    </option>
                </select>

                <p v-if="form.errors.kategori" class="dash-error">
                    {{ form.errors.kategori }}
                </p>
            </div>

            <!-- Kategori Lainnya -->
            <div v-if="isLainnya">
                <label class="dash-label">Nama Kategori Lainnya *</label>

                <input
                    v-model="form.kategori_lain"
                    type="text"
                    maxlength="100"
                    class="dash-input"
                    placeholder="cth: Iuran bazar"
                />

                <p v-if="form.errors.kategori_lain" class="dash-error">
                    {{ form.errors.kategori_lain }}
                </p>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="dash-label">Keterangan</label>

                <textarea
                    v-model="form.keterangan"
                    rows="3"
                    maxlength="255"
                    class="dash-input resize-none"
                    placeholder="cth: Sponsorship Toko ABC untuk Makrab"
                ></textarea>

                <p v-if="form.errors.keterangan" class="dash-error">
                    {{ form.errors.keterangan }}
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
            <button
                type="button"
                @click="emit('cancel')"
                class="dash-btn-ghost"
            >
                Batal
            </button>

            <button
                type="submit"
                :disabled="form.processing"
                class="dash-btn-primary"
            >
                <span v-if="form.processing">
                    Menyimpan...
                </span>

                <span v-else>
                    {{ isEdit ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
                </span>
            </button>
        </div>
    </form>
</template>
