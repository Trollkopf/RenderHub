<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const emit = defineEmits(['close'])

// Simulamos el estado actual (en una app real, esto vendrá del backend)
const isOpen = ref(true)

// Formulario con el estado del sistema
const form = useForm({
  abierto: isOpen.value
})

// Guardar el nuevo estado
const guardarEstado = () => {
  // router.put(route('settings.systemStatus'), form)

  isOpen.value = form.abierto
  emit('close')
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
      <h2 class="text-2xl font-bold mb-4">🛑 Estado del Sistema</h2>

      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">×</button>

      <p class="mb-4 text-gray-700">
        Puedes activar o desactivar temporalmente la recepción de nuevos trabajos por parte de los clientes.
      </p>

      <form @submit.prevent="guardarEstado">
        <label class="block mb-2 font-semibold">Estado actual:</label>
        <select v-model="form.abierto" class="w-full border rounded p-2 mb-4">
          <option :value="true">✅ Abierto (se pueden solicitar trabajos)</option>
          <option :value="false">🚫 Cerrado (no se permiten solicitudes)</option>
        </select>

        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
