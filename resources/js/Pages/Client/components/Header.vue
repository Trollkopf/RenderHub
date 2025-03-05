<script setup>
import { defineEmits } from 'vue'

const emit = defineEmits(['toggleMenu'])
</script>

<template>
    <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">RenderHub Cliente</h1>
        <button @click="emit('toggleMenu')" class="md:hidden">☰</button>
    </header>
</template>
