<script setup>
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
    notifications: Array
})

const markAsRead = (id) => {
    router.put(route('notifications.read', id))
}

const markAllAsRead = () => {
  router.put(route('notifications.readAll'))
}

</script>

<template>
    <div class="container mx-auto p-6">

        <Head title="Notificaciones" />
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">🔔 Notificaciones</h1>

            <button @click="markAllAsRead" class="text-sm bg-gray-200 px-3 py-1 rounded hover:bg-gray-300 text-gray-800"
                v-if="props.notifications.some(n => !n.leido)">
                🧹 Marcar todas como leídas
            </button>
        </div>

        <div v-if="props.notifications.length">
            <ul class="space-y-4">
                <li v-for="n in props.notifications" :key="n.id"
                    class="p-4 border rounded bg-white flex justify-between items-center"
                    :class="{ 'opacity-60': n.leido }">
                    <span>{{ n.mensaje }}</span>
                    <button v-if="!n.leido" @click="markAsRead(n.id)" class="text-sm text-blue-600 hover:underline">
                        Marcar como leída
                    </button>
                </li>
            </ul>
        </div>

        <p v-else class="text-gray-500">No tienes notificaciones.</p>
    </div>
</template>
