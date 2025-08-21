<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600">Manage packages, bookings, and communications</p>
      </div>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card">
          <div class="card-body">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <AcademicCapIcon class="h-8 w-8 text-primary-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Packages</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.totalPackages }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <CalendarIcon class="h-8 w-8 text-green-600" />
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
                <UserGroupIcon class="h-8 w-8 text-blue-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Users</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.totalUsers }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <CurrencyDollarIcon class="h-8 w-8 text-yellow-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Revenue</p>
                <p class="text-2xl font-semibold text-gray-900">₦{{ stats.revenue.toLocaleString() }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="mb-8">
        <nav class="flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'py-2 px-1 border-b-2 font-medium text-sm',
              activeTab === tab.id
                ? 'border-primary-500 text-primary-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.name }}
          </button>
        </nav>
      </div>

      <!-- Package Management -->
      <div v-if="activeTab === 'packages'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-900">Package Management</h2>
          <button @click="showPackageModal = true" class="btn-primary">
            Add Package
          </button>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Price
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="pkg in packages" :key="pkg.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ pkg.name }}</div>
                      <div class="text-sm text-gray-500">{{ pkg.description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      ₦{{ (pkg.price_cents / 100).toLocaleString() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="[
                          'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                          pkg.is_active
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800'
                        ]"
                      >
                        {{ pkg.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button @click="editPackage(pkg)" class="text-primary-600 hover:text-primary-900 mr-3">
                        Edit
                      </button>
                      <button @click="deletePackage(pkg.id)" class="text-red-600 hover:text-red-900">
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Booking Management -->
      <div v-if="activeTab === 'bookings'" class="space-y-6">
        <h2 class="text-xl font-semibold text-gray-900">Booking Management</h2>
        
        <div class="card">
          <div class="card-body">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Package
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="booking in bookings" :key="booking.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ booking.user_name }}</div>
                      <div class="text-sm text-gray-500">{{ booking.user_email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ getPackageName(booking.package_id) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatDate(booking.class_date) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="[
                          'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                          getStatusClasses(booking.status)
                        ]"
                      >
                        {{ booking.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button @click="updateBookingStatus(booking.id, 'completed')" class="text-green-600 hover:text-green-900 mr-3">
                        Complete
                      </button>
                      <button @click="updateBookingStatus(booking.id, 'cancelled')" class="text-red-600 hover:text-red-900">
                        Cancel
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Broadcast Messages -->
      <div v-if="activeTab === 'broadcast'" class="space-y-6">
        <h2 class="text-xl font-semibold text-gray-900">Send Broadcast Message</h2>
        
        <div class="card">
          <div class="card-body">
            <form @submit.prevent="sendBroadcast" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Subject
                </label>
                <input
                  v-model="broadcastForm.subject"
                  type="text"
                  class="input"
                  required
                  placeholder="Enter message subject"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Message
                </label>
                <textarea
                  v-model="broadcastForm.html"
                  rows="6"
                  class="input"
                  required
                  placeholder="Enter your message (HTML supported)"
                ></textarea>
              </div>
              
              <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">
                  This message will be sent to all registered users.
                </p>
                <button
                  type="submit"
                  :disabled="broadcastLoading"
                  class="btn-primary"
                >
                  {{ broadcastLoading ? 'Sending...' : 'Send Broadcast' }}
                </button>
              </div>
            </form>
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
  CalendarIcon, 
  UserGroupIcon, 
  CurrencyDollarIcon 
} from '@heroicons/vue/24/outline'
import { useToast } from 'vue-toastification'
import api from '@/services/api'
import { format } from 'date-fns'

const toast = useToast()

const activeTab = ref('packages')
const packages = ref([])
const bookings = ref([])
const showPackageModal = ref(false)
const broadcastLoading = ref(false)

const tabs = [
  { id: 'packages', name: 'Packages' },
  { id: 'bookings', name: 'Bookings' },
  { id: 'broadcast', name: 'Broadcast' }
]

const broadcastForm = ref({
  subject: '',
  html: ''
})

const stats = computed(() => {
  return {
    totalPackages: packages.value.length,
    totalBookings: bookings.value.length,
    totalUsers: 150, // Mock data
    revenue: 2500000 // Mock data in kobo
  }
})

const getPackageName = (packageId) => {
  const pkg = packages.value.find(p => p.id === packageId)
  return pkg ? pkg.name : 'Unknown Package'
}

const formatDate = (dateString) => {
  try {
    return format(new Date(dateString), 'MMM d, yyyy')
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
    case 'completed':
      return 'bg-blue-100 text-blue-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const fetchPackages = async () => {
  try {
    const response = await api.get('/packages')
    packages.value = response.data.packages || []
  } catch (error) {
    console.error('Error fetching packages:', error)
    toast.error('Failed to load packages')
  }
}

const fetchBookings = async () => {
  try {
    // Mock data for now
    bookings.value = [
      {
        id: 1,
        user_name: 'John Doe',
        user_email: 'john@example.com',
        package_id: 1,
        class_date: '2024-01-15',
        status: 'paid'
      }
    ]
  } catch (error) {
    console.error('Error fetching bookings:', error)
    toast.error('Failed to load bookings')
  }
}

const editPackage = (pkg) => {
  // Implement package editing
  console.log('Edit package:', pkg)
}

const deletePackage = async (id) => {
  if (!confirm('Are you sure you want to delete this package?')) return
  
  try {
    await api.post('/admin/packages/delete', { id })
    await fetchPackages()
    toast.success('Package deleted successfully')
  } catch (error) {
    console.error('Error deleting package:', error)
    toast.error('Failed to delete package')
  }
}

const updateBookingStatus = async (id, status) => {
  try {
    // Implement booking status update
    console.log('Update booking status:', id, status)
    toast.success('Booking status updated')
  } catch (error) {
    console.error('Error updating booking status:', error)
    toast.error('Failed to update booking status')
  }
}

const sendBroadcast = async () => {
  broadcastLoading.value = true
  try {
    await api.post('/admin/broadcast', broadcastForm.value)
    broadcastForm.value = { subject: '', html: '' }
    toast.success('Broadcast message sent successfully')
  } catch (error) {
    console.error('Error sending broadcast:', error)
    toast.error('Failed to send broadcast message')
  } finally {
    broadcastLoading.value = false
  }
}

onMounted(() => {
  fetchPackages()
  fetchBookings()
})
</script>