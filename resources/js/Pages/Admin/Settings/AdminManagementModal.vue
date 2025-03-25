<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  admins: Array
})
const emit = defineEmits(['close'])

const form = useForm({
  name: '',
  email: '',
  password: ''
})

const crearAdmin = () => {
  router.post(route('admin.users.store'), form, {
    onSuccess: () => form.reset()
  })
}

const eliminarAdmin = (id) => {
  if (confirm('¿Estás seguro de que quieres eliminar este administrador?')) {
    router.delete(route('admin.users.destroy', id))
  }
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl relative">
      <h2 class="text-2xl font-bold mb-4">👷 Gestión de Administradores</h2>
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">×</button>

      <!-- Formulario -->
      <form @submit.prevent="crearAdmin" class="space-y-4 mb-6">
        <div>
          <label class="block font-semibold">Nombre</label>
          <input v-model="form.name" type="text" class="w-full border rounded p-2" required />
        </div>
        <div>
          <label class="block font-semibold">Email</label>
          <input v-model="form.email" type="email" class="w-full border rounded p-2" required />
        </div>
        <div>
          <label class="block font-semibold">Contraseña</label>
          <input v-model="form.password" type="password" class="w-full border rounded p-2" required />
        </div>
        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Añadir
          </button>
        </div>
      </form>

      <!-- Lista de admins -->
      <h3 class="text-xl font-semibold mb-2">Administradores actuales</h3>
      <div v-if="props.admins.length">
        <table class="w-full text-left border">
          <thead class="bg-gray-100 text-sm">
            <tr>
              <th class="p-2">Nombre</th>
              <th class="p-2">Email</th>
              <th class="p-2">Desde</th>
              <th class="p-2 text-center">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="admin in props.admins" :key="admin.id" class="border-t text-sm">
              <td class="p-2">{{ admin.name }}</td>
              <td class="p-2">{{ admin.email }}</td>
              <td class="p-2">{{ new Date(admin.created_at).toLocaleDateString() }}</td>
              <td class="p-2 text-center">
                <button
                  @click="eliminarAdmin(admin.id)"
                  class="text-red-500 hover:underline text-sm"
                  :disabled="admin.id === $page.props.auth.user.id"
                >
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-else class="text-gray-500">No hay administradores registrados.</p>
    </div>
  </div>
</template>
