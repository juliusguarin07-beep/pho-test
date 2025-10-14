import axios from 'axios'
import type {
  OutbreakAlert,
  AlertStatistics,
  MapAlert,
  Municipality,
  Barangay,
  ApiResponse,
  PaginatedResponse
} from '@/types'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

export const alertService = {
  // Get all active alerts
  async getAlerts(params?: {
    municipality_id?: number
    barangay_id?: number
    alert_level?: string
    page?: number
  }): Promise<ApiResponse<PaginatedResponse<OutbreakAlert>>> {
    const response = await api.get('/alerts', { params })
    return response.data
  },

  // Get single alert by ID
  async getAlert(id: number): Promise<ApiResponse<OutbreakAlert>> {
    const response = await api.get(`/alerts/${id}`)
    return response.data
  },

  // Get alert statistics
  async getStatistics(params?: {
    municipality_id?: number
    barangay_id?: number
  }): Promise<ApiResponse<AlertStatistics>> {
    const response = await api.get('/alerts-statistics', { params })
    return response.data
  },

  // Get map data
  async getMapData(params?: {
    municipality_id?: number
  }): Promise<ApiResponse<MapAlert[]>> {
    const response = await api.get('/alerts-map', { params })
    return response.data
  },

  // Get recent alerts (last 7 days)
  async getRecentAlerts(params?: {
    municipality_id?: number
    barangay_id?: number
  }): Promise<ApiResponse<OutbreakAlert[]>> {
    const response = await api.get('/alerts-recent', { params })
    return response.data
  },

  // Get municipalities list
  async getMunicipalities(): Promise<ApiResponse<Municipality[]>> {
    const response = await api.get('/municipalities')
    return response.data
  },

  // Get barangays list
  async getBarangays(municipalityId?: number): Promise<ApiResponse<Barangay[]>> {
    const params = municipalityId ? { municipality_id: municipalityId } : undefined
    const response = await api.get('/barangays', { params })
    return response.data
  }
}

export default api
