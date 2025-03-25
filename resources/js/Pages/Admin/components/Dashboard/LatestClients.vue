<script setup>
const props = defineProps({ clients: Array })
</script>

<template>
  <div>
    <h2 class="text-xl font-semibold mb-2">👥 Nuevos Clientes</h2>
    <ul class="bg-white rounded shadow divide-y">
      <li v-for="client in clients" :key="client.id" class="p-3">
        {{ client.user.name }} – {{ client.empresa || 'Sin empresa' }}
      </li>
    </ul>
  </div>
</template>
