<script setup>
import { ref } from 'vue'

const novedades = ref([
    { id: 1, mensaje: "Tu trabajo 'Diseño 3D Casa' ha sido actualizado." },
    { id: 2, mensaje: "El trabajo 'Render Fachada' ha cambiado de estado a 'Finalizado'." }
])
</script>

<template>
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-semibold mb-2">🔔 Novedades en tus Trabajos</h2>

        <div v-if="novedades.length">
            <ul>
                <li v-for="novedad in novedades" :key="novedad.id" class="border-b py-2">
                    {{ novedad.mensaje }}
                </li>
            </ul>
        </div>

        <p v-else class="text-gray-500">No hay novedades recientes.</p>
    </div>
</template>
