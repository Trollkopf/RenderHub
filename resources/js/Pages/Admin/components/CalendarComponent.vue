<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import axios from 'axios'

export default {
    components: { FullCalendar },
    data() {
        return {
            calendarOptions: {
                plugins: [dayGridPlugin],
                initialView: 'dayGridMonth',
                events: [],
                eventClick: this.onEventClick // 👈 aquí
            },
            selectedEvent: null, // evento clicado
            showModal: false,
            editMode: false
        }
    },
    mounted() {
        this.loadEvents()
    },
    methods: {
        async loadEvents() {
            const res = await axios.get('/api/calendar')
            this.calendarOptions.events = res.data
        },
        onEventClick(info) {
            const props = info.event.extendedProps

            this.selectedEvent = {
                id: info.event.id || props.parent_id || null,
                title: info.event.title,
                date: info.event.startStr,
                description: info.event.extendedProps.description || ''
            }
            console.log(this.selectedEvent);
            this.editMode = false
            this.showModal = true
        },
        closeModal() {
            this.showModal = false
            this.selectedEvent = null
        },
        startEditing() {
            this.editMode = true
        },
        async saveChanges() {
            try {
                const payload = {
                    title: this.selectedEvent.title,
                    start: this.selectedEvent.date, // 👈 Aquí renombramos
                    color: this.selectedEvent.color,
                    description: this.selectedEvent.description,
                }

                await axios.put(`/calendar/${this.selectedEvent.id}`, payload)
                await this.loadEvents()
                this.editMode = false
                this.showModal = false
            } catch (err) {
                console.error('Error actualizando evento:', err)
            }
        },
        async deleteEvent() {
            console.log(this.selectedEvent);

            if (confirm('¿Estás seguro de eliminar este evento?')) {
                try {
                    await axios.delete(`/calendar/${this.selectedEvent.id}`)
                    await this.loadEvents()
                    this.closeModal()
                } catch (err) {
                    console.error('Error al eliminar evento:', err)
                }
            }
        }
    }


}
</script>


<template>
    <div>
        <FullCalendar :options="calendarOptions" />
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <template v-if="!editMode">
                <h2 class="text-xl font-bold mb-2">{{ selectedEvent.title }}</h2>
                <p class="mb-2">📅 {{ selectedEvent.date }}</p>
                <p v-if="selectedEvent.description" class="text-sm text-gray-700 mb-2">
                    📝 {{ selectedEvent.description }}
                </p>
                <div class="flex justify-between mt-4">
                    <button @click="startEditing"
                        class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                        Editar
                    </button>
                    <button @click="deleteEvent" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Eliminar
                    </button>
                </div>
            </template>

            <template v-else>
                <h2 class="text-xl font-bold mb-4">Editar evento</h2>
                <label class="block mb-2">Título:
                    <input v-model="selectedEvent.title" class="w-full border rounded px-3 py-2 mt-1" />
                </label>
                <label class="block mb-2">Fecha:
                    <input v-model="selectedEvent.date" type="date" class="w-full border rounded px-3 py-2 mt-1" />
                </label>
                <label class="block mb-2">Descripción:
                    <textarea v-model="selectedEvent.description" maxlength="500"
                        class="w-full border rounded px-3 py-2 mt-1" />
                </label>

                <p v-if="selectedEvent.admins?.length" class="text-sm text-gray-700 mt-2">
                    👥 Admins: {{ selectedEvent.admins.join(', ') }}
                </p>

                <div class="flex justify-end mt-4 space-x-2">
                    <button @click="editMode = false" class="bg-gray-400 text-white px-4 py-2 rounded">Cancelar</button>
                    <button @click="saveChanges" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </template>
        </div>
    </div>


</template>
