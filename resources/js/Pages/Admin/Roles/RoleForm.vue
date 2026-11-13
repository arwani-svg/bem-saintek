
<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
    role: {
        type: Object,
        default: null,
    },
    allPermissions: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['success', 'cancel'])

const form = useForm({
    name: props.role?.name || '',
    permissions: props.role ? [...props.role.permissions] : [],
})

watch(() => props.role, (role) => {
    form.name = role?.name || ''
    form.permissions = role ? [...role.permissions] : []
    form.clearErrors()
})

const toggle = (perm) => {
    const i = form.permissions.indexOf(perm)

    if (i >= 0) {
        form.permissions.splice(i, 1)
    } else {
        form.permissions.push(perm)
    }
}

const checkAll = () => {
    form.permissions = props.allPermissions.map(p => p.name)
}

const uncheckAll = () => {
    form.permissions = []
}

const submit = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => emit('success'),
    }

    if (props.role) {
        form.put(`/roles/${props.role.id}`, options)
    } else {
        form.post('/roles', options)
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div>
            <label class="dash-label">Nama Role *</label>

            <input
                v-model="form.name"
                type="text"
                class="dash-input font-mono"
                placeholder="nama-role"
                :disabled="role?.is_protected"
                required
            />

            <p v-if="form.errors.name" class="dash-error">
                {{ form.errors.name }}
            </p>

            <p v-if="role?.is_protected" class="mt-2 text-xs text-amber-300/70">
                Role sistem: nama tidak dapat diubah.
            </p>
        </div>

        <div>
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <label class="dash-label !mb-0">
                    Permissions ({{ form.permissions.length }}/{{ allPermissions.length }})
                </label>

                <div class="flex gap-2">
                    <button
                        type="button"
                        class="dash-btn-ghost !py-1.5 !text-xs"
                        @click="checkAll"
                    >
                        Pilih semua
                    </button>

                    <button
                        type="button"
                        class="dash-btn-ghost !py-1.5 !text-xs"
                        @click="uncheckAll"
                    >
                        Hapus semua
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                <label
                    v-for="p in allPermissions"
                    :key="p.id"
                    class="flex cursor-pointer items-center gap-3 rounded-xl border px-3 py-2.5 transition"
                    :class="form.permissions.includes(p.name)
                        ? 'border-cyan-300/50 bg-cyan-300/10'
                        : 'border-white/10 hover:border-white/25'"
                >
                    <input
                        type="checkbox"
                        class="h-4 w-4 accent-cyan-300"
                        :checked="form.permissions.includes(p.name)"
                        @change="toggle(p.name)"
                    />

                    <span
                        class="font-mono text-xs"
                        :class="form.permissions.includes(p.name)
                            ? 'text-cyan-200'
                            : 'text-white/50'"
                    >
                        {{ p.name }}
                    </span>
                </label>
            </div>

            <p v-if="form.errors.permissions" class="dash-error">
                {{ form.errors.permissions }}
            </p>
        </div>

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
                {{ form.processing
                    ? 'Menyimpan...'
                    : role ? 'Simpan Perubahan' : 'Buat Role'
                }}
            </button>
        </div>
    </form>
</template>
