<script setup>
const props = defineProps({ notifications: Array })
</script>

<template>
  <div>
    <h2 class="text-xl font-semibold mb-2">🔔 Notificaciones Recientes</h2>
    <ul class="bg-white rounded shadow divide-y">
      <li v-for="n in notifications" :key="n.id" class="p-3">
        {{ n.mensaje }} <span class="text-xs text-gray-500">({{ new Date(n.created_at).toLocaleString() }})</span>
      </li>
    </ul>
  </div>
</template>
