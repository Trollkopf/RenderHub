<script setup>
import { defineProps } from 'vue'

defineProps({
    user: Object
})
</script>

<template>
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-semibold mb-2">Información del Usuario</h2>
        <p><strong>Nombre:</strong> {{ user.name }}</p>
        <p><strong>Email:</strong> {{ user.email }}</p>
        <p v-if="user.client">
            <strong>Teléfono:</strong> {{ user.client.telefono }} <br />
            <strong>Empresa:</strong> {{ user.client.empresa || 'No especificada' }} <br />
            <strong>Dirección:</strong> {{ user.client.direccion || 'No especificada' }}
        </p>
    </div>
</template>
