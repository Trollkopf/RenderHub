<script setup>
import Admin from '../Admin.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
  archivedWorks: Array
})



const restoreWork = (id) => {
  if (confirm('¿Seguro que deseas restaurar este trabajo a pendiente?')) {
    router.put(route('admin.works.restore', id))
  }
}

const deleteWork = (id) => {
  if (confirm('⚠️ Esto eliminará el trabajo permanentemente. ¿Continuar?')) {
    router.delete(route('admin.works.destroy', id))
  }
}
</script>

<template>
  <Admin>
    <Head title="Trabajos Archivados" />
    <div class="container mx-auto p-6">
      <h1 class="text-2xl font-bold mb-6">🗃️ Trabajos Archivados</h1>

      <div v-if="archivedWorks.length">
        <div class="space-y-4">
          <div v-for="work in archivedWorks" :key="work.id" class="p-4 bg-gray-100 rounded shadow">
            <h2 class="text-lg font-semibold">{{ work.titulo }}</h2>
            <p class="text-sm text-gray-600">Cliente: {{ work.client.user.name }}</p>
            <p class="text-sm text-gray-600">Empresa: {{ work.client.empresa }}</p>
            <p class="text-sm text-gray-500">Archivado el: {{ new Date(work.updated_at).toLocaleDateString() }}</p>

            <div class="mt-4 flex space-x-2">
              <button
                @click="router.visit(route('admin.works.show', work.id))"
                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-700 text-sm"
              >
                Ver Detalles
              </button>

              <button
                @click="restoreWork(work.id)"
                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm"
              >
                Restaurar
              </button>

              <button
                @click="deleteWork(work.id)"
                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
              >
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
      <p v-else class="text-gray-500">No hay trabajos archivados.</p>
    </div>
  </Admin>
</template>
