<script setup>
import Client from '../Client.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import WorkList from './components/WorkList.vue'
import RequestWorkModal from './components/RequestWorkModal.vue'
import ReviewWorkModal from './components/ReviewWorkModal.vue'

const props = defineProps({
    pendingWorks: Object,
    inProgressWorks: Object,
    waitingConfirmationWorks: Object,
    completedWorks: Object
})

const showRequestModal = ref(false)
const showReviewModal = ref(false)
const selectedWork = ref(null)

// Abrir modal de revisión
const openReviewModal = (work) => {
    selectedWork.value = work
    showReviewModal.value = true
}
</script>

<template>
    <Client>
        <Head title="Mis Trabajos" />

        <button @click="showRequestModal = true" class="bg-blue-500 text-white px-4 py-2 rounded">
            ➕ Solicitar Nuevo Trabajo
        </button>

        <!-- Sección de trabajos organizados por estado -->
        <WorkList title="⏳ Esperando Confirmación" :works="waitingConfirmationWorks" :openReviewModal="openReviewModal" />
        <WorkList title="📌 Trabajos Solicitados" :works="pendingWorks" />
        <WorkList title="🛠️ Trabajos en Progreso" :works="inProgressWorks" />
        <WorkList title="✅ Trabajos Finalizados" :works="completedWorks" />

        <!-- Modales -->
        <RequestWorkModal :showRequestModal="showRequestModal" @close="showRequestModal = false" />
        <ReviewWorkModal :showReviewModal="showReviewModal" :selectedWork="selectedWork" @close="showReviewModal = false" />
    </Client>
</template>
