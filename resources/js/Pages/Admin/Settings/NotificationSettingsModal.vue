<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  initialSettings: Object
})
const emit = defineEmits(['close'])

const form = useForm({
  nuevo_trabajo: props.initialSettings?.nuevo_trabajo ?? true,
  cambio_solicitado: props.initialSettings?.cambio_solicitado ?? true,
  trabajo_asignado: props.initialSettings?.trabajo_asignado ?? true,
  trabajo_finalizado: props.initialSettings?.trabajo_finalizado ?? true
})

const guardar = () => {
  router.put(route('settings.notifications'), form)
  emit('close')
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg relative">
      <h2 class="text-2xl font-bold mb-4">🔔 Preferencias de Notificación</h2>
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">×</button>

      <form @submit.prevent="guardar" class="space-y-4">
        <div>
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.nuevo_trabajo" />
            <span>Cuando un cliente solicita un trabajo</span>
          </label>
        </div>

        <div>
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.cambio_solicitado" />
            <span>Cuando se solicita un cambio</span>
          </label>
        </div>

        <div>
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.trabajo_asignado" />
            <span>Cuando me asignan un trabajo</span>
          </label>
        </div>

        <div>
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.trabajo_finalizado" />
            <span>Cuando un trabajo se finaliza</span>
          </label>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Guardar Preferencias
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
