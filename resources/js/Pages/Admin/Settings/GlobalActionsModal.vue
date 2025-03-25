<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const emit = defineEmits(['close'])
const loading = ref(false)
const message = ref(null)

const ejecutarAccion = (accion) => {
  loading.value = true
  message.value = null

  router.post(route('settings.globalAction'), { action: accion }, {
    onSuccess: () => {
      message.value = `✅ Acción "${accion}" ejecutada correctamente.`
      loading.value = false
    }
  })
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-xl relative">
      <h2 class="text-2xl font-bold mb-4">🧹 Acciones Globales</h2>
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">×</button>

      <p v-if="message" class="text-green-600 font-semibold mb-4">{{ message }}</p>

      <ul class="space-y-4">
        <li class="border-b pb-3">
          <h3 class="font-semibold">🗄 Generar Backup</h3>
          <p class="text-sm text-gray-600">Exportar los datos del sistema.</p>
          <button @click="ejecutarAccion('backup')" :disabled="loading"
            class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            Ejecutar
          </button>
        </li>

        <li class="border-b pb-3">
          <h3 class="font-semibold">🧺 Limpiar Archivos Huérfanos</h3>
          <p class="text-sm text-gray-600">Eliminar archivos no vinculados.</p>
          <button @click="ejecutarAccion('clean_orphaned_files')" :disabled="loading"
            class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Ejecutar
          </button>
        </li>

        <li>
          <h3 class="font-semibold">📜 Ver Logs del Sistema</h3>
          <p class="text-sm text-gray-600">Consultar actividad reciente.</p>
          <button @click="ejecutarAccion('view_logs')" :disabled="loading"
            class="mt-2 px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
            Ejecutar
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>
