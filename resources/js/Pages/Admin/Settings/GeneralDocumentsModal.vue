<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const emit = defineEmits(['close'])

const form = useForm({
  archivo: null
})

const subirDocumento = () => {
  const data = new FormData()
  data.append('archivo', form.archivo)

  router.post(route('settings.documents.upload'), data, {
    preserveState: true,
    forceFormData: true,
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
      <h2 class="text-2xl font-bold mb-4">📁 Subir Documento General</h2>
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">×</button>

      <form @submit.prevent="subirDocumento">
        <label class="block font-semibold mb-2">Seleccionar archivo:</label>
        <input type="file" @change="e => form.archivo = e.target.files[0]" class="border p-2 rounded w-full mb-4" />

        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Subir
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
