<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { defineEmits } from 'vue'

const emit = defineEmits(['close'])

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const guardarPerfil = () => {
  router.post('/admin/profile', form)
  emit('close')
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
      <h2 class="text-2xl font-bold mb-4">👤 Editar Perfil</h2>
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">×</button>

      <form @submit.prevent="guardarPerfil" class="space-y-4">
        <div>
          <label class="block font-semibold">Nombre</label>
          <input v-model="form.name" type="text" class="w-full border rounded p-2" required />
        </div>

        <div>
          <label class="block font-semibold">Correo</label>
          <input v-model="form.email" type="email" class="w-full border rounded p-2" required />
        </div>

        <div>
          <label class="block font-semibold">Nueva contraseña</label>
          <input v-model="form.password" type="password" class="w-full border rounded p-2" />
        </div>

        <div>
          <label class="block font-semibold">Confirmar contraseña</label>
          <input v-model="form.password_confirmation" type="password" class="w-full border rounded p-2" />
        </div>

        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
