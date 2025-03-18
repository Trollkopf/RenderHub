<script setup>
import { defineProps, defineEmits } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    showRequestModal: Boolean
})

const emit = defineEmits(['close'])

const form = useForm({
    titulo: '',
    descripcion: ''
})

const submitRequest = () => {
    form.post(route('client.works.store'), {
        onSuccess: () => {
            emit('close')
            form.reset()
        }
    })
}
</script>

<template>
    <div v-if="showRequestModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
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
