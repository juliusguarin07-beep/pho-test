export interface OutbreakAlert {
  id: number
  title: string
  description: string
  alert_level: 'Red' | 'Orange' | 'Yellow' | 'Green'
  disease_id: number
  disease?: Disease
  municipality_id: number
  municipality?: Municipality
  barangay_id?: number | null
  barangay?: Barangay | null
  affected_areas: string
  number_of_cases: number
  preventive_measures: string
  dos_and_donts: string
  emergency_contacts: string
  alert_start_date: string
  alert_end_date?: string | null
  status: 'published' | 'draft' | 'archived'
  issued_by: number
  issuedBy?: User
  created_at: string
  updated_at: string
}

export interface Disease {
  id: number
  name: string
  category: string
  description?: string
  symptoms?: string
  prevention_measures?: string
}

export interface Municipality {
  id: number
  name: string
}

export interface Barangay {
  id: number
  name: string
  municipality_id: number
}

export interface User {
  id: number
  name: string
  position?: string
}

export interface AlertStatistics {
  total_active_alerts: number
  red_alerts: number
  orange_alerts: number
  yellow_alerts: number
  green_alerts: number
  diseases_tracked: number
  latest_update: string | null
}

export interface MapAlert {
  id: number
  title: string
  disease: string
  municipality: string
  barangay: string
  alert_level: string
  cases: number
  date: string
  updated_at: string
  affected_barangays?: string[]
  coordinates: {
    latitude: number
    longitude: number
  }
}

export interface ApiResponse<T> {
  success: boolean
  data: T
  message: string
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  links: any[]
}

export interface UserSettings {
  municipality_id: number | null
  barangay_id: number | null
  language: 'en' | 'tl'
  notifications_enabled: boolean
}
