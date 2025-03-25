<script setup>
import Admin from './Admin.vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import { ref, defineProps } from 'vue'

const props = defineProps({
    work: Object
})


// Estado del formulario para cambiar el estado del trabajo
const form = useForm({
    estado: props.work.estado
})

// Estado para reasignar el trabajo (opcional)
const reassignAdmin = ref(null)

// Función para cambiar el estado del trabajo
const updateStatus = () => {
    router.put(route('admin.works.updateStatus', props.work.id), { estado: form.estado }, { preserveState: true })
}

// Función para reasignar el trabajo a otro admin
const reassignWork = () => {
    if (!reassignAdmin.value) return

    router.put(route('admin.works.reassign', props.work.id), { assigned_to: reassignAdmin.value }, { preserveState: true })
}
</script>

<template>
    <Admin>

        <Head title="Detalles del Trabajo" />

        <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">


            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Detalles del Trabajo</h1>

                <button @click="window?.history.back()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">
                    ⬅️ Volver Atrás
                </button>
            </div>

            <!-- Información del trabajo -->
            <div class="border-b pb-4 mb-4">
                <p><strong>Título:</strong> {{ work.titulo }}</p>
                <p><strong>Descripción:</strong> {{ work.descripcion }}</p>
                <p><strong>Estado:</strong> {{ work.estado }}</p>
                <p><strong>Cliente:</strong> {{ work.client.user.name }}</p>
                <p><strong>Empresa:</strong> {{ work.client.empresa || 'No especificada' }}</p>
                <p v-if="work.assignedAdmin"><strong>Asignado a:</strong> {{ work.assignedAdmin.name }}</p>
            </div>

            <!-- Archivos adjuntos -->
            <h2 class="text-xl font-semibold mt-4">📂 Archivos Adjuntos</h2>
            <ul v-if="work.archivos.length">
                <li v-for="file in work.archivos" :key="file">
                    <a :href="`/storage/${file}`" target="_blank" class="text-blue-500 hover:underline">{{ file }}</a>
                </li>
            </ul>
            <p v-else class="text-gray-500">No hay archivos adjuntos.</p>

            <!-- Historial de cambios -->
            <h2 class="text-xl font-semibold mt-4">🔄 Historial de Cambios</h2>
            <ul v-if="work.change_requests.length">
                <li v-for="change in work.change_requests" :key="change.id">
                    <p><strong>📝 {{ change.descripcion }}</strong></p>
                    <p v-if="change.archivo">
                        <a :href="`/storage/${change.archivo}`" target="_blank" class="text-blue-500 hover:underline">
                            📂 Ver Archivo
                        </a>
                    </p>
                </li>
            </ul>
            <p v-else class="text-gray-500">No hay cambios registrados.</p>

            <!-- Cambiar estado del trabajo -->
            <h2 class="text-xl font-semibold mt-4">🔄 Cambiar Estado</h2>
            <select v-model="form.estado" class="border p-2 rounded w-full">
                <option value="pendiente">Pendiente</option>
                <option value="en_progreso">En Progreso</option>
                <option value="esperando_confirmacion">Esperando Confirmación</option>
                <option value="finalizado">Finalizado</option>
            </select>
            <button @click="updateStatus" class="mt-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">
                Guardar Cambios
            </button>

            <!-- Reasignar trabajo (Opcional) -->
            <h2 class="text-xl font-semibold mt-4">👷 Reasignar Trabajo</h2>
            <select v-model="reassignAdmin" class="border p-2 rounded w-full">
                <option v-for="admin in work.admins" :key="admin.id" :value="admin.id">
                    {{ admin.name }}
                </option>
            </select>
            <button @click="reassignWork" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
                Asignar a otro Admin
            </button>

        </div>
    </Admin>
</template>
