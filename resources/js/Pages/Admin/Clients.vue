<script setup>
import Admin from './Admin.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

// Recibimos la lista de clientes paginados desde Laravel
const props = defineProps({
    clients: Object // La paginación ya está incluida
})

// Variables para búsqueda
const search = ref('')

// Función para actualizar la lista con filtros y búsqueda
const applyFilters = () => {
    router.get(route('admin.clients'), { search: search.value }, { preserveState: true })
}

// Función para eliminar un cliente
const deleteClient = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este cliente? Esta acción no se puede deshacer.')) {
        router.delete(route('admin.clients.delete', id), { preserveState: true })
    }
}
</script>

<template>
    <Admin>

        <Head title="Gestión de Clientes" />

        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">👥 Gestión de Clientes</h1>

            <!-- Barra de Búsqueda -->
            <div class="flex mb-4 gap-4">
                <input v-model="search" @input="applyFilters" type="text" placeholder="Buscar cliente..."
                    class="border px-3 py-2 rounded w-full">
            </div>

            <!-- Tabla de Clientes -->
            <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-md">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2 text-left">Nombre</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Teléfono</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Empresa</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="client in clients.data" :key="client.id" class="border-b">
                            <td class="border border-gray-300 px-4 py-2">{{ client.user.name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ client.user.email }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ client.telefono || 'N/A' }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ client.empresa || 'No especificada' }}</td>
                            <td class="border border-gray-300 px-4 py-2 flex gap-2">
                                <Link :href="route('admin.clients.show', client.id)"
                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-700">
                                📂 Ver Trabajos
                                </Link>
                                <button @click="deleteClient(client.id)"
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">
                                    🗑 Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="flex justify-center mt-4">
                <button v-if="clients.prev_page_url"
                    @click="router.visit(clients.prev_page_url, { preserveState: true })"
                    class="px-4 py-2 bg-gray-300 rounded mx-1">
                    ⬅️ Anterior
                </button>
                <button v-if="clients.next_page_url"
                    @click="router.visit(clients.next_page_url, { preserveState: true })"
                    class="px-4 py-2 bg-gray-300 rounded mx-1">
                    Siguiente ➡️
                </button>
            </div>
        </div>
    </Admin>
</template>
