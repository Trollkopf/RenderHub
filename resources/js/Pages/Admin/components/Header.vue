<script setup>
import { defineEmits } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const page = usePage()

console.log(page.props.auth.user)
console.log(page.props.auth.notifications_count)

const emit = defineEmits(['toggleMenu'])
</script>

<template>
    <header class="bg-gray-900 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">RenderHub Admin</h1>
        <div class="flex items-center space-x-4">
            <span class="text-sm font-semibold">{{ page.props.auth.user.name }}</span>
      <Link :href="route('notifications.index')" class="relative text-2xl hover:text-yellow-400">
        🔔
        <span
          v-if="page.props.auth.notifications_count > 0"
          class="absolute -top-1 -right-2 bg-red-600 text-white text-xs rounded-full px-1"
        >
          {{ page.props.auth.notifications_count }}
        </span>
      </Link>

    </div>
        <button @click="emit('toggleMenu')" class="md:hidden">☰</button>
    </header>
</template>
