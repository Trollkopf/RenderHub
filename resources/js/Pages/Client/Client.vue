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
    <div class="flex flex-col h-screen">
        <Header @toggleMenu="toggleMenu" />
        <Link :href="route('notifications.index')" class="relative">
        🔔
        <span v-if="$page.props.auth.user.notifications_count > 0"
            class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1">
            {{ $page.props.auth.user.notifications_count }}
        </span>
        </Link>
        <div class="flex flex-1">
            <Sidebar :show="showMenu" />
            <main class="flex-1 p-4">
                <slot />
            </main>
        </div>
    </div>
</template>
