<script setup>
import { Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'

defineProps({
    show: Boolean
})
</script>

<template>
    <nav :class="show ? 'block' : 'hidden md:block'" class="bg-gray-800 text-white w-64 md:w-48 p-4 min-h-screen">
        <ul>
            <li><Link :href="route('client.dashboard')" class="block p-2 hover:bg-gray-700">📊 Panel de Control</Link></li>
            <li><Link :href="route('client.works')" class="block p-2 hover:bg-gray-700">📂 Trabajos</Link></li>
            <li><Link :href="route('client.profile')" class="block p-2 hover:bg-gray-700">👤 Perfil</Link></li>
            <li><Link :href="route('notifications.index')" class="block p-2 hover:bg-gray-600">🔔 Notificaciones</Link></li>
            <li class="mt-4 border-t pt-2"><Link :href="route('logout')" method="post" class="block p-2 hover:bg-red-700">🚪 Cerrar Sesión</Link></li>
        </ul>
    </nav>
</template>
