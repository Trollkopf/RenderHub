<script setup>
import { ref } from 'vue'
import Header from './components/Header.vue'
import Sidebar from './components/Sidebar.vue'

const showMenu = ref(false)
const toggleMenu = () => {
    showMenu.value = !showMenu.value
}
</script>

<template>
    <div class="flex flex-col h-screen bg-indigo-100">
        <Header @toggleMenu="toggleMenu" />
        <div class="flex flex-1">
            <Sidebar :show="showMenu" />
            <main class="flex-1 p-4">
                <slot />
            </main>
        </div>
    </div>
</template>
