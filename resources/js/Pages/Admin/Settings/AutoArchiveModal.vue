<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  initial: Number
})
const emit = defineEmits(['close'])

const form = useForm({
  days: props.initial
})

const guardar = () => {
  router.put(route('settings.autoArchive'), form)
  emit('close')
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
      <h2 class="text-2xl font-bold mb-4">🕒 Autoarchivar Trabajos</h2>
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">×</button>

      <form @submit.prevent="guardar">
        <label class="block mb-2 font-semibold text-sm">Días antes de archivar automáticamente:</label>
        <input type="number" v-model="form.days" min="1" class="w-full border rounded p-2 mb-4" />

        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
