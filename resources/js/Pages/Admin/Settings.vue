<script setup>
import Admin from './Admin.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

// Importamos todos los modales
import AdminManagementModal from './Settings/AdminManagementModal.vue'
import AutoArchiveModal from './Settings/AutoArchiveModal.vue'
import GeneralDocumentsModal from './Settings/GeneralDocumentsModal.vue'
import GlobalActionsModal from './Settings/GlobalActionsModal.vue'
import NotificationSettingsModal from './Settings/NotificationSettingsModal.vue'
import ProfileSettingsModal from './Settings/ProfileSettingsModal.vue'
import RequestLimitModal from './Settings/RequestLimitModal.vue'
import SystemStatusModal from './Settings/SystemStatusModal.vue'

const props = defineProps({
    settings: Object,
    admins: Array
})

// Modal activo
const showModal = ref(null)

// Abrir y cerrar modal
const openModal = (name) => showModal.value = name
const closeModal = () => showModal.value = null
</script>

<template>
    <Admin>

        <Head title="Configuración" />

        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">⚙️ Configuración del Sistema</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Perfil -->
                <div class="p-4 bg-white rounded shadow">
                    <h2 class="text-lg font-bold mb-2">👤 Perfil de Administrador</h2>
                    <p>Edita tu nombre, correo y contraseña.</p>
                    <button @click="openModal('profile')" class="text-blue-500 hover:underline mt-2">Editar perfil
                        →</button>
                </div>

                <!-- Notificaciones -->
                <div class="p-4 bg-white rounded shadow">
                    <h2 class="text-lg font-bold mb-2">🔔 Preferencias de Notificación</h2>
                    <p>Activa o desactiva alertas por eventos importantes.</p>
                    <button @click="openModal('notifications')" class="text-blue-500 hover:underline mt-2">Configurar
                        →</button>
                </div>

                <!-- Estado del sistema -->
                <div class="p-4 bg-white rounded shadow">
                    <h2 class="text-lg font-bold mb-2">🛑 Estado del Sistema</h2>
                    <p>Controla si los clientes pueden enviar nuevas solicitudes.</p>
                    <button @click="openModal('status')" class="text-blue-500 hover:underline mt-2">Cambiar estado
                        →</button>
                </div>

                <!-- Límite de solicitudes -->
                <div class="p-4 bg-white rounded shadow">
                    <h2 class="text-lg font-bold mb-2">📈 Límite de Solicitudes Activas</h2>
                    <p>Define cuántos trabajos activos puede tener un cliente.</p>
                    <button @click="openModal('limit')" class="text-blue-500 hover:underline mt-2">Establecer límite
                        →</button>
                </div>

                <!-- Autoarchivado -->
                <div class="p-4 bg-white rounded shadow">
                    <h2 class="text-lg font-bold mb-2">🕒 Autoarchivar Trabajos</h2>
                    <p>Elige cuántos días deben pasar para archivar trabajos finalizados.</p>
                    <button @click="openModal('autoArchive')" class="text-blue-500 hover:underline mt-2">Configurar
                        →</button>
                </div>

                <!-- Documentos -->
                <div class="p-4 bg-white rounded shadow">
                    <h2 class="text-lg font-bold mb-2">📁 Documentos Generales</h2>
                    <p>Sube y gestiona plantillas, guías o archivos compartidos.</p>
                    <button @click="openModal('documents')" class="text-blue-500 hover:underline mt-2">Ver documentos
                        →</button>
                </div>

                <!-- Administradores -->
                <div class="p-4 bg-white rounded shadow">
                    <h2 class="text-lg font-bold mb-2">👷 Gestión de Administradores</h2>
                    <p>Ver, añadir o eliminar administradores.</p>
                    <button @click="openModal('admins')" class="text-blue-500 hover:underline mt-2">Administrar
                        →</button>
                </div>

                <!-- Acciones globales -->
                <div class="p-4 bg-white rounded shadow">
                    <h2 class="text-lg font-bold mb-2">🧹 Acciones Globales</h2>
                    <ul class="text-sm text-gray-600 list-disc list-inside mb-2">
                        <li>Generar copia de seguridad</li>
                        <li>Eliminar archivos huérfanos</li>
                        <li>Ver registros del sistema</li>
                    </ul>
                    <button @click="openModal('globalActions')" class="text-blue-500 hover:underline mt-2">Ejecutar
                        →</button>
                </div>
            </div>
        </div>

        <!-- Modales -->
        <ProfileSettingsModal v-if="showModal === 'profile'" @close="closeModal" />
        <NotificationSettingsModal v-if="showModal === 'notifications'" :initial-settings="props.settings.notifications"
            @close="closeModal" />
        <SystemStatusModal v-if="showModal === 'status'" :initial="props.settings.system_open" @close="closeModal" />
        <RequestLimitModal v-if="showModal === 'limit'" :initial="props.settings.active_work_limit"
            @close="closeModal" />
        <AutoArchiveModal v-if="showModal === 'autoArchive'" :initial="props.settings.auto_archive_days"
            @close="closeModal" />
        <GeneralDocumentsModal v-if="showModal === 'documents'" @close="closeModal" />
        <AdminManagementModal v-if="showModal === 'admins'" :admins="props.admins" @close="closeModal" />
        <GlobalActionsModal v-if="showModal === 'globalActions'" @close="closeModal" />
    </Admin>
</template>
