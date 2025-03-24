<script setup>
const props = defineProps({ works: Array })
</script>

<template>
  <div>
    <h2 class="text-xl font-semibold mb-2">📂 Últimos Trabajos</h2>
    <ul class="bg-white rounded shadow divide-y">
      <li v-for="work in works" :key="work.id" class="p-3">
        <strong>{{ work.titulo }}</strong> – {{ work.estado }} –
        <span class="text-gray-600">{{ work.client.user.name }}</span>
      </li>
    </ul>
  </div>
</template>
