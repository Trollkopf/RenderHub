<script setup>
const props = defineProps({ stats: Object })
</script>

<template>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div v-for="(value, key) in stats" :key="key" class="bg-white p-4 shadow rounded text-center">
            <h3 class="text-lg font-semibold capitalize">{{ key }}</h3>
            <p class="text-2xl font-bold text-blue-600">{{ value }}</p>
        </div>
    </div>
</template>
