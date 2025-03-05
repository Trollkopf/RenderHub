<script setup>
import { Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'

// Propiedades que recibe el componente
defineProps({
    work: Object
})
</script>

<template>
    <div class="border rounded-lg p-4 shadow-md bg-white">
        <h2 class="text-lg font-semibold mb-2">{{ work.titulo }}</h2>
        <p class="text-gray-600 text-sm mb-2">{{ work.descripcion }}</p>

        <div class="flex justify-between items-center mt-2">
            <span class="px-2 py-1 text-sm font-semibold rounded"
                :class="{
                    'bg-yellow-200 text-yellow-800': work.estado === 'pendiente',
                    'bg-blue-200 text-blue-800': work.estado === 'en_progreso',
                    'bg-green-200 text-green-800': work.estado === 'finalizado'
                }">
                {{ work.estado }}
            </span>

            <Link :href="route('client.works.show', work.id)" class="text-blue-500 hover:underline">
                Ver detalles →
            </Link>
        </div>
    </div>
</template>
