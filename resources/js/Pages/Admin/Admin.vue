<script setup>
import { ref } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

import Header from './components/Header.vue'
import Sidebar from './components/Sidebar.vue'

const showMenu = ref(false)
const page = usePage()
const toggleMenu = () => {
    showMenu.value = !showMenu.value
}

// console.log(page.props.auth.user);
</script>

<template>

    <div class="flex flex-col h-screen">

        <Header @toggleMenu="toggleMenu" />

        <div class="flex flex-1">
            <Sidebar :show="showMenu" />

            <main class="flex-1 p-4">

                <slot />
            </main>
        </div>
    </div>
</template>
