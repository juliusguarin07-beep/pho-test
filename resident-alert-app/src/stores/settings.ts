import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { alertService } from '@/services/api'
import type { Municipality, Barangay, UserSettings } from '@/types'

const SETTINGS_STORAGE_KEY = 'pho-alert-settings'

export const useSettingsStore = defineStore('settings', () => {
  // State
  const municipalities = ref<Municipality[]>([])
  const barangays = ref<Barangay[]>([])
  const settings = ref<UserSettings>({
    municipality_id: null,
    barangay_id: null,
    language: 'en',
    notifications_enabled: true
  })

  // Load settings from localStorage on init
  const loadSettings = () => {
    const stored = localStorage.getItem(SETTINGS_STORAGE_KEY)
    if (stored) {
      try {
        settings.value = JSON.parse(stored)
      } catch (err) {
        console.error('Error loading settings:', err)
      }
    }
  }

  // Save settings to localStorage
  const saveSettings = () => {
    localStorage.setItem(SETTINGS_STORAGE_KEY, JSON.stringify(settings.value))
  }

  // Getters
  const selectedMunicipality = computed(() =>
    municipalities.value.find(m => m.id === settings.value.municipality_id)
  )

  const selectedBarangay = computed(() =>
    barangays.value.find(b => b.id === settings.value.barangay_id)
  )

  const filteredBarangays = computed(() => {
    if (!settings.value.municipality_id) return []
    return barangays.value.filter(
      b => b.municipality_id === settings.value.municipality_id
    )
  })

  // Actions
  async function fetchMunicipalities() {
    try {
      const response = await alertService.getMunicipalities()
      if (response.success) {
        municipalities.value = response.data
      }
    } catch (err: any) {
      console.error('Error fetching municipalities:', err)
    }
  }

  async function fetchBarangays(municipalityId?: number) {
    try {
      const response = await alertService.getBarangays(municipalityId)
      if (response.success) {
        barangays.value = response.data
      }
    } catch (err: any) {
      console.error('Error fetching barangays:', err)
    }
  }

  function setMunicipality(municipalityId: number | null) {
    settings.value.municipality_id = municipalityId
    settings.value.barangay_id = null // Reset barangay when municipality changes
    saveSettings()
    if (municipalityId) {
      fetchBarangays(municipalityId)
    }
  }

  function setBarangay(barangayId: number | null) {
    settings.value.barangay_id = barangayId
    saveSettings()
  }

  function setLanguage(language: 'en' | 'tl') {
    settings.value.language = language
    saveSettings()
  }

  function setNotificationsEnabled(enabled: boolean) {
    settings.value.notifications_enabled = enabled
    saveSettings()
  }

  function resetSettings() {
    settings.value = {
      municipality_id: null,
      barangay_id: null,
      language: 'en',
      notifications_enabled: true
    }
    saveSettings()
  }

  // Initialize
  loadSettings()

  return {
    // State
    municipalities,
    barangays,
    settings,
    // Getters
    selectedMunicipality,
    selectedBarangay,
    filteredBarangays,
    // Actions
    fetchMunicipalities,
    fetchBarangays,
    setMunicipality,
    setBarangay,
    setLanguage,
    setNotificationsEnabled,
    resetSettings,
    loadSettings
  }
})
