<script setup>
import { defineProps, defineEmits, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
    showReviewModal: Boolean,
    selectedWork: Object
})

const emit = defineEmits(['close'])

const rejectForm = useForm({
    descripcion: '',
    archivo: null
})

// Aceptar trabajo
const acceptWork = () => {
    if (!props.selectedWork) return
    router.post(route('client.works.review', props.selectedWork.id), { action: 'accept' }, { preserveState: true })
}

// Rechazar trabajo con cambios
const rejectWork = () => {
    if (!props.selectedWork) return

    const formData = new FormData()
    formData.append('action', 'reject')
    formData.append('descripcion', rejectForm.descripcion)
    if (rejectForm.archivo) {
        formData.append('archivo', rejectForm.archivo)
    }

    router.post(route('client.works.review', props.selectedWork.id), formData, {
        preserveState: true,
        forceFormData: true
    })

    emit('close')
}
</script>

<template>
    <div v-if="showReviewModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-md w-[600px]">
            <h2 class="text-2xl font-bold mb-3">Detalles del Trabajo</h2>
            <p><strong>Título:</strong> {{ selectedWork.titulo }}</p>
            <p><strong>Descripción:</strong> {{ selectedWork.descripcion }}</p>

            <h3 class="mt-4 font-semibold">📂 Archivos Adjuntos:</h3>
            <ul v-if="selectedWork.archivos.length">
                <li v-for="file in selectedWork.archivos" :key="file">
                    <a :href="`/storage/${file}`" target="_blank" class="text-blue-500 hover:underline">{{ file }}</a>
                </li>
            </ul>

            <h3 class="mt-6 font-semibold">🔄 Historial de Cambios</h3>
            <ul v-if="selectedWork.change_requests.length">
                <li v-for="change in selectedWork.change_requests" :key="change.id">
                    <p><strong>📝 {{ change.descripcion }}</strong></p>
                    <p v-if="change.archivo">
                        <a :href="`/storage/${change.archivo}`" target="_blank" class="text-blue-500 hover:underline">
                            📂 Ver Archivo
                        </a>
                    </p>
                </li>
            </ul>

            <div class="mt-6 flex justify-between">
                <button v-if="selectedWork.change_requests.length >= 3"
                    class="mr-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Ponte en contacto para
                    solicitar nuevos cambios.</button>
                <button v-if="selectedWork.change_requests.length < 3" @click="rejectWork"
                    class="mr-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">
                    ❌ Rechazar ({{ selectedWork.change_requests.length }}/3)
                </button>
                <button @click="acceptWork" class="mr-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">
                    ✅ Aceptar
                </button>



                <button @click="emit('close')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">
                    ❌ Cerrar
                </button>
            </div>
        </div>
    </div>
</template>
