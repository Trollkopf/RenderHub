<script setup>
import Client from './Client.vue'
import { Head } from '@inertiajs/vue3'
import UserInfo from './components/UserInfo.vue'
import WorkList from './components/WorkList.vue'
import WorkNotifications from './components/WorkNotifications.vue'

defineProps({
    user: Object,
    works: Array,
    notifications: Array,
})
</script>

<template>
    <Client>

        <Head title="Panel de Cliente" />
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">Bienvenido, {{ user.name }}</h1>

            <!-- Información del Usuario -->
            <UserInfo :user="user" />

            <!-- Indicador de Novedades -->
            <WorkNotifications :notifications="notifications"/>

            <!-- Listado de Trabajos -->
            <WorkList :works="works" />
        </div>
    </Client>
</template>
