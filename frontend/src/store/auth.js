import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const accessToken = ref(null)
  const isLoading = ref(false)
  const toast = useToast()

  // Computed
  const isAuthenticated = computed(() => !!accessToken.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  // Actions
  const login = async (credentials) => {
    isLoading.value = true
    try {
      const response = await api.post('/auth/login', credentials)
      const { access_token, refresh_token, user: userData } = response.data
      
      // Store tokens
      accessToken.value = access_token
      user.value = userData
      
      // Set auth header for future requests
      api.defaults.headers.common['Authorization'] = `Bearer ${access_token}`
      
      // Store refresh token in httpOnly cookie (handled by backend)
      document.cookie = `refresh_token=${refresh_token}; path=/; secure; samesite=strict`
      
      toast.success('Login successful!')
      return true
    } catch (error) {
      const message = error.response?.data?.error || 'Login failed'
      toast.error(message)
      return false
    } finally {
      isLoading.value = false
    }
  }

  const register = async (userData) => {
    isLoading.value = true
    try {
      const response = await api.post('/auth/register', userData)
      const { access_token, refresh_token, user: newUser } = response.data
      
      // Store tokens
      accessToken.value = access_token
      user.value = newUser
      
      // Set auth header
      api.defaults.headers.common['Authorization'] = `Bearer ${access_token}`
      
      // Store refresh token
      document.cookie = `refresh_token=${refresh_token}; path=/; secure; samesite=strict`
      
      toast.success('Registration successful!')
      return true
    } catch (error) {
      const message = error.response?.data?.error || 'Registration failed'
      toast.error(message)
      return false
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    try {
      // Call logout endpoint to invalidate refresh token
      if (accessToken.value) {
        await api.post('/auth/logout', {
          user_id: user.value?.id,
          refresh_token: getRefreshToken()
        })
      }
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      // Clear local state
      user.value = null
      accessToken.value = null
      delete api.defaults.headers.common['Authorization']
      
      // Clear refresh token cookie
      document.cookie = 'refresh_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT'
      
      toast.success('Logged out successfully')
    }
  }

  const refreshToken = async () => {
    try {
      const refreshTokenValue = getRefreshToken()
      if (!refreshTokenValue || !user.value?.id) {
        throw new Error('No refresh token available')
      }

      const response = await api.post('/auth/refresh', {
        user_id: user.value.id,
        refresh_token: refreshTokenValue
      })

      const { access_token, refresh_token } = response.data
      accessToken.value = access_token
      api.defaults.headers.common['Authorization'] = `Bearer ${access_token}`
      
      // Update refresh token cookie
      document.cookie = `refresh_token=${refresh_token}; path=/; secure; samesite=strict`
      
      return true
    } catch (error) {
      console.error('Token refresh failed:', error)
      await logout()
      return false
    }
  }

  const getRefreshToken = () => {
    const cookies = document.cookie.split(';')
    const refreshTokenCookie = cookies.find(cookie => 
      cookie.trim().startsWith('refresh_token=')
    )
    return refreshTokenCookie ? refreshTokenCookie.split('=')[1] : null
  }

  const checkAuth = async () => {
    if (!accessToken.value) {
      return await refreshToken()
    }
    return true
  }

  return {
    // State
    user,
    accessToken,
    isLoading,
    
    // Computed
    isAuthenticated,
    isAdmin,
    
    // Actions
    login,
    register,
    logout,
    refreshToken,
    checkAuth
  }
})