<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="px-4 py-4">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="text-gray-500 text-sm mt-0.5">Manage your preferences</p>
      </div>
    </header>

    <!-- Settings Content -->
    <div class="px-4 py-6 space-y-5 pb-24">

      <!-- Location Settings -->
      <section class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-2 mb-4">
          <div class="bg-blue-100 rounded-lg p-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            </svg>
          </div>
          <div>
            <h2 class="font-bold text-gray-900">Location</h2>
            <p class="text-xs text-gray-500 mt-0.5">Choose your area</p>
          </div>
        </div>

        <!-- Municipality Select -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Municipality</label>
          <div v-if="municipalities.length === 0" class="w-full px-4 py-3 bg-gray-50 rounded-lg text-gray-500 text-sm">
            Loading...
          </div>
          <select
            v-else
            v-model="selectedMunicipality"
            @change="onMunicipalityChange"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
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
        </div>

        <!-- Barangay Select -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Barangay (Optional)</label>
          <select
            v-model="selectedBarangay"
            @change="onBarangayChange"
            :disabled="!selectedMunicipality"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:cursor-not-allowed transition-colors"
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
        </div>

        <!-- Current Selection Display -->
        <div v-if="selectedMunicipality || selectedBarangay" class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
          <div class="text-sm">
            <span class="font-semibold text-blue-900">Active Filter:</span>
            <div class="mt-2 text-blue-800 font-medium">
              {{ selectedMunicipality ? municipalities.find(m => m.id === selectedMunicipality)?.name : 'All Areas' }}
              <span v-if="selectedBarangay" class="block text-xs text-blue-700 mt-1">
                └─ {{ filteredBarangays.find(b => b.id === selectedBarangay)?.name }}
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- Notification Settings -->
      <section class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3 flex-1">
            <div class="bg-orange-100 rounded-lg p-2">
              <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </div>
            <div>
              <h3 class="font-bold text-gray-900">Push Notifications</h3>
              <p class="text-xs text-gray-500 mt-0.5">Get alerts when outbreaks occur</p>
            </div>
          </div>
          <button
            @click="toggleNotifications"
            :class="[
              'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200',
              notificationsEnabled ? 'bg-green-500' : 'bg-gray-300'
            ]"
          >
            <span
              :class="[
                'inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200',
                notificationsEnabled ? 'translate-x-5.5' : 'translate-x-1'
              ]"
            />
          </button>
        </div>
      </section>

      <!-- About Section -->
      <section class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-200 p-4">
        <div class="flex items-center gap-2 mb-3">
          <div class="text-xl">ℹ️</div>
          <h2 class="font-bold text-blue-900">About PHO Alert</h2>
        </div>
        <p class="text-sm text-blue-800 leading-relaxed mb-4">
          Stay informed about health outbreaks in Pangasinan with real-time alerts and important health advisories.
        </p>
        <div class="grid grid-cols-2 gap-2">
          <div class="flex items-start gap-2 text-xs text-blue-700">
            <span class="mt-0.5">✓</span>
            <span>Real-time alerts</span>
          </div>
          <div class="flex items-start gap-2 text-xs text-blue-700">
            <span class="mt-0.5">✓</span>
            <span>Disease tracking</span>
          </div>
          <div class="flex items-start gap-2 text-xs text-blue-700">
            <span class="mt-0.5">✓</span>
            <span>Health guidelines</span>
          </div>
          <div class="flex items-start gap-2 text-xs text-blue-700">
            <span class="mt-0.5">✓</span>
            <span>Emergency contacts</span>
          </div>
        </div>
      </section>

      <!-- App Info -->
      <section class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-xs text-gray-600">
          <span class="font-semibold">Version:</span> 1.0.0
        </p>
        <p class="text-xs text-gray-500 mt-1">
          © 2025 Provincial Health Office
        </p>
      </section>

      <!-- Reset Button -->
      <button
        @click="resetSettings"
        class="w-full py-3 bg-red-50 text-red-700 font-semibold rounded-xl hover:bg-red-100 transition-colors border border-red-200 text-sm"
      >
        Reset Settings
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
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission()
    }
  }
}

const resetSettings = () => {
  if (confirm('Reset all settings? Your preferences will be cleared.')) {
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
  if (settingsStore.municipalities.length === 0) {
    await settingsStore.fetchMunicipalities()
  }
  loadSettings()
  if (selectedMunicipality.value && settingsStore.filteredBarangays.length === 0) {
    await settingsStore.fetchBarangays(selectedMunicipality.value)
  }
})
</script>
