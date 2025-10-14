<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center h-screen">
      <div class="spinner"></div>
    </div>

    <!-- Alert Details -->
    <div v-else-if="alert" class="pb-6">
      <!-- Header with Back Button -->
      <div
        class="sticky top-0 z-40 px-4 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg"
      >
        <button @click="goBack" class="flex items-center gap-2 mb-4">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          <span class="font-semibold">Back to Alerts</span>
        </button>

        <div class="flex items-start justify-between">
          <div class="flex-1">
            <span
              class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase mb-2"
              :class="badgeClass"
            >
              {{ alertLevelText }} Alert
            </span>
            <h1 class="text-2xl font-bold">{{ alert.title }}</h1>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="px-4 py-6 space-y-4">
        <!-- Quick Info Cards -->
        <div class="grid grid-cols-2 gap-3">
          <div class="card bg-red-50 border border-red-200">
            <div class="text-xs text-red-600 font-semibold mb-1">Disease</div>
            <div class="text-sm font-bold text-red-900">{{ alert.disease?.name }}</div>
          </div>
          <div class="card bg-orange-50 border border-orange-200">
            <div class="text-xs text-orange-600 font-semibold mb-1">Cases</div>
            <div class="text-2xl font-bold text-orange-900">{{ alert.number_of_cases }}</div>
          </div>
        </div>

        <!-- Location Info -->
        <div class="card">
          <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            </svg>
            Affected Location
          </h2>
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600">Municipality:</span>
              <span class="text-sm font-semibold text-gray-900">{{ alert.municipality?.name }}</span>
            </div>
            <div v-if="alert.barangay" class="flex items-center justify-between">
              <span class="text-sm text-gray-600">Barangay:</span>
              <span class="text-sm font-semibold text-gray-900">{{ alert.barangay.name }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600">Areas:</span>
              <span class="text-sm font-semibold text-gray-900">{{ alert.affected_areas }}</span>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="card">
          <h2 class="text-lg font-bold text-gray-900 mb-3">Alert Description</h2>
          <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ alert.description }}</p>
        </div>

        <!-- Preventive Measures -->
        <div class="card bg-blue-50 border border-blue-200">
          <h2 class="text-lg font-bold text-blue-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Preventive Measures
          </h2>
          <p class="text-sm text-blue-900 leading-relaxed whitespace-pre-wrap">{{ alert.preventive_measures }}</p>
        </div>

        <!-- Do's and Don'ts -->
        <div class="card bg-yellow-50 border border-yellow-200">
          <h2 class="text-lg font-bold text-yellow-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            Do's and Don'ts
          </h2>
          <p class="text-sm text-yellow-900 leading-relaxed whitespace-pre-wrap">{{ alert.dos_and_donts }}</p>
        </div>

        <!-- Emergency Contacts -->
        <div class="card bg-red-50 border border-red-200">
          <h2 class="text-lg font-bold text-red-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
            </svg>
            Emergency Contacts
          </h2>
          <p class="text-sm text-red-900 leading-relaxed whitespace-pre-wrap">{{ alert.emergency_contacts }}</p>
        </div>

        <!-- Date Info -->
        <div class="card bg-gray-50">
          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-gray-600">Alert Started:</span>
              <span class="font-semibold text-gray-900">{{ formatDate(alert.alert_start_date) }}</span>
            </div>
            <div v-if="alert.alert_end_date" class="flex items-center justify-between">
              <span class="text-gray-600">Alert Ends:</span>
              <span class="font-semibold text-gray-900">{{ formatDate(alert.alert_end_date) }}</span>
            </div>
            <div v-if="alert.issuedBy" class="flex items-center justify-between">
              <span class="text-gray-600">Issued By:</span>
              <span class="font-semibold text-gray-900">{{ alert.issuedBy.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="flex flex-col items-center justify-center h-screen px-4">
      <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-2">Alert Not Found</h3>
      <p class="text-gray-600 text-center text-sm mb-4">This alert may have been removed or is no longer available.</p>
      <button @click="goBack" class="btn-primary">
        Back to Alerts
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAlertStore } from '@/stores/alert'

const route = useRoute()
const router = useRouter()
const alertStore = useAlertStore()

const loading = computed(() => alertStore.loading)
const alert = computed(() => alertStore.currentAlert)

const badgeClass = computed(() => {
  if (!alert.value) return ''
  const classes: Record<string, string> = {
    'Red': 'bg-red-600 text-white',
    'Orange': 'bg-orange-600 text-white',
    'Yellow': 'bg-yellow-600 text-white',
    'Green': 'bg-green-600 text-white'
  }
  return classes[alert.value.alert_level] || 'bg-gray-600 text-white'
})

const alertLevelText = computed(() => {
  if (!alert.value) return ''
  const texts: Record<string, string> = {
    'Red': 'Critical Emergency',
    'Orange': 'Immediate Action Required',
    'Yellow': 'Increase Vigilance',
    'Green': 'Monitor Situation'
  }
  return texts[alert.value.alert_level] || alert.value.alert_level
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const goBack = () => {
  router.push('/alerts')
}

onMounted(() => {
  const alertId = Number(route.params.id)
  if (alertId) {
    alertStore.fetchAlert(alertId)
  }
})
</script>
