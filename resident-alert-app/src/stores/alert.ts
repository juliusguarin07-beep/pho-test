import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { alertService } from '@/services/api'
import type { OutbreakAlert, AlertStatistics, MapAlert } from '@/types'

export const useAlertStore = defineStore('alert', () => {
  // State
  const alerts = ref<OutbreakAlert[]>([])
  const currentAlert = ref<OutbreakAlert | null>(null)
  const statistics = ref<AlertStatistics | null>(null)
  const mapData = ref<MapAlert[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const currentPage = ref(1)
  const lastPage = ref(1)

  // Getters
  const redAlerts = computed(() =>
    alerts.value.filter(alert => alert.alert_level === 'Red')
  )

  const orangeAlerts = computed(() =>
    alerts.value.filter(alert => alert.alert_level === 'Orange')
  )

  const yellowAlerts = computed(() =>
    alerts.value.filter(alert => alert.alert_level === 'Yellow')
  )

  const greenAlerts = computed(() =>
    alerts.value.filter(alert => alert.alert_level === 'Green')
  )

  const criticalAlerts = computed(() =>
    alerts.value.filter(alert => alert.alert_level === 'Red' || alert.alert_level === 'Orange')
  )

  // Actions
  async function fetchAlerts(params?: {
    municipality_id?: number
    barangay_id?: number
    alert_level?: string
    page?: number
  }) {
    loading.value = true
    error.value = null
    try {
      const response = await alertService.getAlerts(params)
      if (response.success) {
        alerts.value = response.data.data
        currentPage.value = response.data.current_page
        lastPage.value = response.data.last_page
      }
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch alerts'
      console.error('Error fetching alerts:', err)
    } finally {
      loading.value = false
    }
  }

  async function fetchAlert(id: number) {
    loading.value = true
    error.value = null
    try {
      const response = await alertService.getAlert(id)
      if (response.success) {
        currentAlert.value = response.data
      }
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch alert details'
      console.error('Error fetching alert:', err)
    } finally {
      loading.value = false
    }
  }

  async function fetchStatistics(params?: {
    municipality_id?: number
    barangay_id?: number
  }) {
    try {
      const response = await alertService.getStatistics(params)
      if (response.success) {
        statistics.value = response.data
      }
    } catch (err: any) {
      console.error('Error fetching statistics:', err)
    }
  }

  async function fetchMapData(municipalityId?: number) {
    loading.value = true
    error.value = null
    try {
      const params = municipalityId ? { municipality_id: municipalityId } : undefined
      const response = await alertService.getMapData(params)
      if (response.success) {
        mapData.value = response.data
      }
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch map data'
      console.error('Error fetching map data:', err)
    } finally {
      loading.value = false
    }
  }

  async function fetchRecentAlerts(params?: {
    municipality_id?: number
    barangay_id?: number
  }) {
    loading.value = true
    error.value = null
    try {
      const response = await alertService.getRecentAlerts(params)
      if (response.success) {
        alerts.value = response.data
      }
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch recent alerts'
      console.error('Error fetching recent alerts:', err)
    } finally {
      loading.value = false
    }
  }

  function clearCurrentAlert() {
    currentAlert.value = null
  }

  return {
    // State
    alerts,
    currentAlert,
    statistics,
    mapData,
    loading,
    error,
    currentPage,
    lastPage,
    // Getters
    redAlerts,
    orangeAlerts,
    yellowAlerts,
    greenAlerts,
    criticalAlerts,
    // Actions
    fetchAlerts,
    fetchAlert,
    fetchStatistics,
    fetchMapData,
    fetchRecentAlerts,
    clearCurrentAlert
  }
})
