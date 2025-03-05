<script setup>
import Admin from './Admin.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
    client: Object
})

// Función para eliminar cliente con confirmación
const deleteClient = () => {
    if (confirm('⚠️ ¿Seguro que deseas eliminar este cliente? Esto no se puede deshacer.')) {
        router.delete(route('admin.clients.delete', props.client.id), { preserveState: true })
    }
}

// Función para obtener el color del estado del trabajo
const getStatusClass = (status) => {
    return {
        'pendiente': 'bg-yellow-200 text-yellow-800',
        'en_progreso': 'bg-blue-200 text-blue-800',
        'finalizado': 'bg-green-200 text-green-800'
    }[status] || 'bg-gray-200 text-gray-800'
}
</script>

<template>
    <Admin>
        <Head :title="`Detalles de ${client.user.name}`" />

        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">👤 Detalles del Cliente</h1>

            <!-- Información del Cliente -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-2">{{ client.user.name }}</h2>
                <p><strong>Email:</strong> {{ client.user.email }}</p>
                <p><strong>Teléfono:</strong> {{ client.telefono || 'No especificado' }}</p>
                <p><strong>Empresa:</strong> {{ client.empresa || 'No especificada' }}</p>
                <p><strong>Dirección:</strong> {{ client.direccion || 'No especificada' }}</p>
            </div>

            <!-- Trabajos del Cliente -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h2 class="text-xl font-semibold mb-2">📂 Trabajos de {{ client.user.name }}</h2>

                <div v-if="client.works.length">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2 text-left">Título</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Estado</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="work in client.works" :key="work.id" class="border-b">
                                <td class="border border-gray-300 px-4 py-2">{{ work.titulo }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <span :class="`px-3 py-1 rounded ${getStatusClass(work.estado)}`">
                                        {{ work.estado }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <Link :href="route('admin.works.show', work.id)" class="text-blue-500 hover:underline">
                                        Ver →
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p v-else class="text-gray-500">Este cliente no tiene trabajos asignados.</p>
            </div>
        </div>
    </Admin>
</template>
