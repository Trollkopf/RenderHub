<script setup>
import Admin from './Admin.vue'
import { Head, Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
    works: Array
})
</script>

<template>
    <Admin>
        <Head title="Mis Trabajos" />

        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold mb-6">🧑‍💼 Mis Trabajos Asignados</h1>

            <div v-if="works.length">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="work in works" :key="work.id" class="p-4 bg-white rounded shadow">
                        <h2 class="text-lg font-semibold mb-1">{{ work.titulo }}</h2>
                        <p class="text-sm text-gray-700 mb-2">{{ work.descripcion }}</p>
                        <p class="text-sm text-gray-600"><strong>Cliente:</strong> {{ work.client.user.name }}</p>
                        <p class="text-sm text-gray-600"><strong>Estado:</strong> {{ work.estado }}</p>
                        <div class="mt-4">
                            <Link :href="route('admin.works.show', work.id)" class="text-blue-600 hover:underline">
                                🔍 Ver Detalles
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <p v-else class="text-gray-500">No tienes trabajos asignados.</p>
        </div>
    </Admin>
</template>
