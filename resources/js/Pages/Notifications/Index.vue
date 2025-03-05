<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

defineProps({
    notifications: Array
})
</script>

<template>
    <Head title="Notificaciones" />
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Notificaciones</h1>

        <ul>
            <li v-for="notification in notifications" :key="notification.id" class="border p-2 my-2">
                <span :class="{'font-bold': !notification.leido}">
                    {{ notification.mensaje }}
                </span>
                <Link :href="route('notifications.markAsRead', notification.id)" method="post" class="ml-2 text-blue-500">
                    Marcar como leído
                </Link>
            </li>
        </ul>
    </div>
</template>
