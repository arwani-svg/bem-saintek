<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'

const props = defineProps({
    role: Object, // {id, name, permissions[], is_protected}
    allPermissions: Array, // [{id, name}]
    user: Object,
})

const form = useForm({
    name: props.role.name,
    permissions: [...props.role.permissions],
})

const toggle = (perm) => {
    const i = form.permissions.indexOf(perm)
    if (i >= 0) form.permissions.splice(i, 1)
    else form.permissions.push(perm)
}

const checkAll = () => {
    form.permissions = props.allPermissions.map((p) => p.name)
}

const uncheckAll = () => {
    form.permissions = []
}

const submit = () => {
    form.put(`/roles/${props.role.id}`)
}
</script>

<template>
    <ModalPage
        :title="`Atur Role ${role.name}`"
        :subtitle="role.is_protected ? 'Role sistem: nama dikunci, permission tetap bisa diubah.' : 'Centang permission yang boleh diakses role ini.'"
        eyebrow="SAINTEK.OS // ACCESS"
        size="lg"
        backHref="/roles"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <div>
                    <label class="dash-label">Nama role *</label>
                    <input v-model="form.name" type="text" class="dash-input font-mono" :disabled="role.is_protected" required />
                    <p v-if="form.errors.name" class="dash-error">{{ form.errors.name }}</p>
                </div>

                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <label class="dash-label !mb-0">Permissions ({{ form.permissions.length }}/{{ allPermissions.length }})</label>
                        <div class="flex gap-2">
                            <button type="button" class="dash-btn-ghost !py-1.5 !text-xs" @click="checkAll">Pilih semua</button>
                            <button type="button" class="dash-btn-ghost !py-1.5 !text-xs" @click="uncheckAll">Hapus semua</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <label
                            v-for="p in allPermissions" :key="p.id"
                            class="flex cursor-pointer items-center gap-3 rounded-xl border px-3 py-2.5 transition"
                            :class="form.permissions.includes(p.name) ? 'border-cyan-300/50 bg-cyan-300/10' : 'border-white/10 hover:border-white/25'"
                        >
                            <input type="checkbox" class="h-4 w-4 accent-cyan-300" :checked="form.permissions.includes(p.name)" @change="toggle(p.name)" />
                            <span class="font-mono text-xs" :class="form.permissions.includes(p.name) ? 'text-cyan-200' : 'text-white/50'">{{ p.name }}</span>
                        </label>
                    </div>
                    <p v-if="form.errors.permissions" class="dash-error">{{ form.errors.permissions }}</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                <Link href="/roles" class="dash-btn-ghost">Batal</Link>
                <button type="submit" :disabled="form.processing" class="dash-btn-primary">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
            </div>
        </form>
    </ModalPage>
</template>
