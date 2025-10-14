<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-primary-600 to-primary-700 text-white sticky top-0 z-40 shadow-lg">
      <div class="px-4 py-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h1 class="text-2xl font-bold">Outbreak Alerts</h1>
            <p class="text-primary-100 text-sm mt-1">Provincial Health Office</p>
          </div>
          <button @click="refreshAlerts" :disabled="loading" class="p-2 bg-white/20 rounded-full hover:bg-white/30 transition-colors">
            <svg class="w-6 h-6" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>

        <!-- Statistics Cards -->
        <div v-if="statistics" class="space-y-2">
          <!-- Total -->
          <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center">
            <div class="text-2xl font-bold">{{ statistics.total_active_alerts }}</div>
            <div class="text-xs text-primary-100">Total Active Alerts</div>
          </div>
          <!-- Alert Levels Grid -->
          <div class="grid grid-cols-4 gap-2">
            <div class="bg-red-500/30 backdrop-blur-sm rounded-lg p-2 text-center">
              <div class="text-lg font-bold">{{ statistics.red_alerts }}</div>
              <div class="text-xs text-red-100">Critical</div>
            </div>
            <div class="bg-orange-500/30 backdrop-blur-sm rounded-lg p-2 text-center">
              <div class="text-lg font-bold">{{ statistics.orange_alerts }}</div>
              <div class="text-xs text-orange-100">Immediate</div>
            </div>
            <div class="bg-yellow-500/30 backdrop-blur-sm rounded-lg p-2 text-center">
              <div class="text-lg font-bold">{{ statistics.yellow_alerts }}</div>
              <div class="text-xs text-yellow-100">Vigilance</div>
            </div>
            <div class="bg-green-500/30 backdrop-blur-sm rounded-lg p-2 text-center">
              <div class="text-lg font-bold">{{ statistics.green_alerts }}</div>
              <div class="text-xs text-green-100">Monitor</div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Filters -->
    <div class="bg-white border-b border-gray-200 px-4 py-3 sticky top-0 z-30">
      <div class="flex gap-2 overflow-x-auto pb-1">
        <button
          @click="setFilter('')"
          :class="filterLevel === '' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700'"
          class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors"
        >
          All Alerts
        </button>
        <button
          @click="setFilter('Red')"
          :class="filterLevel === 'Red' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700'"
          class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors"
        >
          🔴 Critical Emergency
        </button>
        <button
          @click="setFilter('Orange')"
          :class="filterLevel === 'Orange' ? 'bg-orange-600 text-white' : 'bg-orange-100 text-orange-700'"
          class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors"
        >
          🟠 Immediate Action Required
        </button>
        <button
          @click="setFilter('Yellow')"
          :class="filterLevel === 'Yellow' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700'"
          class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors"
        >
          🟡 Increase Vigilance
        </button>
        <button
          @click="setFilter('Green')"
          :class="filterLevel === 'Green' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700'"
          class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors"
        >
          🟢 Monitor Situation
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !alerts.length" class="flex items-center justify-center h-64">
      <div class="spinner"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && !alerts.length" class="flex flex-col items-center justify-center h-64 px-4">
      <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-2">No Active Alerts</h3>
      <p class="text-gray-600 text-center text-sm">There are currently no outbreak alerts in your area.</p>
    </div>

    <!-- Alert Cards -->
    <div v-else class="px-4 py-4 space-y-4">
      <alert-card
        v-for="alert in alerts"
        :key="alert.id"
        :alert="alert"
        @click="viewAlert(alert.id)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAlertStore } from '@/stores/alert'
import { useSettingsStore } from '@/stores/settings'
import AlertCard from '@/components/AlertCard.vue'

const router = useRouter()
const alertStore = useAlertStore()
const settingsStore = useSettingsStore()

const filterLevel = ref('')

const alerts = computed(() => alertStore.alerts)
const statistics = computed(() => alertStore.statistics)
const loading = computed(() => alertStore.loading)

const refreshAlerts = async () => {
  const params: any = {}

  if (settingsStore.settings.municipality_id) {
    params.municipality_id = settingsStore.settings.municipality_id
  }

  if (settingsStore.settings.barangay_id) {
    params.barangay_id = settingsStore.settings.barangay_id
  }

  if (filterLevel.value) {
    params.alert_level = filterLevel.value
  }

  await alertStore.fetchAlerts(params)
  await alertStore.fetchStatistics(params)
}

const setFilter = (level: string) => {
  filterLevel.value = level
  refreshAlerts()
}

const viewAlert = (id: number) => {
  router.push(`/alerts/${id}`)
}

// Watch for settings changes
watch(
  () => [settingsStore.settings.municipality_id, settingsStore.settings.barangay_id],
  () => {
    refreshAlerts()
  }
)

onMounted(() => {
  refreshAlerts()
})
</script>
