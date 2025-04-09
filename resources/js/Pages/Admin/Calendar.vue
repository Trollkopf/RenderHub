<template>
    <Admin>

        <Head title="Calendario" />
        <div class="container mx-auto p-6 space-y-6">
            <h1 class="text-2xl font-bold">🗓️ Calendario de Eventos</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Calendario -->
                <div class="col-span-1 lg:col-span-2">
                    <CalendarComponent ref="calendarRef" />
                </div>

                <!-- Formulario -->
                <form @submit.prevent="submitEvent" class="col-span-1 bg-white shadow rounded-xl p-4 space-y-4">
                    <h2 class="text-2xl font-bold">Añadir Evento</h2>
                    <label class="block">
                        Título:
                        <input v-model="form.title" type="text" class="w-full border rounded px-3 py-2 mt-1" required />
                    </label>

                    <label class="block">
                        Fecha:
                        <input v-model="form.date" type="date" class="w-full border rounded px-3 py-2 mt-1" required />
                    </label>

                    <label class="block">
                        Color del evento:
                        <input type="color" v-model="form.color" class="w-full h-10 border rounded px-3 py-2 mt-1" />
                    </label>
                    <label class="block mb-4">
                        Descripción:
                        <textarea v-model="form.description" maxlength="500" rows="3"
                            class="w-full border rounded px-3 py-2 mt-1 resize-none"
                            placeholder="Escribe una descripción corta (máx. 500 caracteres)"></textarea>
                    </label>

                    <label class="block">
                        Repetición:
                        <select v-model="form.recurrence" class="w-full border rounded px-3 py-2 mt-1">
                            <option value="">— No repetir —</option>
                            <option value="daily">Diariamente</option>
                            <option value="weekly">Semanalmente</option>
                            <option value="monthly">Mensualmente</option>
                            <option value="quarterly">Trimestralmente</option>
                            <option value="yearly">Anualmente</option>
                        </select>
                    </label>

                    <label class="block mb-4" v-if="form.recurrence">
                        ¿Cuántas veces se repite (incluyendo la original)?
                        <input v-model="form.repeat_count" type="number" min="1"
                            class="w-full border rounded px-3 py-2 mt-1" />
                    </label>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Guardar evento
                    </button>
                </form>
            </div>
        </div>
    </Admin>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import Admin from './Admin.vue'
import CalendarComponent from './components/CalendarComponent.vue'
import { ref } from 'vue'
import axios from 'axios'

const calendarRef = ref(null)

const form = ref({
    title: '',
    date: '',
    recurrence: null,
    repeat_count: null,
    color: '#3b82f6',
    description: ''

})


async function submitEvent() {
    try {
        await axios.post('/calendar', form.value)
        alert('✅ Evento guardado')

        // Limpia el formulario si quieres
        form.value = {
            title: '',
            start: '',
            recurrence: null,
            repeat_until_days: null,
            color: '#3b82f6'
        }

        // Recarga el calendario
        calendarRef.value?.loadEvents()
    } catch (err) {
        console.error('Error al guardar evento:', err)
    }
}

</script>
