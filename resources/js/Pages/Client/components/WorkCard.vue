<script setup>
import { Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'

// Propiedades que recibe el componente
defineProps({
    work: Object,
    openReviewModal: Function
})
</script>

<template>
    <div class="border rounded-lg p-4 shadow-md bg-white">
        <h2 class="text-lg font-semibold mb-2">{{ work.titulo }}</h2>
        <p class="text-gray-600 text-sm mb-2">{{ work.descripcion }}</p>

        <div class="flex justify-between items-center mt-2">
            <span class="px-2 py-1 text-sm font-semibold rounded" :class="{
                'bg-yellow-200 text-yellow-800': work.estado === 'pendiente',
                'bg-red-200 text-red-800': work.estado === 'esperando_confirmacion',
                'bg-blue-200 text-blue-800': work.estado === 'en_progreso',
                'bg-green-200 text-green-800': work.estado === 'finalizado'
            }">
                {{ work.estado }}
            </span>

            <div class="flex justify-between items-center mt-2">
                <button v-if="work.estado === 'esperando_confirmacion'" @click="openReviewModal(work)"
                    class="mt-auto px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
                    🔍 Ver Trabajo
                </button>
                <Link v-else :href="route('client.works.show', work.id)" class="text-blue-500 hover:underline">
                Ver detalles →
                </Link>
            </div>
        </div>
    </div>
</template>
