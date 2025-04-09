<script setup>
import Admin from './Admin.vue'

import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { defineProps, ref } from 'vue'

// Recibimos los datos desde el backend
const props = defineProps({
    pendingWorks: Array,
    inProgressWorks: Array,
    waitingConfirmationWorks: Array,
    completedWorks: Array,
    admins: Array
})

const toastMessage = ref('')
const showToast = ref(false)

const showToastNotification = (message) => {
    toastMessage.value = message
    showToast.value = true
    setTimeout(() => {
        showToast.value = false
        toastMessage.value = ''
    }, 3000)
}


// Estado para manejar el modal de detalles/asignación
const selectedWork = ref(null)
const selectedAdmin = ref(null)
const dueDate = ref(null)
const showModal = ref(false)

const works = ref({
    pendiente: [...props.pendingWorks],
    en_progreso: [...props.inProgressWorks],
    esperando_confirmacion: [...props.waitingConfirmationWorks],
    finalizado: [...props.completedWorks]
})

// Función para abrir el modal
const openModal = (work) => {
    selectedWork.value = work
    selectedAdmin.value = work.assigned_to || null
    dueDate.value = work.due_date || null
    showModal.value = true
}

// Función para asignar trabajo a un administrador y/o definir fecha de vencimiento
const assignWork = () => {
    if (!selectedWork.value) return

    router.put(route('admin.works.reassign', selectedWork.value.id), {
        assigned_to: selectedAdmin.value,
        due_date: dueDate.value
    }, {
        preserveState: true,
        onSuccess: () => {
            closeModal()
            showToastNotification('✅ Trabajo asignado correctamente')
        },
        onError: () => {
            showToastNotification('❌ Hubo un error', 'error')
        }
    })
}

// Función para cerrar el modal
const closeModal = () => {
    showModal.value = false
    selectedWork.value = null
}

// Función para cambiar el estado de un trabajo con Drag & Drop
// Función para comenzar a arrastrar
const dragStart = (event, work) => {
    if (!work || !work.id) {
        console.error('Error: Trabajo no definido en dragStart()', work);
        return;
    }
    event.dataTransfer.setData('workId', work.id);
};

// Función para soltar un trabajo en otra columna
const dropWork = (event, newStatus) => {
    event.preventDefault()
    const workId = event.dataTransfer.getData('workId')

    // Buscar el trabajo en la columna original
    let movedWork = null
    Object.keys(works.value).forEach((status) => {
        const index = works.value[status].findIndex(work => work.id == workId)
        if (index !== -1) {
            movedWork = works.value[status].splice(index, 1)[0] // Remover de la columna actual
        }
    })

    // Si el trabajo fue encontrado y el estado cambió, actualizar
    if (movedWork && movedWork.estado !== newStatus) {
        movedWork.estado = newStatus
        works.value[newStatus].push(movedWork) // Agregar a la nueva columna

        // Enviar actualización al servidor
        router.put(route('admin.works.updateStatus', movedWork.id), { estado: newStatus }, { preserveState: true })
    }
}

</script>

<template>
    <Admin>

        <Head title="Gestión de Trabajos" />

        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">🗂 Tablero Kanban de Trabajos</h1>

            <!-- VISTA KANBAN -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div v-for="(columnWorks, status) in {
                    pendiente: pendingWorks,
                    en_progreso: inProgressWorks,
                    esperando_confirmacion: waitingConfirmationWorks,
                    finalizado: completedWorks
                }" :key="status" class="p-4 rounded-lg shadow-md min-h-[300px]" :class="{
                    'bg-yellow-100': status === 'pendiente',
                    'bg-blue-100': status === 'en_progreso',
                    'bg-orange-100': status === 'esperando_confirmacion',
                    'bg-green-100': status === 'finalizado'
                }" @dragstart="dragStart($event, work)" @dragover.prevent @drop="dropWork($event, status)">

                    <h2 class="text-xl font-semibold mb-3">
                        {{
                            status === 'pendiente' ? '📌 Pendientes' :
                                status === 'en_progreso' ? '🛠️ En Progreso' :
                                    status === 'esperando_confirmacion' ? '⏳ Esperando Confirmación' :
                                        '✅ Finalizados'
                        }}
                    </h2>

                    <div v-if="columnWorks.length">
                        <div v-for="work in columnWorks" :key="work.id" draggable="true"
                            class="p-3 rounded shadow mb-2 bg-gray-200 cursor-pointer"
                            @dragstart="(event) => dragStart(event, work)" @click=" openModal(work)">
                            <p class="font-semibold">{{ work.titulo }}</p>
                            <p class="text-gray-700 text-sm">👤 {{ work.client.user.name }}</p>
                            <p class="text-gray-500 text-xs">🏢 {{ work.client.empresa || 'No especificada' }}</p>
                            <p v-if="work.assignedAdmin" class="text-blue-700 text-xs">👷 Asignado a: {{
                                work.assignedAdmin.name }}</p>
                        </div>
                    </div>

                    <p v-else class="text-gray-500">No hay trabajos en esta categoría.</p>
                </div>
            </div>

            <!-- MODAL PARA DETALLES Y ASIGNACIÓN -->
            <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white p-6 rounded-lg shadow-md w-[90%]">
                    <h2 class="text-2xl font-bold mb-3">Detalles del Trabajo</h2>
                    <p><strong>Título:</strong> {{ selectedWork.titulo }}</p>
                    <p><strong>Descripción:</strong> {{ selectedWork.descripcion }}</p>
                    <p><strong>Cliente:</strong> {{ selectedWork.client.user.name }}</p>
                    <p><strong>Empresa:</strong> {{ selectedWork.client.empresa || 'No especificada' }}</p>

                    <h3 class="mt-4 font-semibold">📂 Archivos Adjuntos:</h3>
                    <ul v-if="selectedWork.archivos && selectedWork.archivos.length">
                        <li v-for="file in selectedWork.archivos" :key="file">
                            <a :href="`/storage/${file}`" target="_blank" class="text-blue-500 hover:underline">{{ file
                            }}</a>
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
                        <button @click="assignWork"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">
                            Asignar
                        </button>
                        <button @click="closeModal" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showToast"
            class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 transition-all duration-300">
            {{ toastMessage }}
        </div>
    </Admin>
</template>
