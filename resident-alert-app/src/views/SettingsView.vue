<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-4 py-6">
      <h1 class="text-2xl font-bold">Settings</h1>
      <p class="text-primary-100 text-sm mt-1">Customize your alert preferences</p>
    </header>

    <!-- Settings Content -->
    <div class="px-4 py-6 space-y-4">

      <!-- Location Settings -->
      <div class="card">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          </svg>
          Location Filter
        </h2>
        <p class="text-sm text-gray-600 mb-4">Select your location to see relevant alerts</p>

        <!-- Municipality Select -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Municipality</label>

          <!-- Loading state -->
          <div v-if="municipalities.length === 0" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
            Loading municipalities...
          </div>

          <!-- Municipality dropdown -->
          <select
            v-else
            v-model="selectedMunicipality"
            @change="onMunicipalityChange"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          >
            <option :value="null">All Municipalities</option>
            <option
              v-for="municipality in municipalities"
              :key="municipality.id"
              :value="municipality.id"
            >
              {{ municipality.name }}
            </option>
          </select>

          <!-- Debug info -->
          <p class="text-xs text-gray-500 mt-1">
            {{ municipalities.length }} municipalities available
          </p>
        </div>

        <!-- Barangay Select -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Barangay</label>
          <select
            v-model="selectedBarangay"
            @change="onBarangayChange"
            :disabled="!selectedMunicipality"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
          >
            <option :value="null">
              {{ selectedMunicipality ? 'All Barangays' : 'Select municipality first' }}
            </option>
            <option
              v-for="barangay in filteredBarangays"
              :key="barangay.id"
              :value="barangay.id"
            >
              {{ barangay.name }}
            </option>
          </select>
          <p class="text-xs text-gray-500 mt-2">
            Select a municipality first to choose a barangay
          </p>
        </div>

        <!-- Current Selection Display -->
        <div v-if="selectedMunicipality || selectedBarangay" class="mt-4 p-3 bg-primary-50 rounded-lg border border-primary-200">
          <div class="text-sm">
            <span class="font-semibold text-primary-900">Filtering alerts for:</span>
            <div class="mt-1 text-primary-700">
              {{ selectedMunicipality ? municipalities.find(m => m.id === selectedMunicipality)?.name : 'All Areas' }}
              <span v-if="selectedBarangay">
                → {{ filteredBarangays.find(b => b.id === selectedBarangay)?.name }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Notification Settings -->
      <div class="card">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          Notifications
        </h2>
        <div class="flex items-center justify-between">
          <div class="flex-1">
            <div class="font-semibold text-gray-900">Push Notifications</div>
            <div class="text-sm text-gray-600 mt-1">Receive alerts for new outbreaks</div>
          </div>
          <button
            @click="toggleNotifications"
            :class="[
              'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200',
              notificationsEnabled ? 'bg-primary-600' : 'bg-gray-200'
            ]"
          >
            <span
              :class="[
                'inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200',
                notificationsEnabled ? 'translate-x-6' : 'translate-x-1'
              ]"
            />
          </button>
        </div>
        <div v-if="!notificationsEnabled" class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
          <p class="text-sm text-yellow-800">
            ⚠️ You won't receive urgent outbreak alerts. Enable notifications to stay informed.
          </p>
        </div>
      </div>

      <!-- About Section -->
      <div class="card bg-gradient-to-br from-primary-50 to-blue-50 border border-primary-200">
        <h2 class="text-lg font-bold text-primary-900 mb-3">About PHO Alert</h2>
        <p class="text-sm text-primary-800 leading-relaxed mb-4">
          The Provincial Health Office Alert System provides real-time outbreak alerts and health advisories to keep Pangasinan residents informed and safe.
        </p>
        <div class="space-y-2 text-sm text-primary-700">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>Real-time outbreak alerts</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>Interactive disease map</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>Prevention guidelines</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>Emergency contacts</span>
          </div>
        </div>
      </div>

      <!-- Version & Privacy -->
      <div class="card text-center">
        <p class="text-sm text-gray-600 mb-2">
          <span class="font-semibold">Version:</span> 1.0.0
        </p>
        <p class="text-xs text-gray-500">
          © 2025 Provincial Health Office - Pangasinan
        </p>
        <div class="mt-3 pt-3 border-t border-gray-200">
          <button class="text-sm text-primary-600 font-semibold hover:text-primary-700">
            Privacy Policy
          </button>
          <span class="mx-2 text-gray-300">•</span>
          <button class="text-sm text-primary-600 font-semibold hover:text-primary-700">
            Terms of Service
          </button>
        </div>
      </div>

      <!-- Reset Button -->
      <button
        @click="resetSettings"
        class="w-full py-3 bg-red-50 text-red-700 font-semibold rounded-lg hover:bg-red-100 transition-colors border border-red-200"
      >
        Reset All Settings
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSettingsStore } from '@/stores/settings'

const settingsStore = useSettingsStore()

const selectedMunicipality = ref<number | null>(null)
const selectedBarangay = ref<number | null>(null)
const notificationsEnabled = ref(true)

const municipalities = computed(() => settingsStore.municipalities)
const filteredBarangays = computed(() => settingsStore.filteredBarangays)

const onMunicipalityChange = () => {
  selectedBarangay.value = null
  settingsStore.setMunicipality(selectedMunicipality.value)
}

const onBarangayChange = () => {
  settingsStore.setBarangay(selectedBarangay.value)
}

const toggleNotifications = () => {
  notificationsEnabled.value = !notificationsEnabled.value
  settingsStore.setNotificationsEnabled(notificationsEnabled.value)

  if (notificationsEnabled.value) {
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission()
    }
  }
}

const resetSettings = () => {
  if (confirm('Are you sure you want to reset all settings? This will clear your location and preferences.')) {
    settingsStore.resetSettings()
    loadSettings()
  }
}

const loadSettings = () => {
  selectedMunicipality.value = settingsStore.settings.municipality_id
  selectedBarangay.value = settingsStore.settings.barangay_id
  notificationsEnabled.value = settingsStore.settings.notifications_enabled
}

onMounted(async () => {
  // Ensure municipalities are loaded
  if (settingsStore.municipalities.length === 0) {
    await settingsStore.fetchMunicipalities()
  }

  // Load user settings
  loadSettings()

  // If user has a selected municipality but no barangays loaded, fetch them
  if (selectedMunicipality.value && settingsStore.filteredBarangays.length === 0) {
    await settingsStore.fetchBarangays(selectedMunicipality.value)
  }
})
</script>
