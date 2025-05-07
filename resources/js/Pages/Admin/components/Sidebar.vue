<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { defineProps, computed } from 'vue'

// Recibimos la prop para mostrar u ocultar el menú
defineProps({
    show: Boolean
})

// Obtener la ruta actual para resaltar el menú activo
const currentRoute = computed(() => usePage().url)
</script>

<template>
    <nav :class="show ? 'block' : 'hidden md:block'" class="bg-gray-800 text-white w-64 md:w-48 p-4 min-h-screen">
        <h2 class="text-lg font-semibold mb-4">⚙️ Admin Panel</h2>
        <ul class="space-y-2">
            <li>
                <Link :href="route('admin.dashboard')" class="block p-3 rounded hover:bg-gray-700"
                    :class="{ 'bg-gray-700': currentRoute === '/admin/dashboard' }">
                📊 Dashboard
                </Link>
            </li>
            <li>
                <Link :href="route('admin.clients')" class="block p-3 rounded hover:bg-gray-700"
                    :class="{ 'bg-gray-700': currentRoute === '/admin/clientes' }">
                👥 Clientes
                </Link>
            </li>
            <li>
                <Link :href="route('admin.works')" class="block p-3 rounded hover:bg-gray-700"
                    :class="{ 'bg-gray-700': currentRoute === '/admin/trabajos' }">
                📂 Trabajos
                </Link>
            </li>
            <Link :href="route('admin.my-works')" class="block p-3 rounded hover:bg-gray-700"
                :class="{ 'bg-gray-700': currentRoute === '/admin/mis-trabajos' }">
            🧑‍💼 Mis Trabajos
            </Link>
            <li>
                <Link :href="route('admin.works.archived')" class="block p-2 hover:bg-gray-600">
                🗃️ Archivados
                </Link>
            </li>
            <li>
                <Link :href="route('admin.calendar')" class="block p-2 hover:bg-gray-600">🗓️ Calendario</Link>
            </li>
            <li>
                <Link :href="route('notifications.index')" class="block p-2 hover:bg-gray-600">🔔 Notificaciones</Link>
            </li>

            <li>
                <Link :href="route('settings.index')" class="block p-3 rounded hover:bg-gray-700"
                    :class="{ 'bg-gray-700': currentRoute === '/admin/settings' }">
                ⚙️ Configuración
                </Link>
            </li>
            <li class="mt-4 border-t border-gray-600 pt-2">
                <Link :href="route('logout')" method="post" class="block p-3 rounded hover:bg-red-700">
                🚪 Cerrar Sesión
                </Link>
            </li>
        </ul>
    </nav>
</template>
