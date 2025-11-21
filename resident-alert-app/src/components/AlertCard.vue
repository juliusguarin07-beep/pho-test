<template>
  <div
    class="bg-white/70 backdrop-blur-sm rounded-xl border transition-all duration-300 cursor-pointer hover:shadow-md hover:border-gray-300 overflow-hidden group"
    :class="borderClass"
  >
    <!-- Top Color Bar -->
    <div :class="barColor" class="h-1.5 origin-left scale-x-100 group-hover:scale-x-100 transition-transform"></div>

    <div class="p-4">
      <!-- Header: Badge & Date -->
      <div class="flex items-start justify-between mb-3">
        <span
          class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
          :class="badgeClass"
        >
          <span :class="dotColor" class="inline-block w-1.5 h-1.5 rounded-full mr-1.5"></span>
          {{ alertLevelText }}
        </span>
        <span class="text-xs text-gray-400 font-light">
          {{ formatDate(alert.alert_start_date) }}
        </span>
      </div>

      <!-- Title -->
      <h3 class="text-base font-semibold text-gray-900 mb-3 leading-snug group-hover:text-gray-950 transition-colors">
        {{ alert.title }}
      </h3>

      <!-- Info Grid - Simplified -->
      <div class="space-y-2.5 mb-4">
        <!-- Disease -->
        <div class="flex items-center gap-2">
          <div :class="iconBgColor" class="rounded-md p-2 w-fit flex-shrink-0">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <div class="text-xs text-gray-500 font-medium">{{ alert.disease?.name || 'Unknown' }}</div>
          </div>
        </div>

        <!-- Location -->
        <div class="flex items-center gap-2">
          <div class="rounded-md p-2 w-fit flex-shrink-0 bg-blue-100">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <div class="text-xs text-gray-600 font-light truncate">
              {{ alert.municipality?.name || 'Multiple Areas' }}{{ alert.barangay?.name ? ` • ${alert.barangay.name}` : '' }}
            </div>
          </div>
        </div>

        <!-- Cases -->
        <div class="flex items-center gap-2">
          <div class="rounded-md p-2 w-fit flex-shrink-0 bg-orange-100">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <div class="text-xs text-gray-600 font-light">
              <span class="font-semibold text-gray-900">{{ alert.number_of_cases }}</span> confirmed
            </div>
          </div>
        </div>
      </div>

      <!-- Description Preview -->
      <p class="text-xs text-gray-600 line-clamp-2 mb-3 leading-relaxed font-light">
        {{ alert.description }}
      </p>

      <!-- Footer: View Details -->
      <div class="flex items-center justify-between pt-3 border-t border-gray-100/60 group-hover:border-gray-200 transition-colors">
        <span class="text-xs text-gray-500 font-light">View details</span>
        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </div>
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
    'Red': 'bg-red-100 text-red-700',
    'Orange': 'bg-orange-100 text-orange-700',
    'Yellow': 'bg-yellow-100 text-yellow-700',
    'Green': 'bg-green-100 text-green-700'
  }
  return classes[props.alert.alert_level] || 'bg-gray-100 text-gray-700'
})

const borderClass = computed(() => {
  const colors: Record<string, string> = {
    'Red': 'border-red-200',
    'Orange': 'border-orange-200',
    'Yellow': 'border-yellow-200',
    'Green': 'border-green-200'
  }
  return colors[props.alert.alert_level] || 'border-gray-200'
})

const barColor = computed(() => {
  const colors: Record<string, string> = {
    'Red': 'bg-red-500',
    'Orange': 'bg-orange-500',
    'Yellow': 'bg-yellow-500',
    'Green': 'bg-green-500'
  }
  return colors[props.alert.alert_level] || 'bg-gray-400'
})

const dotColor = computed(() => {
  const colors: Record<string, string> = {
    'Red': 'bg-red-600',
    'Orange': 'bg-orange-600',
    'Yellow': 'bg-yellow-600',
    'Green': 'bg-green-600'
  }
  return colors[props.alert.alert_level] || 'bg-gray-400'
})

const iconBgColor = computed(() => {
  const colors: Record<string, string> = {
    'Red': 'bg-red-100',
    'Orange': 'bg-orange-100',
    'Yellow': 'bg-yellow-100',
    'Green': 'bg-green-100'
  }
  return colors[props.alert.alert_level] || 'bg-gray-100'
})

const alertLevelText = computed(() => {
  const texts: Record<string, string> = {
    'Red': 'Critical Emergency',
    'Orange': 'Immediate Action',
    'Yellow': 'Increase Vigilance',
    'Green': 'Monitor'
  }
  return texts[props.alert.alert_level] || props.alert.alert_level
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
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
