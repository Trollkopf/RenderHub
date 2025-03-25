<script setup>
import { defineEmits } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { onClickOutside } from '@vueuse/core'
import { ref } from 'vue'

const page = usePage()
const showDropdown = ref(false)
const dropdownRef = ref(null)

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value
}

const markAllAsRead = () => {
    router.put(route('notifications.readAll'))
}

onClickOutside(dropdownRef, () => {
    showDropdown.value = false
})

const emit = defineEmits(['toggleMenu'])
</script>

<template>
    <header class="bg-gray-900 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">RenderHub Admin</h1>
        <div class="flex items-center space-x-4">
            <span class="text-sm font-semibold">{{ page.props.auth.user.name }}</span>

            <div class="relative inline-block text-left" ref="dropdownRef">
                <!-- Botón de notificaciones -->
                <button @click="toggleDropdown" class="text-2xl hover:text-yellow-400 relative">
                    🔔
                    <span v-if="page.props.auth.notifications_count > 0"
                        class="absolute -top-1 -right-2 bg-red-600 text-white text-xs rounded-full px-1">
                        {{ page.props.auth.notifications_count }}
                    </span>
                </button>

                <!-- DROPDOWN -->
                <div v-if="showDropdown" class="absolute right-0 mt-2 w-64 bg-white border rounded shadow-lg z-50">
                    <div class="p-3 border-b font-bold flex justify-between items-center">
                        Notificaciones
                        <button v-if="page.props.auth.notifications_count > 0"
                            class="text-xs text-blue-500 hover:underline" @click="markAllAsRead">
                            Marcar todas
                        </button>
                    </div>

                    <ul v-if="page.props.auth.notifications?.length">
                        <li v-for="notif in page.props.auth.notifications" :key="notif.id"
                            class="px-4 py-2 border-b text-sm text-gray-900 hover:bg-gray-100">
                            <Link :href="route('notifications.view', notif.id)">
                            <span>{{ notif.mensaje }}</span><br>
                            <span class="text-xs text-gray-500">{{ new Date(notif.created_at).toLocaleString() }}</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        <button @click="emit('toggleMenu')" class="md:hidden">☰</button>
    </header>
</template>
