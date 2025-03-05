<script setup>
import { defineProps, ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
    works: Object, // Paginación incluida

})

// Variables para búsqueda y filtro
const search = ref('')
const status = ref('')

// Función para actualizar la lista con filtros y búsqueda
const applyFilters = () => {
    router.get(route('client.dashboard'), { search: search.value, status: status.value }, { preserveState: true })
}

// Función para cambiar de página
const changePage = (url) => {
    if (url) {
        router.visit(url, { preserveState: true }) // Mantiene filtros al cambiar de página
    }
}

// Función para asignar clases según el estado
const getStatusClass = (status) => {
    return {
        'pendiente': 'bg-yellow-200 text-yellow-800',
        'en_progreso': 'bg-blue-200 text-blue-800',
        'finalizado': 'bg-green-200 text-green-800'
    }[status] || 'bg-gray-200 text-gray-800'
}

</script>

<template>
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-semibold mb-4">Tus Trabajos</h2>

        <!-- Filtros y Búsqueda -->
        <div class="flex gap-4 mb-4">
            <input v-model="search" @input="applyFilters" type="text" placeholder="Buscar por título..."
                class="border px-3 py-2 rounded w-full">
            <select v-model="status" @change="applyFilters" class="border px-3 py-2 rounded">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_progreso">En Progreso</option>
                <option value="finalizado">Finalizado</option>
            </select>
        </div>

        <!-- Tabla de Trabajos -->
        <div v-if="works.length">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2 text-left">Título</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Estado</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="work in works" :key="work.id" class="border-b">
                            <td class="border border-gray-300 px-4 py-2">{{ work.titulo }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span :class="`px-3 py-1 rounded ${getStatusClass(work.estado)}`">
                                    {{ work.estado }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <Link :href="route('client.works.show', work.id)" class="text-blue-500 hover:underline">
                                    Ver →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="flex justify-center mt-4">
                <button v-if="works.prev_page_url" @click="changePage(works.prev_page_url)" class="px-4 py-2 bg-gray-300 rounded mx-1">
                    ⬅️ Anterior
                </button>
                <button v-if="works.next_page_url" @click="changePage(works.next_page_url)" class="px-4 py-2 bg-gray-300 rounded mx-1">
                    Siguiente ➡️
                </button>
            </div>
        </div>

        <p v-else class="text-gray-500">No hay trabajos disponibles con estos filtros.</p>
    </div>
</template>
