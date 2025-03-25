<script setup>
import Admin from '../Admin/Admin.vue'
import Client from '../Client/Client.vue'

import { Head, router } from '@inertiajs/vue3'
import { computed } from 'vue'


const props = defineProps({
    notifications: Object,
    auth: Object
})

const markAsRead = (id) => {
    router.put(route('notifications.read', id))
}

const markAllAsRead = () => {
    router.put(route('notifications.readAll'))
}

const isAdmin = computed(() => props.auth.user?.role === 'admin')

const goToWork = (workId, notificationId = null) => {
    if (notificationId) {
        router.put(route('notifications.read', notificationId), {}, {
            onSuccess: () => {
                router.get(route(isAdmin.value ? 'admin.works.show' : 'client.works.show', workId))
            }
        })
    } else {
        router.get(route(isAdmin.value ? 'admin.works.show' : 'client.works.show', workId))
    }
}

</script>

<template>
    <component :is="isAdmin ? Admin : Client">
        <div class="container mx-auto p-6">

            <Head title="Notificaciones" />
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">🔔 Notificaciones</h1>

                <button @click="markAllAsRead"
                    class="text-sm bg-gray-200 px-3 py-1 rounded hover:bg-gray-300 text-gray-800"
                    v-if="props.notifications.data.some(n => !n.leido)">
                    🧹 Marcar todas como leídas
                </button>
            </div>

            <div v-if="props.notifications.data.length">
                <ul class="space-y-4">
                    <li v-for="n in notifications.data" :key="n.id"
                        class="p-4 border rounded bg-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
                        :class="{ 'opacity-60': n.leido }">
                        <!-- Contenido de la notificación -->
                        <div>
                            <p>{{ n.mensaje }}</p>
                            <p class="text-xs text-gray-500">{{ new Date(n.created_at).toLocaleString() }}</p>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex flex-wrap gap-2">
                            <!-- Ver trabajo -->
                            <button v-if="n.work_id" @click="goToWork(n.work_id, n.id)"
                                class="px-3 py-1 text-sm bg-indigo-500 text-white rounded hover:bg-indigo-600">
                                🔍 Ver Trabajo
                            </button>

                            <!-- Marcar como leído -->
                            <button v-if="!n.leido" @click="markAsRead(n.id)"
                                class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600">
                                ✅ Marcar como Leída
                            </button>

                            <!-- Eliminar -->
                            <button @click="router.delete(route('notifications.destroy', n.id))"
                                class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                                🗑 Eliminar
                            </button>
                        </div>
                    </li>
                </ul>
                <div class="flex justify-center mt-6 space-x-2" v-if="props.notifications.links.length > 3">
                    <button v-for="link in props.notifications.links" :key="link.label" :disabled="!link.url"
                        @click="link.url && router.get(link.url)" v-html="link.label" class="px-3 py-1 rounded" :class="{
                            'bg-blue-500 text-white': link.active,
                            'bg-gray-200 text-gray-700 hover:bg-gray-300': !link.active
                        }" />
                </div>
            </div>

            <p v-else class="text-gray-500">No tienes notificaciones.</p>
        </div>
    </component>
</template>
