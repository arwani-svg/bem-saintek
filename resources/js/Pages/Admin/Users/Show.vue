<script setup>
import { Head } from '@inertiajs/vue3'
import Layout from '@/components/dashboard/Layout.vue'
import ProfileDetail from '@/components/dashboard/ProfileDetail.vue'
import RiwayatUser from '@/components/dashboard/RiwayatUser.vue'

const props = defineProps({
    user: Object,
    riwayatKegiatan: Array,
    tagihanIuran: Array,
    totalPoin: Number,
    totalSisaIuran: Number,
})
</script>

<template>
    <Head :title="`Profile - ${user.name}`" />

    <Layout
        :title="user.name"
        subtitle="Lihat profile lengkap user"
        eyebrow="SAINTEK.OS // USERS"
        activeMenu="users"
        backHref="/users"
    >
        <ProfileDetail
            :profile="user"
            :editHref="`/users/${user.id}/edit`"
            editLabel="Edit User"
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
