<script setup>
import Admin from '../Admin.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  archivedWorks: Array
})
</script>

<template>
  <Admin>
    <Head title="Trabajos Archivados" />
    <div class="container mx-auto p-6">
      <h1 class="text-2xl font-bold mb-4">🗃️ Trabajos Archivados</h1>

      <div v-if="archivedWorks.length">
        <ul class="space-y-4">
          <li v-for="work in archivedWorks" :key="work.id" class="border p-4 rounded bg-gray-100">
            <p class="font-semibold">{{ work.titulo }}</p>
            <p class="text-sm text-gray-600">Cliente: {{ work.client.user.name }}</p>
            <p class="text-sm text-gray-500">Archivado el: {{ new Date(work.updated_at).toLocaleDateString() }}</p>
          </li>
        </ul>
      </div>
      <p v-else class="text-gray-500">No hay trabajos archivados.</p>
    </div>
  </Admin>
</template>
