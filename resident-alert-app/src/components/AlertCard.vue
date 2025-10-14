<template>
  <div
    class="card hover:shadow-xl transition-shadow duration-200 cursor-pointer border-l-4"
    :class="borderColor"
  >
    <!-- Alert Level Badge -->
    <div class="flex items-start justify-between mb-3">
      <span
        class="alert-badge"
        :class="badgeClass"
      >
        {{ alertLevelText }}
      </span>
      <span class="text-xs text-gray-500">
        {{ formatDate(alert.alert_start_date) }}
      </span>
    </div>

    <!-- Title -->
    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
      {{ alert.title }}
    </h3>

    <!-- Disease Info -->
    <div class="flex items-center gap-2 mb-2">
      <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <span class="text-sm font-semibold text-gray-900">{{ alert.disease?.name || 'Unknown Disease' }}</span>
    </div>

    <!-- Location -->
    <div class="flex items-center gap-2 mb-2">
      <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <span class="text-sm text-gray-600">
        {{ alert.barangay?.name || '' }}{{ alert.barangay?.name ? ', ' : '' }}{{ alert.municipality?.name || 'Multiple Areas' }}
      </span>
    </div>

    <!-- Cases Count -->
    <div class="flex items-center gap-2 mb-3">
      <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>
      <span class="text-sm text-gray-600">
        <span class="font-semibold text-gray-900">{{ alert.number_of_cases }}</span> confirmed cases
      </span>
    </div>

    <!-- Description Preview -->
    <p class="text-sm text-gray-600 line-clamp-2 mb-3">
      {{ alert.description }}
    </p>

    <!-- View Details Button -->
    <div class="flex items-center justify-end">
      <span class="text-sm font-semibold text-primary-600 flex items-center gap-1">
        View Details
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { OutbreakAlert } from '@/types'

interface Props {
  alert: OutbreakAlert
}

const props = defineProps<Props>()

const badgeClass = computed(() => {
  const classes: Record<string, string> = {
    'Red': 'alert-badge-red',
    'Orange': 'alert-badge-orange',
    'Yellow': 'alert-badge-yellow',
    'Green': 'alert-badge-green'
  }
  return classes[props.alert.alert_level] || 'alert-badge'
})

const borderColor = computed(() => {
  const colors: Record<string, string> = {
    'Red': 'border-red-500',
    'Orange': 'border-orange-500',
    'Yellow': 'border-yellow-500',
    'Green': 'border-green-500'
  }
  return colors[props.alert.alert_level] || 'border-gray-300'
})

const alertLevelText = computed(() => {
  const texts: Record<string, string> = {
    'Red': 'Critical Emergency',
    'Orange': 'Immediate Action Required',
    'Yellow': 'Increase Vigilance',
    'Green': 'Monitor Situation'
  }
  return texts[props.alert.alert_level] || props.alert.alert_level
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
