<script setup>
import { defineEmits } from 'vue'

const emit = defineEmits(['toggleMenu'])
</script>

<template>
    <header class="bg-gray-900 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">RenderHub Admin</h1>
        <button @click="emit('toggleMenu')" class="md:hidden">☰</button>
    </header>
</template>
