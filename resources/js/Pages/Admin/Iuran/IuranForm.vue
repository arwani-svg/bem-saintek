
<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, reactive, ref, watch, onBeforeUnmount } from 'vue'

const props = defineProps({
    iuran: {
        type: Object,
        default: null,
    },
    prodiList: {
        type: Array,
        default: () => [],
    },
    rolesList: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['success', 'cancel'])

const isEdit = computed(() => !!props.iuran)

const getInitialData = (iuran) => ({
    nama: iuran?.nama || '',
    nominal: iuran?.nominal ? String(iuran.nominal) : '',
    jatuh_tempo: iuran?.jatuh_tempo
        ? String(iuran.jatuh_tempo).substring(0, 10)
        : '',
    deskripsi: iuran?.deskripsi || '',
    status: iuran?.status || 'aktif',
    peserta_ids: [],
})

const form = useForm(getInitialData(props.iuran))

// ========================================
// RESET FORM
// ========================================

watch(
    () => props.iuran,
    (iuran) => {
        form.reset()
        form.clearErrors()
        Object.assign(form, getInitialData(iuran))

        dipilih.value = new Map()
        q.search = ''
        q.prodi = ''
        q.role = ''
        hasil.value = []

        if (!iuran) {
            fetchHasil()
        }
    }
)

// ========================================
// NOMINAL RUPIAH
// ========================================

const rupiahPreview = computed(() => {
    const n = Number(
        String(form.nominal).replace(/[^0-9]/g, '') || 0
    )

    return 'Rp ' + n.toLocaleString('id-ID')
})

// ========================================
// PICKER PESERTA
// ========================================

const q = reactive({
    search: '',
    prodi: '',
    role: '',
})

const hasil = ref([])
const loading = ref(false)
const dipilih = ref(new Map())

let debounceTimer = null
let requestController = null

const fetchHasil = async () => {
    if (isEdit.value) return

    if (requestController) {
        requestController.abort()
    }

    requestController = new AbortController()
    loading.value = true

    try {
        const params = new URLSearchParams()

        if (q.search) params.append('search', q.search)
        if (q.prodi) params.append('prodi', q.prodi)
        if (q.role) params.append('role', q.role)

        params.append('limit', '20')

        const res = await fetch(
            '/iuran/cari-user?' + params.toString(),
            {
                headers: {
                    Accept: 'application/json',
                },
                signal: requestController.signal,
            }
        )

        hasil.value = res.ok ? await res.json() : []
    } catch (e) {
        if (e.name !== 'AbortError') {
            hasil.value = []
        }
    } finally {
        if (!requestController?.signal.aborted) {
            loading.value = false
        }
    }
}

watch(
    q,
    () => {
        clearTimeout(debounceTimer)

        debounceTimer = setTimeout(fetchHasil, 300)
    },
    { deep: true }
)

// Ambil peserta pertama kali saat modal create dibuka
if (!isEdit.value) {
    fetchHasil()
}

onBeforeUnmount(() => {
    clearTimeout(debounceTimer)
    requestController?.abort()
})

// ========================================
// KELOLA PESERTA
// ========================================

const toggle = (u) => {
    const next = new Map(dipilih.value)

    if (next.has(u.id)) {
        next.delete(u.id)
    } else {
        next.set(u.id, u)
    }

    dipilih.value = next
    form.peserta_ids = [...next.keys()]
}

const pilihSemuaHasil = () => {
    const next = new Map(dipilih.value)

    hasil.value.forEach((u) => {
        next.set(u.id, u)
    })

    dipilih.value = next
    form.peserta_ids = [...next.keys()]
}

const bersihkan = () => {
    dipilih.value = new Map()
    form.peserta_ids = []
}

const isDipilih = (id) => dipilih.value.has(id)

// ========================================
// SUBMIT
// ========================================

const submit = () => {
    const nominal = Number(
        String(form.nominal).replace(/[^0-9]/g, '') || 0
    )

    if (isEdit.value) {
        form.transform((data) => ({
            nama: data.nama,
            nominal,
            jatuh_tempo: data.jatuh_tempo || null,
            deskripsi: data.deskripsi,
            status: data.status,
        }))

        form.put(`/iuran/${props.iuran.id}`, {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    } else {
        form.transform((data) => ({
            nama: data.nama,
            nominal,
            jatuh_tempo: data.jatuh_tempo || null,
            deskripsi: data.deskripsi,
            peserta_ids: data.peserta_ids,
        }))

        form.post('/iuran', {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        })
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">

        <!-- ================================== -->
        <!-- INFORMASI IURAN -->
        <!-- ================================== -->

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <div class="md:col-span-2">
                <label class="dash-label">Nama Iuran *</label>

                <input
                    v-model="form.nama"
                    type="text"
                    maxlength="150"
                    class="dash-input"
                    placeholder="cth: Kas Oktober 2026"
                    required
                />

                <p v-if="form.errors.nama" class="dash-error">
                    {{ form.errors.nama }}
                </p>
            </div>

            <div>
                <label class="dash-label">
                    Nominal per orang (Rp) *
                </label>

                <input
                    v-model="form.nominal"
                    type="text"
                    inputmode="numeric"
                    class="dash-input font-mono"
                    placeholder="cth: 20000"
                    required
                />

                <p class="mt-1 font-mono text-xs text-cyan-300">
                    {{ rupiahPreview }}
                </p>

                <p v-if="form.errors.nominal" class="dash-error">
                    {{ form.errors.nominal }}
                </p>

                <p v-if="isEdit" class="dash-hint">
                    Nominal baru hanya berlaku untuk peserta baru.
                </p>
            </div>

            <div>
                <label class="dash-label">Jatuh Tempo</label>

                <input
                    v-model="form.jatuh_tempo"
                    type="date"
                    class="dash-input"
                />

                <p v-if="form.errors.jatuh_tempo" class="dash-error">
                    {{ form.errors.jatuh_tempo }}
                </p>
            </div>

            <!-- Status hanya saat edit -->
            <div v-if="isEdit" class="md:col-span-2">
                <label class="dash-label">Status Iuran</label>

                <select
                    v-model="form.status"
                    class="dash-input cursor-pointer"
                >
                    <option value="aktif">
                        Aktif — masih ditagih
                    </option>

                    <option value="selesai">
                        Selesai — stop penagihan
                    </option>

                    <option value="dibatalkan">
                        Dibatalkan
                    </option>
                </select>

                <p v-if="form.errors.status" class="dash-error">
                    {{ form.errors.status }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="dash-label">Deskripsi</label>

                <textarea
                    v-model="form.deskripsi"
                    rows="3"
                    maxlength="1000"
                    class="dash-input resize-none"
                    placeholder="cth: Kas wajib pengurus untuk operasional sekretariat"
                ></textarea>

                <p v-if="form.errors.deskripsi" class="dash-error">
                    {{ form.errors.deskripsi }}
                </p>
            </div>

        </div>

        <!-- ================================== -->
        <!-- PICKER PESERTA (CREATE SAJA) -->
        <!-- ================================== -->

        <div
            v-if="!isEdit"
            class="rounded-xl border border-white/10 bg-white/[0.02] p-4"
        >

            <div class="flex items-center justify-between gap-3">

                <p class="font-mono text-[11px] tracking-[.25em] text-cyan-300">
                    PESERTA WAJIB BAYAR ({{ dipilih.size }})
                </p>

                <button
                    v-if="dipilih.size"
                    type="button"
                    @click="bersihkan"
                    class="text-xs text-rose-300 hover:underline"
                >
                    Bersihkan
                </button>

            </div>

            <!-- Filter peserta -->
            <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">

                <input
                    v-model="q.search"
                    type="text"
                    placeholder="Cari nama / NIM..."
                    class="dash-input"
                />

                <select
                    v-model="q.prodi"
                    class="dash-input cursor-pointer"
                >
                    <option value="">Semua prodi</option>

                    <option
                        v-for="p in prodiList"
                        :key="p"
                        :value="p"
                    >
                        {{ p }}
                    </option>
                </select>

                <select
                    v-model="q.role"
                    class="dash-input cursor-pointer"
                >
                    <option value="">Semua role</option>

                    <option
                        v-for="r in rolesList"
                        :key="r"
                        :value="r"
                    >
                        {{ r }}
                    </option>
                </select>

            </div>

            <!-- Hasil pencarian -->
            <div class="mt-3 flex items-center justify-between gap-3">

                <p class="text-xs text-white/40">
                    {{ loading ? 'Mencari...' : hasil.length + ' hasil' }}
                </p>

                <button
                    v-if="hasil.length"
                    type="button"
                    @click="pilihSemuaHasil"
                    class="dash-btn-ghost !px-3 !py-1.5 text-xs"
                >
                    + Pilih semua hasil
                </button>

            </div>

            <div class="mt-2 max-h-56 space-y-1.5 overflow-y-auto pr-1 saintek-scroll">

                <button
                    v-for="u in hasil"
                    :key="u.id"
                    type="button"
                    @click="toggle(u)"
                    :class="isDipilih(u.id)
                        ? 'border-cyan-300/40 bg-cyan-300/10'
                        : 'border-white/5 bg-white/[0.02] hover:border-white/20'"
                    class="flex w-full items-center gap-3 rounded-xl border px-3 py-2 text-left transition"
                >

                    <span
                        :class="isDipilih(u.id)
                            ? 'bg-cyan-300 text-black'
                            : 'border-white/20 text-transparent'"
                        class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border text-xs font-bold"
                    >
                        ✓
                    </span>

                    <span class="min-w-0 flex-1">

                        <span class="block truncate text-sm font-semibold text-white">
                            {{ u.name }}
                        </span>

                        <span class="block truncate font-mono text-[11px] text-white/40">
                            {{ u.nim }} · {{ u.prodi }} · {{ u.role }}
                        </span>

                    </span>

                </button>

                <p
                    v-if="!hasil.length && !loading"
                    class="py-6 text-center text-sm text-white/30"
                >
                    Tidak ada hasil. Coba kata kunci lain.
                </p>

            </div>

            <!-- Peserta terpilih -->
            <div
                v-if="dipilih.size"
                class="mt-3 flex flex-wrap gap-1.5"
            >

                <span
                    v-for="[id, u] in dipilih"
                    :key="id"
                    class="inline-flex items-center gap-1.5 rounded-full border border-cyan-300/25 bg-cyan-300/10 px-2.5 py-1 text-xs text-cyan-200"
                >
                    {{ u.name }}

                    <button
                        type="button"
                        @click="toggle(u)"
                        class="hover:text-rose-300"
                    >
                        ×
                    </button>

                </span>

            </div>

            <p v-if="form.errors.peserta_ids" class="dash-error mt-2">
                {{ form.errors.peserta_ids }}
            </p>

        </div>

        <!-- ================================== -->
        <!-- FOOTER -->
        <!-- ================================== -->

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
                :disabled="
                    form.processing ||
                    (!isEdit && !dipilih.size)
                "
                class="dash-btn-primary disabled:opacity-40"
            >
                <span v-if="form.processing">
                    Menyimpan...
                </span>

                <span v-else-if="isEdit">
                    Simpan Perubahan
                </span>

                <span v-else>
                    Buat Iuran ({{ dipilih.size }} peserta)
                </span>
            </button>

        </div>

    </form>
</template>
