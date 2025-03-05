<script setup>
import Client from '../Client.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import WorkCard from '../components/WorkCard.vue'
import { ref, computed } from 'vue'

// Recibimos los trabajos paginados como props
const props = defineProps({
    pendingWorks: Object,
    inProgressWorks: Object,
    completedWorks: Object
})

// Función para cambiar de página
const changePage = (url) => {
    if (url) {
        router.get(url) // Inertia navega a la nueva página sin recargar
    }
}

// Controlar la apertura del modal
const showModal = ref(false)

// Formulario para solicitar un nuevo trabajo
const form = useForm({
    titulo: '',
    descripcion: ''
})

// Enviar solicitud de nuevo trabajo
const submitRequest = () => {
    form.post(route('client.works.store'), {
        onSuccess: () => {
            showModal.value = false // Cerrar el modal tras éxito
            form.reset() // Limpiar el formulario
        }
    })
}
</script>

<template>
    <Client>

        <Head title="Mis Trabajos" />
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold mb-4">Mis Trabajos</h1>

            <!-- Botón para solicitar nuevo trabajo -->
            <div class="mb-4">
                <button @click="showModal = true" class="bg-blue-500 text-white px-4 py-2 rounded">
                    ➕ Solicitar Nuevo Trabajo
                </button>
            </div>

            <!-- Sección de Trabajos Pendientes -->
            <div v-if="pendingWorks.data.length">
                <h2 class="text-xl font-semibold mt-4">📌 Trabajos Solicitados</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <WorkCard v-for="work in pendingWorks.data" :key="work.id" :work="work" />
                </div>
                <!-- Paginación -->
                <div class="flex justify-center mt-4">
                    <button v-if="pendingWorks.prev_page_url" @click="changePage(pendingWorks.prev_page_url)"
                        class="px-4 py-2 bg-gray-300 rounded mx-1">
                        ⬅️ Anterior
                    </button>
                    <button v-if="pendingWorks.next_page_url" @click="changePage(pendingWorks.next_page_url)"
                        class="px-4 py-2 bg-gray-300 rounded mx-1">
                        Siguiente ➡️
                    </button>
                </div>
            </div>

            <!-- Sección de Trabajos en Progreso -->
            <div v-if="inProgressWorks.data.length">
                <h2 class="text-xl font-semibold mt-4">🛠️ Trabajos en Progreso</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <WorkCard v-for="work in inProgressWorks.data" :key="work.id" :work="work" />
                </div>
                <div class="flex justify-center mt-4">
                    <button v-if="inProgressWorks.prev_page_url" @click="changePage(inProgressWorks.prev_page_url)"
                        class="px-4 py-2 bg-gray-300 rounded mx-1">
                        ⬅️ Anterior
                    </button>
                    <button v-if="inProgressWorks.next_page_url" @click="changePage(inProgressWorks.next_page_url)"
                        class="px-4 py-2 bg-gray-300 rounded mx-1">
                        Siguiente ➡️
                    </button>
                </div>
            </div>

            <!-- Sección de Trabajos Finalizados -->
            <div v-if="completedWorks.data.length">
                <h2 class="text-xl font-semibold mt-4">✅ Trabajos Finalizados</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <WorkCard v-for="work in completedWorks.data" :key="work.id" :work="work" />
                </div>
                <div class="flex justify-center mt-4">
                    <button v-if="completedWorks.prev_page_url" @click="changePage(completedWorks.prev_page_url)"
                        class="px-4 py-2 bg-gray-300 rounded mx-1">
                        ⬅️ Anterior
                    </button>
                    <button v-if="completedWorks.next_page_url" @click="changePage(completedWorks.next_page_url)"
                        class="px-4 py-2 bg-gray-300 rounded mx-1">
                        Siguiente ➡️
                    </button>
                </div>
            </div>

            <p v-else class="text-gray-500">No tienes trabajos asignados.</p>

            <!-- MODAL PARA SOLICITAR NUEVO TRABAJO -->
            <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                    <h2 class="text-xl font-semibold mb-4">Solicitar Nuevo Trabajo</h2>

                    <form @submit.prevent="submitRequest">
                        <div class="mb-4">
                            <label class="block text-gray-700">Título:</label>
                            <input v-model="form.titulo" type="text" class="border p-2 w-full rounded" required />
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700">Descripción:</label>
                            <textarea v-model="form.descripcion" class="border p-2 w-full rounded" required></textarea>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="showModal = false"
                                class="bg-gray-400 text-white px-4 py-2 rounded">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded"
                                :disabled="form.processing">
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Client>
</template>
