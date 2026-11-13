
<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    proker: {
        type: Object,
        default: null,
    },
    departemens: {
        type: Array,
        default: () => [],
    },
    statusList: {
        type: Object,
        default: () => ({}),
    },
    warnaList: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['success', 'cancel'])

const isEdit = !!props.proker

const form = useForm({
    departemen_id: props.proker?.departemen_id ?? props.departemens[0]?.id ?? '',
    judul: props.proker?.judul || '',
    deskripsi: props.proker?.deskripsi || '',
    kategori: props.proker?.kategori || '',
    warna: props.proker?.warna || 'blue',
    status: props.proker?.status || 'berlangsung',
    tanggal_mulai: props.proker?.tanggal_mulai
        ? String(props.proker.tanggal_mulai).substring(0, 10)
        : '',
    tanggal_selesai: props.proker?.tanggal_selesai
        ? String(props.proker.tanggal_selesai).substring(0, 10)
        : '',
    lokasi: props.proker?.lokasi || '',
    penanggung_jawab: props.proker?.penanggung_jawab || '',
    is_unggulan: props.proker?.is_unggulan ?? true,
})

const submit = () => {
    const options = {
        onSuccess: () => emit('success'),
    }

    if (isEdit) {
        form.put(`/proker/${props.proker.id}`, options)
    } else {
        form.post('/proker', options)
    }
}
</script>

<template>
    <div
        v-if="departemens.length === 0"
        class="rounded-xl border border-amber-300/25 bg-amber-300/5 p-6 text-sm text-amber-300"
    >
        Belum ada departemen.
        <a href="/departemen" class="font-semibold underline">
            Kelola departemen terlebih dahulu.
        </a>
    </div>

    <form v-else @submit.prevent="submit" class="space-y-6">

        <div class="space-y-6">
            <h3 class="font-display text-lg font-bold text-white">
                Informasi Program
            </h3>

            <!-- DEPARTEMEN & STATUS -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="dash-label">Departemen *</label>

                    <select
                        v-model="form.departemen_id"
                        class="dash-input cursor-pointer"
                        required
                    >
                        <option
                            v-for="d in departemens"
                            :key="d.id"
                            :value="d.id"
                        >
                            {{ d.nama }}{{ d.kepanjangan ? ` — ${d.kepanjangan}` : '' }}
                        </option>
                    </select>

                    <p v-if="form.errors.departemen_id" class="dash-error">
                        {{ form.errors.departemen_id }}
                    </p>
                </div>

                <div>
                    <label class="dash-label">Status *</label>

                    <select
                        v-model="form.status"
                        class="dash-input cursor-pointer"
                        required
                    >
                        <option
                            v-for="(label, key) in statusList"
                            :key="key"
                            :value="key"
                        >
                            {{ label }}
                        </option>
                    </select>

                    <p v-if="form.errors.status" class="dash-error">
                        {{ form.errors.status }}
                    </p>
                </div>
            </div>

            <!-- JUDUL -->
            <div>
                <label class="dash-label">Judul Program *</label>

                <input
                    v-model="form.judul"
                    type="text"
                    class="dash-input"
                    placeholder="cth: Lomba Olimpiade Sains"
                    required
                />

                <p v-if="form.errors.judul" class="dash-error">
                    {{ form.errors.judul }}
                </p>
            </div>

            <!-- DESKRIPSI -->
            <div>
                <label class="dash-label">Deskripsi</label>

                <textarea
                    v-model="form.deskripsi"
                    rows="4"
                    class="dash-input resize-none"
                    placeholder="Ceritakan tujuan dan bentuk kegiatan proker ini..."
                ></textarea>

                <p v-if="form.errors.deskripsi" class="dash-error">
                    {{ form.errors.deskripsi }}
                </p>
            </div>

            <!-- DETAIL PROGRAM -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                <div>
                    <label class="dash-label">Kategori</label>

                    <input
                        v-model="form.kategori"
                        type="text"
                        class="dash-input"
                        placeholder="cth: Akademik"
                    />

                    <p v-if="form.errors.kategori" class="dash-error">
                        {{ form.errors.kategori }}
                    </p>
                </div>

                <div>
                    <label class="dash-label">Warna Kartu *</label>

                    <select
                        v-model="form.warna"
                        class="dash-input cursor-pointer"
                        required
                    >
                        <option
                            v-for="w in warnaList"
                            :key="w"
                            :value="w"
                            class="capitalize"
                        >
                            {{ w }}
                        </option>
                    </select>

                    <p v-if="form.errors.warna" class="dash-error">
                        {{ form.errors.warna }}
                    </p>
                </div>

                <div>
                    <label class="dash-label">Tanggal Mulai</label>

                    <input
                        v-model="form.tanggal_mulai"
                        type="date"
                        class="dash-input"
                    />

                    <p v-if="form.errors.tanggal_mulai" class="dash-error">
                        {{ form.errors.tanggal_mulai }}
                    </p>
                </div>

                <div>
                    <label class="dash-label">Tanggal Selesai</label>

                    <input
                        v-model="form.tanggal_selesai"
                        type="date"
                        class="dash-input"
                    />

                    <p v-if="form.errors.tanggal_selesai" class="dash-error">
                        {{ form.errors.tanggal_selesai }}
                    </p>
                </div>

                <div>
                    <label class="dash-label">Lokasi</label>

                    <input
                        v-model="form.lokasi"
                        type="text"
                        class="dash-input"
                        placeholder="cth: Aula Fakultas"
                    />

                    <p v-if="form.errors.lokasi" class="dash-error">
                        {{ form.errors.lokasi }}
                    </p>
                </div>

                <div>
                    <label class="dash-label">Penanggung Jawab</label>

                    <input
                        v-model="form.penanggung_jawab"
                        type="text"
                        class="dash-input"
                        placeholder="Nama penanggung jawab"
                    />

                    <p v-if="form.errors.penanggung_jawab" class="dash-error">
                        {{ form.errors.penanggung_jawab }}
                    </p>
                </div>
            </div>

            <!-- UNGGULAN -->
            <label class="flex cursor-pointer items-center gap-3 pt-1">
                <input
                    v-model="form.is_unggulan"
                    type="checkbox"
                    class="dash-check"
                />

                <span class="text-sm font-medium text-white/70">
                    Tampilkan di halaman depan ⭐
                </span>
            </label>
        </div>

        <!-- BUTTON -->
        <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
            <button
                type="button"
                class="dash-btn-ghost"
                @click="emit('cancel')"
            >
                Batal
            </button>

            <button
                type="submit"
                :disabled="form.processing"
                class="dash-btn-primary"
            >
                {{
                    form.processing
                        ? 'Menyimpan...'
                        : isEdit
                            ? 'Simpan Perubahan'
                            : 'Simpan Proker'
                }}
            </button>
        </div>
    </form>
</template>
