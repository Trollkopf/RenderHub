<script setup>
import { usePage } from '@inertiajs/vue3'
import { onMounted } from 'vue'

const user = usePage().props.auth.user

onMounted(() => {
    if (user.role === 'admin') {
        window.location.href = route('admin.dashboard')
    } else {
        window.location.href = route('client.dashboard')
    }
})
</script>

<template>
    <div>Cargando...</div>
</template>
