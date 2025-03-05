<script setup>
import Client from '../Client.vue'
import { Head } from '@inertiajs/vue3'
import { defineProps } from 'vue'

defineProps({
    work: Object
})
</script>

<template>
    <Client>
        <Head :title="work.titulo" />
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-4">{{ work.titulo }}</h1>
            <p class="text-gray-600 mb-4">{{ work.descripcion }}</p>

            <div class="bg-gray-100 p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold">Archivos:</h2>
                <ul v-if="work.archivos && work.archivos.length">
                    <li v-for="(archivo, index) in work.archivos" :key="index">
                        <a :href="`/storage/${archivo}`" target="_blank" class="text-blue-500 hover:underline">
                            Descargar archivo {{ index + 1 }}
                        </a>
                    </li>
                </ul>
                <p v-else class="text-gray-500">No hay archivos disponibles.</p>
            </div>
        </div>
    </Client>
</template>
