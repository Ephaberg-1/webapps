<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600">Welcome back, {{ authStore.user?.full_name }}!</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card">
          <div class="card-body">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <AcademicCapIcon class="h-8 w-8 text-primary-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.totalBookings }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <CheckCircleIcon class="h-8 w-8 text-green-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Completed Classes</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.completedClasses }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <ClockIcon class="h-8 w-8 text-yellow-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Upcoming Classes</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.upcomingClasses }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Bookings -->
      <div class="card mb-8">
        <div class="card-header">
          <h2 class="text-xl font-semibold text-gray-900">Recent Bookings</h2>
        </div>
        <div class="card-body">
          <div v-if="loading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
          
          <div v-else-if="bookings.length === 0" class="text-center py-8">
            <CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings yet</h3>
            <p class="mt-1 text-sm text-gray-500">
              Get started by booking your first cybersecurity class.
            </p>
            <div class="mt-6">
              <router-link to="/packages" class="btn-primary">
                Browse Classes
              </router-link>
            </div>
          </div>
          
          <div v-else class="space-y-4">
            <div
              v-for="booking in bookings"
              :key="booking.id"
              class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <h3 class="text-lg font-medium text-gray-900">
                    {{ getPackageName(booking.package_id) }}
                  </h3>
                  <p class="text-sm text-gray-600">
                    {{ formatDate(booking.class_date) }}
                  </p>
                  <div class="mt-2">
                    <span
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getStatusClasses(booking.status)
                      ]"
                    >
                      {{ booking.status }}
                    </span>
                  </div>
                </div>
                
                <div class="ml-4">
                  <div v-if="booking.status === 'paid'" class="space-y-2">
                    <a
                      v-if="booking.whatsapp_link"
                      :href="booking.whatsapp_link"
                      target="_blank"
                      class="btn-secondary text-sm"
                    >
                      <ChatBubbleLeftRightIcon class="h-4 w-4 mr-1" />
                      WhatsApp
                    </a>
                    <a
                      v-if="booking.discord_link"
                      :href="booking.discord_link"
                      target="_blank"
                      class="btn-secondary text-sm"
                    >
                      <UserGroupIcon class="h-4 w-4 mr-1" />
                      Discord
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card">
          <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
          </div>
          <div class="card-body space-y-3">
            <router-link to="/packages" class="btn-primary w-full justify-center">
              Book New Class
            </router-link>
            <button class="btn-secondary w-full justify-center">
              Download Certificate
            </button>
            <button class="btn-secondary w-full justify-center">
              Contact Support
            </button>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Account Settings</h3>
          </div>
          <div class="card-body space-y-3">
            <button class="btn-secondary w-full justify-center">
              Update Profile
            </button>
            <button class="btn-secondary w-full justify-center">
              Change Password
            </button>
            <button class="btn-secondary w-full justify-center">
              Enable 2FA
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  AcademicCapIcon, 
  CheckCircleIcon, 
  ClockIcon, 
  CalendarIcon,
  ChatBubbleLeftRightIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/store/auth'
import { useToast } from 'vue-toastification'
import api from '@/services/api'
import { format } from 'date-fns'

const authStore = useAuthStore()
const toast = useToast()

const bookings = ref([])
const packages = ref([])
const loading = ref(true)

const stats = computed(() => {
  const total = bookings.value.length
  const completed = bookings.value.filter(b => b.status === 'completed').length
  const upcoming = bookings.value.filter(b => 
    b.status === 'paid' && new Date(b.class_date) > new Date()
  ).length
  
  return {
    totalBookings: total,
    completedClasses: completed,
    upcomingClasses: upcoming
  }
})

const getPackageName = (packageId) => {
  const pkg = packages.value.find(p => p.id === packageId)
  return pkg ? pkg.name : 'Unknown Package'
}

const formatDate = (dateString) => {
  try {
    return format(new Date(dateString), 'EEEE, MMMM d, yyyy')
  } catch {
    return dateString
  }
}

const getStatusClasses = (status) => {
  switch (status) {
    case 'paid':
      return 'bg-green-100 text-green-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'cancelled':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const fetchBookings = async () => {
  try {
    // In a real app, you'd have a dedicated endpoint for user bookings
    // For now, we'll simulate this
    const response = await api.get('/bookings')
    bookings.value = response.data.bookings || []
  } catch (error) {
    console.error('Error fetching bookings:', error)
    toast.error('Failed to load bookings')
  }
}

const fetchPackages = async () => {
  try {
    const response = await api.get('/packages')
    packages.value = response.data.packages || []
  } catch (error) {
    console.error('Error fetching packages:', error)
  }
}

onMounted(async () => {
  await Promise.all([fetchBookings(), fetchPackages()])
  loading.value = false
})
</script>