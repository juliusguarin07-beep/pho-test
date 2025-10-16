<template>
  <AuthenticatedLayout>
    <Head title="Automatic Alert Details" />

    <div class="py-12">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <Link
                :href="route('automatic-alerts.index')"
                class="text-blue-600 hover:text-blue-900 mb-2 inline-flex items-center"
              >
                ← Back to Automatic Alerts
              </Link>
              <h2 class="text-3xl font-bold leading-tight text-gray-900">
                {{ alert.alert_title }}
              </h2>
              <div class="mt-2 flex items-center space-x-4">
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    getAlertLevelClass(alert.alert_level)
                  ]"
                >
                  {{ alert.alert_level.toUpperCase() }}
                </span>
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    getStatusClass(alert.status)
                  ]"
                >
                  {{ alert.status.toUpperCase() }}
                </span>
                <span class="text-sm text-gray-600">
                  Created {{ formatDate(alert.created_at) }}
                </span>
              </div>
            </div>
            <div class="flex space-x-3">
              <button
                v-if="alert.status === 'draft'"
                @click="publishAlert"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Publish Alert
              </button>
              <button
                v-if="alert.status === 'published'"
                @click="archiveAlert"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Archive Alert
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Alert Details -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Alert Details</h3>
              </div>
              <div class="px-6 py-4">
                <div class="prose max-w-none">
                  <div class="whitespace-pre-wrap">{{ alert.alert_details }}</div>
                </div>
              </div>
            </div>

            <!-- Health Advisory -->
            <div v-if="alert.health_advisory" class="bg-blue-50 border border-blue-200 rounded-lg">
              <div class="px-6 py-4 border-b border-blue-200">
                <h3 class="text-lg font-medium text-blue-900">Health Advisory</h3>
              </div>
              <div class="px-6 py-4">
                <div class="text-blue-800 whitespace-pre-wrap">{{ alert.health_advisory }}</div>
              </div>
            </div>

            <!-- Preventive Measures -->
            <div v-if="alert.preventive_measures" class="bg-green-50 border border-green-200 rounded-lg">
              <div class="px-6 py-4 border-b border-green-200">
                <h3 class="text-lg font-medium text-green-900">Preventive Measures</h3>
              </div>
              <div class="px-6 py-4">
                <div class="text-green-800 whitespace-pre-wrap">{{ alert.preventive_measures }}</div>
              </div>
            </div>

            <!-- Do's and Don'ts -->
            <div v-if="alert.dos_and_donts" class="bg-yellow-50 border border-yellow-200 rounded-lg">
              <div class="px-6 py-4 border-b border-yellow-200">
                <h3 class="text-lg font-medium text-yellow-900">Do's and Don'ts</h3>
              </div>
              <div class="px-6 py-4">
                <div class="text-yellow-800 whitespace-pre-wrap">{{ alert.dos_and_donts }}</div>
              </div>
            </div>

            <!-- Emergency Contacts -->
            <div v-if="alert.emergency_contacts" class="bg-red-50 border border-red-200 rounded-lg">
              <div class="px-6 py-4 border-b border-red-200">
                <h3 class="text-lg font-medium text-red-900">Emergency Contacts</h3>
              </div>
              <div class="px-6 py-4">
                <div class="text-red-800 whitespace-pre-wrap">{{ alert.emergency_contacts }}</div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Key Information -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Key Information</h3>
              </div>
              <div class="px-6 py-4 space-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Disease</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ alert.disease?.name }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Municipality</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ alert.municipality?.name }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Current Cases</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ alert.number_of_cases }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Threshold Reached</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ alert.threshold_reached }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Alert Level</dt>
                  <dd class="mt-1">
                    <span
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getAlertLevelClass(alert.alert_level)
                      ]"
                    >
                      {{ alert.alert_level.toUpperCase() }}
                    </span>
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Status</dt>
                  <dd class="mt-1">
                    <span
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getStatusClass(alert.status)
                      ]"
                    >
                      {{ alert.status.toUpperCase() }}
                    </span>
                  </dd>
                </div>
                <div v-if="alert.alert_start_date">
                  <dt class="text-sm font-medium text-gray-500">Alert Start Date</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(alert.alert_start_date) }}</dd>
                </div>
                <div v-if="alert.published_at">
                  <dt class="text-sm font-medium text-gray-500">Published At</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(alert.published_at) }}</dd>
                </div>
              </div>
            </div>

            <!-- System Information -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">System Information</h3>
              </div>
              <div class="px-6 py-4 space-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Automatic Alert</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      Yes
                    </span>
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Created By</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ alert.creator?.name || 'System' }}</dd>
                </div>
                <div v-if="alert.resolver">
                  <dt class="text-sm font-medium text-gray-500">Resolved By</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ alert.resolver.name }}</dd>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

interface Alert {
  id: number
  alert_title: string
  alert_details: string
  alert_level: string
  status: string
  number_of_cases: number
  threshold_reached: number
  alert_start_date?: string
  health_advisory?: string
  preventive_measures?: string
  dos_and_donts?: string
  emergency_contacts?: string
  created_at: string
  published_at?: string
  disease?: {
    id: number
    name: string
  }
  municipality?: {
    id: number
    name: string
  }
  creator?: {
    id: number
    name: string
  }
  resolver?: {
    id: number
    name: string
  }
}

interface Props {
  alert: Alert
}

const props = defineProps<Props>()

const getAlertLevelClass = (level: string) => {
  switch (level) {
    case 'moderate':
      return 'bg-yellow-100 text-yellow-800'
    case 'high':
      return 'bg-orange-100 text-orange-800'
    case 'severe':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusClass = (status: string) => {
  switch (status) {
    case 'draft':
      return 'bg-gray-100 text-gray-800'
    case 'published':
      return 'bg-green-100 text-green-800'
    case 'archived':
      return 'bg-blue-100 text-blue-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const publishAlert = () => {
  if (confirm('Are you sure you want to publish this alert? This will make it visible to residents.')) {
    router.post(route('automatic-alerts.publish', props.alert.id))
  }
}

const archiveAlert = () => {
  if (confirm('Are you sure you want to archive this alert?')) {
    router.post(route('automatic-alerts.archive', props.alert.id))
  }
}
</script>
