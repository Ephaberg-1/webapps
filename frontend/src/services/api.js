import axios from 'axios'
import { useAuthStore } from '@/store/auth'
import { useToast } from 'vue-toastification'

// Create axios instance
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8080',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Request interceptor
api.interceptors.request.use(
  async (config) => {
    // Get CSRF token for non-GET requests
    if (config.method !== 'get') {
      try {
        const csrfResponse = await axios.get(`${config.baseURL}/api/csrf`)
        config.headers['X-CSRF-Token'] = csrfResponse.data.csrf_token
      } catch (error) {
        console.warn('Failed to get CSRF token:', error)
      }
    }
    
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response
  },
  async (error) => {
    const originalRequest = error.config
    const authStore = useAuthStore()
    const toast = useToast()

    // Handle 401 errors (unauthorized)
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true
      
      try {
        // Try to refresh token
        const refreshed = await authStore.refreshToken()
        if (refreshed) {
          // Retry original request with new token
          return api(originalRequest)
        }
      } catch (refreshError) {
        console.error('Token refresh failed:', refreshError)
        await authStore.logout()
        toast.error('Session expired. Please login again.')
      }
    }

    // Handle other errors
    if (error.response?.status >= 500) {
      toast.error('Server error. Please try again later.')
    }

    return Promise.reject(error)
  }
)

export default api