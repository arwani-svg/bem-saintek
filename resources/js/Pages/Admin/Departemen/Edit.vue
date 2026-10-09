<script setup>
import { router } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'
import DepartemenForm from './DepartemenForm.vue'

const props = defineProps({
    departemen: Object,
    user: Object,
})

const goBack = () => {
    router.visit('/departemen')
}
</script>

<template>
    <ModalPage
        title="Edit Departemen"
        :subtitle="`Perbarui data ${departemen.nama}`"
        eyebrow="SAINTEK.OS // DEPARTEMEN"
        size="lg"
        backHref="/departemen"
    >
        <!-- Fallback halaman langsung — alur utama sudah pop-up di Index -->
        <DepartemenForm :departemen="departemen" @success="goBack" @cancel="goBack" />
    </ModalPage>
</template>
