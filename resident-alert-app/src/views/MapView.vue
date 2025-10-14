<template>
  <div class="flex flex-col h-full">
    <!-- Header -->
    <header class="bg-primary-600 text-white px-4 py-4 shadow-lg z-50">
      <h1 class="text-xl font-bold">Alert Map</h1>
      <p class="text-primary-100 text-sm">Outbreak locations across Pangasinan</p>
    </header>

    <!-- Legend -->
    <div class="bg-white border-b border-gray-200 px-4 py-3 z-40">
      <div class="flex items-center gap-4 text-xs">
        <button
          @click="toggleFilter('Red')"
          class="flex items-center gap-1 px-2 py-1 rounded transition-colors"
          :class="isFilterActive('Red') ? 'bg-red-100 border border-red-300' : 'hover:bg-gray-100'"
        >
          <div class="w-4 h-4 rounded-full bg-red-500" :class="isFilterActive('Red') ? '' : 'opacity-50'"></div>
          <span class="text-gray-700">Critical Emergency</span>
        </button>
        <button
          @click="toggleFilter('Orange')"
          class="flex items-center gap-1 px-2 py-1 rounded transition-colors"
          :class="isFilterActive('Orange') ? 'bg-orange-100 border border-orange-300' : 'hover:bg-gray-100'"
        >
          <div class="w-4 h-4 rounded-full bg-orange-500" :class="isFilterActive('Orange') ? '' : 'opacity-50'"></div>
          <span class="text-gray-700">Immediate Action</span>
        </button>
        <button
          @click="toggleFilter('Yellow')"
          class="flex items-center gap-1 px-2 py-1 rounded transition-colors"
          :class="isFilterActive('Yellow') ? 'bg-yellow-100 border border-yellow-300' : 'hover:bg-gray-100'"
        >
          <div class="w-4 h-4 rounded-full bg-yellow-500" :class="isFilterActive('Yellow') ? '' : 'opacity-50'"></div>
          <span class="text-gray-700">Increase Vigilance</span>
        </button>
        <button
          @click="toggleFilter('Green')"
          class="flex items-center gap-1 px-2 py-1 rounded transition-colors"
          :class="isFilterActive('Green') ? 'bg-green-100 border border-green-300' : 'hover:bg-gray-100'"
        >
          <div class="w-4 h-4 rounded-full bg-green-500" :class="isFilterActive('Green') ? '' : 'opacity-50'"></div>
          <span class="text-gray-700">Monitor Situation</span>
        </button>
        <button
          v-if="activeFilters.length > 0"
          @click="clearFilters"
          class="ml-2 px-2 py-1 text-gray-500 hover:text-gray-700 text-xs underline"
        >
          Clear filters ({{ activeFilters.length }})
        </button>
      </div>
    </div>

    <!-- Map Container -->
    <div class="flex-1 relative">
      <div ref="mapContainer" class="w-full h-full"></div>

      <!-- Loading Overlay -->
      <div v-if="loading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-50">
        <div class="text-center">
          <div class="spinner mx-auto mb-2"></div>
          <p class="text-sm text-gray-600">Loading map data...</p>
        </div>
      </div>
    </div>

    <!-- Alert Info Modal (floating) -->
    <transition name="fade">
      <div
        v-if="selectedMunicipality"
        @click="closeModal"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
        style="z-index: 10000;"
      >
        <div @click.stop class="bg-white rounded-2xl shadow-2xl max-w-md w-full animate-scale-in">
          <!-- Header with close button -->
          <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Alert Details</h3>
            <button @click="closeModal" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Content -->
          <div class="p-6 space-y-4">
            <!-- Diseases in this municipality -->
            <div class="space-y-3">
              <div class="flex items-center gap-2">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-50 flex items-center justify-center">
                  <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Disease Alerts in {{ selectedMunicipality }}</p>
              </div>

              <div class="space-y-2">
                <div v-for="disease in getMunicipalityDiseases(selectedMunicipality)" :key="disease.disease"
                     class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ disease.disease }}</p>
                  </div>
                  <span class="px-2 py-1 rounded-full text-xs font-bold uppercase" :class="getBadgeClass(disease.alert_level)">
                    {{ getAlertLevelText(disease.alert_level) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Municipality -->
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</p>
                <p class="text-base font-semibold text-gray-900 mt-0.5">{{ selectedMunicipality }}</p>
              </div>
            </div>

            <!-- Affected Barangays -->
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Affected Barangays</p>
                <div class="mt-1 flex flex-wrap gap-1">
                  <span v-for="barangay in getMunicipalityBarangays(selectedMunicipality)" :key="barangay"
                        class="inline-block px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                    {{ barangay }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Last Update -->
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Update</p>
                <p class="text-base font-semibold text-gray-900 mt-0.5">{{ getLatestUpdate(selectedMunicipality) }}</p>
              </div>
            </div>
          </div>

          <!-- Footer with action button -->
          <div class="p-5 bg-gray-50 rounded-b-2xl border-t border-gray-100">
            <button @click="closeModal" class="w-full btn-primary py-3 font-semibold">
              Close
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { useAlertStore } from '@/stores/alert'
import { useSettingsStore } from '@/stores/settings'
import type { MapAlert } from '@/types'

const router = useRouter()
const alertStore = useAlertStore()
const settingsStore = useSettingsStore()

const mapContainer = ref<HTMLElement | null>(null)
const map = ref<L.Map | null>(null)
const markers = ref<L.Marker[]>([])
const selectedMunicipality = ref<string | null>(null)
const activeFilters = ref<string[]>([])

const loading = computed(() => alertStore.loading)
const mapData = computed(() => alertStore.mapData)
const filteredMapData = computed(() => {
  if (activeFilters.value.length === 0) {
    return mapData.value
  }
  return mapData.value.filter(alert => activeFilters.value.includes(alert.alert_level))
})

// Close modal
const closeModal = () => {
  console.log('Closing modal')
  selectedMunicipality.value = null
}

// Filter functions
const toggleFilter = (alertLevel: string) => {
  const index = activeFilters.value.indexOf(alertLevel)
  if (index > -1) {
    activeFilters.value.splice(index, 1)
  } else {
    activeFilters.value.push(alertLevel)
  }
}

const isFilterActive = (alertLevel: string) => {
  return activeFilters.value.length === 0 || activeFilters.value.includes(alertLevel)
}

const clearFilters = () => {
  activeFilters.value = []
}

// Get all diseases in a municipality
const getMunicipalityDiseases = (municipality: string) => {
  if (!mapData.value) return []
  const diseases = mapData.value
    .filter(alert => alert.municipality === municipality)
    .map(alert => ({
      disease: alert.disease,
      alert_level: alert.alert_level
    }))

  // Remove duplicates by disease name, keeping the highest alert level
  const uniqueDiseases = diseases.reduce((acc, current) => {
    const existing = acc.find(item => item.disease === current.disease)
    if (!existing) {
      acc.push(current)
    } else {
      // Keep the higher priority alert level (Red > Orange > Yellow > Green)
      const priority: Record<string, number> = { 'Red': 4, 'Orange': 3, 'Yellow': 2, 'Green': 1 }
      const currentPriority = priority[current.alert_level] || 0
      const existingPriority = priority[existing.alert_level] || 0
      if (currentPriority > existingPriority) {
        existing.alert_level = current.alert_level
      }
    }
    return acc
  }, [] as Array<{ disease: string; alert_level: string }>)

  return uniqueDiseases
}

// Get all affected barangays in a municipality
const getMunicipalityBarangays = (municipality: string) => {
  if (!mapData.value) return []

  const allBarangays: string[] = []

  const municipalityAlerts = mapData.value.filter(alert => alert.municipality === municipality)

  municipalityAlerts.forEach(alert => {
    // Check if affected_barangays exists and is an array
    if (alert.affected_barangays && Array.isArray(alert.affected_barangays)) {
      allBarangays.push(...alert.affected_barangays)
    }
    // Also check for single barangay field as fallback
    if (alert.barangay) {
      allBarangays.push(alert.barangay)
    }
  })

  return [...new Set(allBarangays)] // Remove duplicates
}

// Get the latest update for a municipality
const getLatestUpdate = (municipality: string) => {
  if (!mapData.value || !municipality) return ''
  const municipalityAlerts = mapData.value.filter(alert => alert.municipality === municipality)
  if (municipalityAlerts.length === 0) return ''

  // Find the most recent update
  const latest = municipalityAlerts.reduce((latest, current) =>
    new Date(current.updated_at) > new Date(latest.updated_at) ? current : latest
  )
  return formatDate(latest.updated_at)
}

// Initialize map
const initMap = () => {
  try {
    console.log('Initializing map...')
    if (!mapContainer.value) {
      console.error('Map container not found')
      return
    }

    if (map.value) {
      console.log('Map already exists, removing...')
      map.value.remove()
    }

    console.log('Creating new map...')
    // Pangasinan center coordinates
    map.value = L.map(mapContainer.value).setView([15.8949, 120.2863], 10)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 18
    }).addTo(map.value)

    console.log('Map initialized successfully')
  } catch (error) {
    console.error('Error initializing map:', error)
  }
}

// Create marker with custom color
const createMarker = (alert: MapAlert) => {
  const colors: Record<string, string> = {
    'Red': '#ef4444',
    'Orange': '#f97316',
    'Yellow': '#eab308',
    'Green': '#22c55e'
  }

  const color = colors[alert.alert_level] || '#6b7280'

  // Make critical alerts (Red) larger for visibility
  const size = alert.alert_level === 'Red' ? 40 : 32
  const height = alert.alert_level === 'Red' ? 52 : 42

  const icon = L.divIcon({
    className: 'custom-marker',
    html: `
      <div style="position: relative; cursor: pointer;">
        <svg width="${size}" height="${height}" viewBox="0 0 32 42" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M16 0C7.16344 0 0 7.16344 0 16C0 24.8366 16 42 16 42C16 42 32 24.8366 32 16C32 7.16344 24.8366 0 16 0Z" fill="${color}" stroke="#000" stroke-width="1"/>
          <circle cx="16" cy="16" r="6" fill="white"/>
        </svg>
        <div style="position: absolute; top: ${height * 0.24}px; left: 50%; transform: translateX(-50%); font-weight: bold; font-size: 10px; color: ${color};">
          ${alert.cases || 0}
        </div>
      </div>
    `,
    iconSize: [size, height],
    iconAnchor: [size/2, height],
    popupAnchor: [0, -height]
  })

  const marker = L.marker([alert.coordinates.latitude, alert.coordinates.longitude], { icon })

  // Add click event to the marker
  marker.on('click', () => {
    selectedMunicipality.value = alert.municipality
    // Center the map on the clicked marker
    if (map.value) {
      map.value.setView([alert.coordinates.latitude, alert.coordinates.longitude], 13)
    }
  })

  return marker
}

// Update markers
const updateMarkers = () => {
  if (!map.value) return

  // Clear existing markers
  markers.value.forEach(marker => marker.remove())
  markers.value = []

  // Add new markers using filtered data
  filteredMapData.value.forEach((alert) => {
    const marker = createMarker(alert)
    marker.addTo(map.value!)
    markers.value.push(marker)
  })

  // Fit bounds if there are markers
  if (markers.value.length > 0) {
    const group = L.featureGroup(markers.value)
    const bounds = group.getBounds()

    if (markers.value.length === 1) {
      // Single marker - center on it with appropriate zoom
      const marker = markers.value[0]
      const latlng = marker.getLatLng()
      map.value.setView([latlng.lat, latlng.lng], 12)
      console.log('Centered on single marker')
    } else {
      // Multiple markers - fit bounds with padding
      map.value.fitBounds(bounds.pad(0.1))
      console.log('Map bounds fitted for multiple markers')
    }
  }
}

// Load map data
const loadMapData = async () => {
  await alertStore.fetchMapData()
}

const viewAlertDetails = (id: number) => {
  router.push(`/alerts/${id}`)
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now.getTime() - date.getTime())
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 0) return 'Today'
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays} days ago`

  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const getBorderColor = (level: string) => {
  const colors: Record<string, string> = {
    'Red': 'border-red-500',
    'Orange': 'border-orange-500',
    'Yellow': 'border-yellow-500',
    'Green': 'border-green-500'
  }
  return colors[level] || 'border-gray-300'
}

const getBadgeClass = (level: string) => {
  const classes: Record<string, string> = {
    'Red': 'alert-badge-red',
    'Orange': 'alert-badge-orange',
    'Yellow': 'alert-badge-yellow',
    'Green': 'alert-badge-green'
  }
  return classes[level] || 'bg-gray-100 text-gray-700'
}

const getAlertLevelText = (level: string) => {
  const texts: Record<string, string> = {
    'Red': 'Critical Emergency',
    'Orange': 'Immediate Action Required',
    'Yellow': 'Increase Vigilance',
    'Green': 'Monitor Situation'
  }
  return texts[level] || level
}

// Watch for municipality changes (temporarily disabled for debugging)
// watch(
//   () => settingsStore.settings.municipality_id,
//   () => {
//     loadMapData()
//   }
// )

// Watch for map data changes
watch(mapData, () => {
  updateMarkers()
})

// Watch for filter changes
watch(activeFilters, () => {
  updateMarkers()
}, { deep: true })

// Watch for selectedMunicipality changes (debugging)
watch(selectedMunicipality, (newVal) => {
  console.log('selectedMunicipality changed to:', newVal)
  if (newVal) {
    console.log('Modal should be visible now!')
  }
})

onMounted(() => {
  // Add a small delay to ensure DOM is fully ready
  setTimeout(() => {
    initMap()
    loadMapData()
  }, 100)
})

onUnmounted(() => {
  if (map.value) {
    map.value.remove()
    map.value = null
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes scale-in {
  0% {
    transform: scale(0.9);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out;
}
</style>

<style>
.custom-marker {
  background: transparent !important;
  border: none !important;
  cursor: pointer !important;
  pointer-events: auto !important;
}

.custom-marker > div {
  cursor: pointer;
  pointer-events: auto;
}
</style>
