<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col items-center justify-center">
        <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Welcome to Laravel
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                You have successfully set up Laravel 13 with Inertia.js, Vue 3, and Tailwind CSS.
            </p>
            <Link
                href="/"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
            >
                Home
            </Link>
        </div>
    </div>
</template>
