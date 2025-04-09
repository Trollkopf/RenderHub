<script setup>
import Admin from './Admin.vue'
import { Head } from '@inertiajs/vue3'

import StatsCard from './components/Dashboard/StatsCard.vue'
import LatestWorks from './components/Dashboard/LatestWorks.vue'
import LatestClients from './components/Dashboard/LatestClients.vue'
import RecentNotifications from './components/Dashboard/RecentNotifications.vue'

const props = defineProps({
  stats: Object,
  latestWorks: Array,
  latestClients: Array,
  recentNotifications: Array
})
</script>

<template>
  <Admin>
    <Head title="Dashboard Admin" />
    <div class="container mx-auto p-6 space-y-8">
      <h1 class="text-2xl font-bold mb-4">📊 Panel de Control</h1>

      <h2 class="text-xl font-semibold mb-2">Resumen de Trabajos</h2>
      <StatsCard :stats="props.stats" />

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <LatestWorks :works="props.latestWorks" />
        <LatestClients :clients="props.latestClients" />
      </div>

      <RecentNotifications :notifications="props.recentNotifications" />


    </div>
  </Admin>
</template>
