<script setup>
import Admin from './Admin.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { defineProps, ref } from 'vue'

// Recibimos los datos desde el backend
const props = defineProps({
    pendingWorks: Array,
    inProgressWorks: Array,
    completedWorks: Array,
    admins: Array // Lista de administradores disponibles
})

// Estado para manejar la asignación de trabajos
const selectedWork = ref(null)
const selectedAdmin = ref(null)
const dueDate = ref(null)
const showModal = ref(false)

// Función para abrir el modal con la información del trabajo
const openModal = (work) => {
    selectedWork.value = work
    selectedAdmin.value = work.assigned_to || null
    dueDate.value = work.due_date || null
    showModal.value = true
}

// Función para cerrar el modal
const closeModal = () => {
    showModal.value = false
    selectedWork.value = null
}

// Función para asignar un trabajo a un admin
const assignWork = () => {
    if (!selectedWork.value) return

    router.put(route('admin.works.assign', selectedWork.value.id), {
        assigned_to: selectedAdmin.value,
        due_date: dueDate.value
    }, { preserveState: true })

    showModal.value = false
}
</script>

<template>
    <Admin>
        <Head title="Gestión de Trabajos" />

        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">🗂 Tablero Kanban de Trabajos</h1>

            <!-- VISTA KANBAN -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="(columnWorks, status) in { pendiente: pendingWorks, en_progreso: inProgressWorks, finalizado: completedWorks }"
                    :key="status"
                    class="p-4 rounded-lg shadow-md min-h-[300px]"
                    :class="{ 'bg-yellow-100': status === 'pendiente', 'bg-blue-100': status === 'en_progreso', 'bg-green-100': status === 'finalizado' }">

                    <h2 class="text-xl font-semibold mb-3">
                        {{ status === 'pendiente' ? '📌 Pendientes' : status === 'en_progreso' ? '🛠️ En Progreso' : '✅ Finalizados' }}
                    </h2>

                    <div v-if="columnWorks.length">
                        <div v-for="work in columnWorks" :key="work.id"
                            class="p-3 rounded shadow mb-2 bg-gray-200 cursor-pointer"
                            @click="openModal(work)">
                            <p class="font-semibold">{{ work.titulo }}</p>
                            <p class="text-gray-700 text-sm">👤 {{ work.client.user.name }}</p>
                            <p class="text-gray-500 text-xs">🏢 {{ work.client.empresa || 'No especificada' }}</p>
                            <p v-if="work.assignedAdmin" class="text-blue-700 text-xs">👷 Asignado a: {{ work.assignedAdmin.name }}</p>
                        </div>
                    </div>

                    <p v-else class="text-gray-500">No hay trabajos en esta categoría.</p>
                </div>
            </div>

            <!-- MODAL PARA DETALLES Y ASIGNACIÓN -->
            <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white p-6 rounded-lg shadow-md w-[600px]">
                    <h2 class="text-2xl font-bold mb-3">Detalles del Trabajo</h2>
                    <p><strong>Título:</strong> {{ selectedWork.titulo }}</p>
                    <p><strong>Descripción:</strong> {{ selectedWork.descripcion }}</p>
                    <p><strong>Cliente:</strong> {{ selectedWork.client.user.name }}</p>
                    <p><strong>Empresa:</strong> {{ selectedWork.client.empresa || 'No especificada' }}</p>

                    <h3 class="mt-4 font-semibold">📂 Archivos Adjuntos:</h3>
                    <ul v-if="selectedWork.archivos && selectedWork.archivos.length">
                        <li v-for="file in selectedWork.archivos" :key="file">
                            <a :href="`/storage/${file}`" target="_blank" class="text-blue-500 hover:underline">{{ file }}</a>
                        </li>
                    </ul>
                    <p v-else class="text-gray-500">No hay archivos adjuntos.</p>

                    <!-- Asignación de trabajo -->
                    <h3 class="mt-6 text-xl font-semibold">🔧 Asignar Trabajo</h3>
                    <label class="block mt-4">📅 Fecha de Vencimiento</label>
                    <input type="date" v-model="dueDate" class="border p-2 rounded w-full">

                    <label class="block mt-4">👷 Asignar a:</label>
                    <select v-model="selectedAdmin" class="border p-2 rounded w-full">
                        <option :value="null">Sin Asignar</option>
                        <option v-for="admin in props.admins" :key="admin.id" :value="admin.id">
                            {{ admin.name }}
                        </option>
                    </select>

                    <div class="mt-6 flex justify-between">
                        <button @click="assignWork" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">
                            Asignar
                        </button>
                        <button @click="closeModal" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Admin>
</template>
