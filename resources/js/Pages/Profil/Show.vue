<script setup>
import { Head } from '@inertiajs/vue3'
import Layout from '@/components/dashboard/Layout.vue'
import ProfileDetail from '@/components/dashboard/ProfileDetail.vue'
import RiwayatUser from '@/components/dashboard/RiwayatUser.vue'

const props = defineProps({
    profile: Object,
    canEditPrestasi: Boolean,
    user: Object,
    riwayatKegiatan: Array,
    tagihanIuran: Array,
    totalPoin: Number,
    totalSisaIuran: Number,
})
</script>

<template>
    <Head title="Profil Saya" />

    <Layout
        :title="profile.name"
        subtitle="Informasi lengkap data dirimu"
        eyebrow="SAINTEK.OS // PROFIL"
        activeMenu="profil"
        backHref="/dashboard"
        :user="user"
    >
        <ProfileDetail
            :profile="profile"
            editHref="/profil/edit"
            editLabel="Edit Profil"
            :prestasiNote="canEditPrestasi ? '' : 'Prestasi hanya bisa diubah pengurus — hubungi admin bila ada pembaruan.'"
        />

        <div class="mt-6">
            <RiwayatUser
                :riwayatKegiatan="riwayatKegiatan"
                :tagihanIuran="tagihanIuran"
                :totalPoin="totalPoin"
                :totalSisaIuran="totalSisaIuran"
            />
        </div>
    </Layout>
</template>
