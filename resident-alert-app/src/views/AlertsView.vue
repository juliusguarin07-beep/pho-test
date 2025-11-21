<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 sticky top-0 z-40 shadow-sm">
      <div class="px-4 py-5">
        <!-- Title Section -->
        <div class="flex items-center justify-between mb-5">
          <div>
            <h1 class="text-3xl font-light tracking-tight text-gray-900">Alerts</h1>
            <p class="text-gray-500 text-sm mt-1 font-light">Health updates for your area</p>
          </div>
          <button
            @click="refreshAlerts"
            :disabled="loading"
            class="p-2.5 hover:bg-gray-100 rounded-full transition-all duration-200 group"
          >
            <svg
              class="w-5 h-5 text-gray-600 group-hover:text-gray-900 transition-colors"
              :class="{ 'animate-spin': loading }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>

        <!-- Statistics Cards - Elegant Design -->
        <div v-if="statistics" class="grid grid-cols-4 gap-2.5">
          <div class="bg-gradient-to-br from-red-50 to-red-100/30 rounded-lg p-3 border border-red-200/50 backdrop-blur">
            <div class="text-2xl font-semibold text-red-600">{{ statistics.red_alerts }}</div>
            <div class="text-xs text-red-600/70 font-medium mt-1">Critical</div>
          </div>
          <div class="bg-gradient-to-br from-orange-50 to-orange-100/30 rounded-lg p-3 border border-orange-200/50 backdrop-blur">
            <div class="text-2xl font-semibold text-orange-600">{{ statistics.orange_alerts }}</div>
            <div class="text-xs text-orange-600/70 font-medium mt-1">Immediate</div>
          </div>
          <div class="bg-gradient-to-br from-amber-50 to-amber-100/30 rounded-lg p-3 border border-amber-200/50 backdrop-blur">
            <div class="text-2xl font-semibold text-amber-600">{{ statistics.yellow_alerts }}</div>
            <div class="text-xs text-amber-600/70 font-medium mt-1\">Vigilance</div>
          </div>
          <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/30 rounded-lg p-3 border border-emerald-200/50 backdrop-blur">
            <div class="text-2xl font-semibold text-emerald-600">{{ statistics.green_alerts }}</div>
            <div class="text-xs text-emerald-600/70 font-medium mt-1">Monitor</div>
          </div>
        </div>
      </div>
    </header>

    <!-- Filters -->
    <div class="bg-white/60 backdrop-blur-sm border-b border-gray-200/50 px-4 py-3 sticky top-[160px] z-30">
      <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
        <button
          @click="setFilter('')"
          :class="filterLevel === '' ? 'bg-gray-800 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300/50 hover:bg-gray-50'"
          class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all duration-200"
        >
          All
        </button>
        <button
          @click="setFilter('Red')"
          :class="filterLevel === 'Red' ? 'bg-red-500 text-white shadow-md' : 'bg-white text-red-600 border border-red-200/50 hover:bg-red-50'"
          class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all duration-200"
        >
          Critical
        </button>
        <button
          @click="setFilter('Orange')"
          :class="filterLevel === 'Orange' ? 'bg-orange-500 text-white shadow-md' : 'bg-white text-orange-600 border border-orange-200/50 hover:bg-orange-50'"
          class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all duration-200"
        >
          Immediate
        </button>
        <button
          @click="setFilter('Yellow')"
          :class="filterLevel === 'Yellow' ? 'bg-amber-500 text-white shadow-md' : 'bg-white text-amber-600 border border-amber-200/50 hover:bg-amber-50'"
          class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all duration-200"
        >
          Vigilance
        </button>
        <button
          @click="setFilter('Green')"
          :class="filterLevel === 'Green' ? 'bg-emerald-500 text-white shadow-md' : 'bg-white text-emerald-600 border border-emerald-200/50 hover:bg-emerald-50'"
          class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all duration-200"
        >
          Monitor
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !alerts.length" class="flex items-center justify-center h-64">
      <div class="animate-pulse space-y-4 w-full px-4">
        <div class="h-32 bg-gray-200 rounded-lg"></div>
        <div class="h-32 bg-gray-200 rounded-lg"></div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && !alerts.length" class="flex flex-col items-center justify-center py-20 px-4">
      <div class="text-6xl mb-4 opacity-80">✓</div>
      <h3 class="text-xl font-light text-gray-900 mb-2">All Clear</h3>
      <p class="text-gray-500 text-center text-sm max-w-xs font-light">No active outbreak alerts in your area. Stay healthy!</p>
    </div>

    <!-- Alert Cards -->
    <div v-else class="px-4 py-5 space-y-3 pb-24">
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
