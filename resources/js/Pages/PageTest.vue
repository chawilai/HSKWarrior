<script setup>
import { Head } from '@inertiajs/vue3';
import Scene1 from '@/Components/Scene1.vue';
import OrganicLayout from "@/Layouts/OrganicLayout.vue";
</script>

<template>
    <Head title="Test Page" />
    <OrganicLayout />
    <div class="relative min-h-screen bg-gray-100">
        <Scene1 />
    </div>
    <OrganicLayout />
</template>

<style>
</style>
