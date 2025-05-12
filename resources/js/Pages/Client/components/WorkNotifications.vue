<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    notifications: Array
})

console.log(props);
</script>

<template>
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-semibold mb-2">🔔 Novedades en tus Trabajos</h2>

        <div v-if="props.notifications">
            <ul>
                <li v-for="n in props.notifications" :key="n.id" class="border-b py-2" :class="{ 'opacity-60': n.leido }">
                    {{ n.mensaje }}
                </li>
            </ul>
        </div>

        <p v-else class="text-gray-500">No hay novedades recientes.</p>
    </div>
</template>
