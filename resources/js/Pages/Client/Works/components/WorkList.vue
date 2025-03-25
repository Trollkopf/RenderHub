<script setup>
import WorkCard from './WorkCard.vue'
import { defineProps } from 'vue'

const props = defineProps({
    title: String,
    works: { type: Object, default: () => ({ data: [] }) },
    openReviewModal: Function,
})
</script>

<template>
    <div v-if="works && works.data && works.data.length">
        <h2 class="text-xl font-semibold mt-4">{{ title }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            <div v-for="work in works.data" :key="work.id">
                <WorkCard :work="work" class="w-full" :openReviewModal="openReviewModal" />
            </div>
        </div>
    </div>
</template>
