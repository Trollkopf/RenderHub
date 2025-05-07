<script setup>
import { defineProps, defineEmits } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    showRequestModal: Boolean
})

const emit = defineEmits(['close'])

const form = useForm({
    titulo: '',
    descripcion: '',
    archivo: null
})

const handleFileChange = (e) => {
    form.archivo = e.target.files[0]
}

const submitRequest = () => {
    const formData = new FormData()
    formData.append('titulo', form.titulo)
    formData.append('descripcion', form.descripcion)
    if (form.archivo) {
        formData.append('archivo', form.archivo)
    }

    form.post(route('client.works.store'), {
        data: formData,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            emit('close')
            form.reset()
        }
    })
}
</script>

<template>
    <div v-if="showRequestModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-8/12">
            <h2 class="text-xl font-semibold mb-4">Solicitar Nuevo Trabajo</h2>

            <!-- Texto informativo -->
            <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 text-sm rounded border border-yellow-300">
                Recuerda que este trabajo incluye <strong>hasta dos modificaciones gratuitas</strong>. A partir de la tercera modificación se aplicará un coste adicional.
            </div>

            <form @submit.prevent="submitRequest">
                <div class="mb-4">
                    <label class="block text-gray-700">Título:</label>
                    <input v-model="form.titulo" type="text" class="border p-2 w-full rounded" required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Descripción:</label>
                    <textarea v-model="form.descripcion" class="border p-2 w-full rounded" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">📂 Boceto o Referencia (opcional):</label>
                    <input type="file" @change="handleFileChange" class="border p-2 rounded w-full" />
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="emit('close')" class="bg-gray-400 text-white px-4 py-2 rounded">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded" :disabled="form.processing">
                        Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
