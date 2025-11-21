<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Debug Map Data</h1>

    <div class="mb-4">
      <button @click="loadData" class="bg-blue-500 text-white px-4 py-2 rounded">
        Load Map Data
      </button>
    </div>

    <div v-if="loading" class="mb-4">
      <p>Loading...</p>
    </div>

    <div v-if="error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      <strong>Error:</strong> {{ error }}
    </div>

    <div v-if="rawData" class="mb-4">
      <h2 class="text-lg font-semibold mb-2">Raw API Response:</h2>
      <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">{{ JSON.stringify(rawData, null, 2) }}</pre>
    </div>

    <div v-if="mapData && mapData.length" class="mb-4">
      <h2 class="text-lg font-semibold mb-2">Processed Map Data ({{ mapData.length }} alerts):</h2>
      <div v-for="(alert, index) in mapData" :key="alert.id" class="mb-4 p-4 border rounded">
        <h3 class="font-bold">Alert {{ index + 1 }}: {{ alert.title }}</h3>
        <p><strong>ID:</strong> {{ alert.id }}</p>
        <p><strong>Disease:</strong> {{ alert.disease }}</p>
        <p><strong>Municipality:</strong> {{ alert.municipality }}</p>
        <p><strong>Alert Level:</strong> {{ alert.alert_level }}</p>
        <p><strong>Cases:</strong> {{ alert.cases }}</p>
        <p><strong>Coordinates:</strong> {{ alert.coordinates.latitude }}, {{ alert.coordinates.longitude }}</p>
        <p><strong>Date:</strong> {{ alert.date }}</p>
        <p><strong>Updated:</strong> {{ alert.updated_at }}</p>
      </div>
    </div>

    <div class="mt-4">
      <router-link to="/map" class="text-blue-500 hover:underline">
        Go back to Map
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { alertService } from '@/services/api'
import type { MapAlert } from '@/types'

const loading = ref(false)
const error = ref('')
const rawData = ref<any>(null)
const mapData = ref<MapAlert[]>([])

const loadData = async () => {
  loading.value = true
  error.value = ''
  rawData.value = null
  mapData.value = []

  try {
    console.log('Making API request...')
    const response = await alertService.getMapData()
    console.log('API Response:', response)

    rawData.value = response

    if (response.success) {
      mapData.value = response.data
      console.log('Map data processed:', mapData.value)
    } else {
      error.value = 'API returned unsuccessful response'
    }
  } catch (err: any) {
    console.error('Error loading data:', err)
    error.value = err.message || 'Failed to load data'
  } finally {
    loading.value = false
  }
}
</script>
