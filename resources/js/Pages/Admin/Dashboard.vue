<script setup>
import Admin from './Admin.vue'
import { Head } from '@inertiajs/vue3'

defineProps({
    clients: Array,
    works: Array
})
</script>

<template>
    <Admin>
        <Head title="Panel de Administrador" />
        <h1 class="text-2xl font-bold">Panel de Administración</h1>
    </Admin>
</template>
