<script setup>
import { router } from '@inertiajs/vue3'
import ModalPage from '@/components/dashboard/ModalPage.vue'
import DepartemenForm from './DepartemenForm.vue'

defineProps({
    user: Object,
})

const goBack = () => {
    router.visit('/departemen')
}
</script>

<template>
    <ModalPage
        title="Tambah Departemen"
        subtitle="Buat departemen baru BEM SAINTEK"
        eyebrow="SAINTEK.OS // DEPARTEMEN"
        size="lg"
        backHref="/departemen"
    >
        <!-- Fallback halaman langsung — alur utama sudah pop-up di Index -->
        <DepartemenForm :departemen="null" @success="goBack" @cancel="goBack" />
    </ModalPage>
</template>
