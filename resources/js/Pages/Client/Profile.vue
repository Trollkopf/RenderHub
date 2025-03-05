<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { watch } from 'vue'
import Client from './Client.vue'

// Recibimos los datos del cliente como `props`
const props = defineProps({
    client: Object,
    user: Object // Ahora también pasamos los datos del usuario
})

// Inicializamos el formulario con valores vacíos
const form = useForm({
    name: '',
    email: '',
    empresa: '',
    telefono: '',
    direccion: ''
})

// Cuando `props.client` y `props.user` estén disponibles, actualizamos el formulario
watch([() => props.client, () => props.user], ([newClient, newUser]) => {
    if (newClient && newUser) {
        form.name = newUser.name || ''
        form.email = newUser.email || ''
        form.empresa = newClient.empresa || ''
        form.telefono = newClient.telefono || ''
        form.direccion = newClient.direccion || ''
    }
}, { immediate: true }) // `immediate: true` para que se ejecute al montar el componente

// Función para enviar los cambios al backend
const submit = () => {
    form.put(route('client.profile.update'))
}
</script>

<template>
    <Client>
        <Head title="Editar Perfil" />

        <div v-if="client && user" class="container mx-auto p-6">
            <h1 class="text-2xl font-bold mb-4">Editar Perfil</h1>

            <form @submit.prevent="submit" class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-4">
                    <label class="block text-gray-700">Nombre:</label>
                    <input v-model="form.name" type="text" class="border p-2 w-full rounded" required />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Correo Electrónico:</label>
                    <input v-model="form.email" type="email" class="border p-2 w-full rounded" required />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Empresa:</label>
                    <input v-model="form.empresa" type="text" class="border p-2 w-full rounded" />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Teléfono:</label>
                    <input v-model="form.telefono" type="text" class="border p-2 w-full rounded" required />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Dirección:</label>
                    <textarea v-model="form.direccion" class="border p-2 w-full rounded"></textarea>
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded" :disabled="form.processing">
                    Guardar Cambios
                </button>
            </form>

            <p v-if="form.hasErrors" class="text-red-500 mt-2">Corrige los errores antes de enviar.</p>
            <p v-if="form.recentlySuccessful" class="text-green-500 mt-2">Perfil actualizado correctamente.</p>
        </div>

        <div v-else class="container mx-auto p-6">
            <p class="text-gray-500">Cargando datos del perfil...</p>
        </div>
    </Client>
</template>
