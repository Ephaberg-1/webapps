<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Book Your Cybersecurity Class
        </h1>
        <p class="text-gray-600">
          Select your preferred package and class date to get started.
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Booking Form -->
        <div class="card">
          <div class="card-header">
            <h2 class="text-xl font-semibold text-gray-900">Booking Details</h2>
          </div>
          
          <div class="card-body">
            <form @submit.prevent="handleBooking" class="space-y-6">
              <!-- Package Selection -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Select Package
                </label>
                <select 
                  v-model="form.package_id"
                  class="input"
                  required
                >
                  <option value="">Choose a package...</option>
                  <option 
                    v-for="pkg in packages" 
                    :key="pkg.id" 
                    :value="pkg.id"
                  >
                    {{ pkg.name }} - ₦{{ (pkg.price_cents / 100).toLocaleString() }}
                  </option>
                </select>
              </div>

              <!-- Class Date -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Class Date
                </label>
                <input 
                  v-model="form.class_date"
                  type="date"
                  class="input"
                  :min="minDate"
                  required
                />
                <p class="mt-1 text-sm text-gray-500">
                  Classes are held on weekdays from 9 AM to 5 PM
                </p>
              </div>

              <!-- Email for Payment -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Email for Payment
                </label>
                <input 
                  v-model="form.email"
                  type="email"
                  class="input"
                  required
                  placeholder="Enter email for payment confirmation"
                />
              </div>

              <!-- Submit Button -->
              <button
                type="submit"
                :disabled="loading || !isFormValid"
                class="btn-primary w-full justify-center"
              >
                <span v-if="loading" class="mr-2">
                  <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </span>
                {{ loading ? 'Processing...' : 'Proceed to Payment' }}
              </button>
            </form>
          </div>
        </div>

        <!-- Package Details -->
        <div class="space-y-6">
          <div v-if="selectedPackage" class="card">
            <div class="card-header">
              <h3 class="text-lg font-semibold text-gray-900">Selected Package</h3>
            </div>
            <div class="card-body">
              <h4 class="text-xl font-bold text-primary-600 mb-2">
                {{ selectedPackage.name }}
              </h4>
              <p class="text-gray-600 mb-4">{{ selectedPackage.description }}</p>
              <div class="text-2xl font-bold text-gray-900">
                ₦{{ (selectedPackage.price_cents / 100).toLocaleString() }}
              </div>
            </div>
          </div>

          <!-- What's Included -->
          <div class="card">
            <div class="card-header">
              <h3 class="text-lg font-semibold text-gray-900">What's Included</h3>
            </div>
            <div class="card-body">
              <ul class="space-y-3">
                <li class="flex items-start">
                  <CheckIcon class="h-5 w-5 text-green-500 mr-3 mt-0.5" />
                  <span class="text-sm text-gray-700">Full-day intensive training session</span>
                </li>
                <li class="flex items-start">
                  <CheckIcon class="h-5 w-5 text-green-500 mr-3 mt-0.5" />
                  <span class="text-sm text-gray-700">Hands-on lab exercises and real-world scenarios</span>
                </li>
                <li class="flex items-start">
                  <CheckIcon class="h-5 w-5 text-green-500 mr-3 mt-0.5" />
                  <span class="text-sm text-gray-700">Certificate of completion</span>
                </li>
                <li class="flex items-start">
                  <CheckIcon class="h-5 w-5 text-green-500 mr-3 mt-0.5" />
                  <span class="text-sm text-gray-700">Access to WhatsApp and Discord communities</span>
                </li>
                <li class="flex items-start">
                  <CheckIcon class="h-5 w-5 text-green-500 mr-3 mt-0.5" />
                  <span class="text-sm text-gray-700">Post-class support and resources</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Payment Info -->
          <div class="card">
            <div class="card-header">
              <h3 class="text-lg font-semibold text-gray-900">Payment Information</h3>
            </div>
            <div class="card-body">
              <div class="flex items-center mb-3">
                <ShieldCheckIcon class="h-5 w-5 text-green-500 mr-2" />
                <span class="text-sm text-gray-700">Secure payment via Paystack</span>
              </div>
              <div class="flex items-center mb-3">
                <CreditCardIcon class="h-5 w-5 text-blue-500 mr-2" />
                <span class="text-sm text-gray-700">All major cards accepted</span>
              </div>
              <div class="flex items-center">
                <LockClosedIcon class="h-5 w-5 text-gray-500 mr-2" />
                <span class="text-sm text-gray-700">SSL encrypted transactions</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { 
  CheckIcon, 
  ShieldCheckIcon, 
  CreditCardIcon, 
  LockClosedIcon 
} from '@heroicons/vue/24/outline'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/store/auth'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const packages = ref([])
const loading = ref(false)

const form = ref({
  package_id: route.params.packageId || '',
  class_date: '',
  email: authStore.user?.email || ''
})

const minDate = computed(() => {
  const today = new Date()
  const tomorrow = new Date(today)
  tomorrow.setDate(tomorrow.getDate() + 1)
  return tomorrow.toISOString().split('T')[0]
})

const selectedPackage = computed(() => {
  return packages.value.find(pkg => pkg.id == form.value.package_id)
})

const isFormValid = computed(() => {
  return form.value.package_id && form.value.class_date && form.value.email
})

const fetchPackages = async () => {
  try {
    const response = await api.get('/packages')
    packages.value = response.data.packages || []
  } catch (error) {
    console.error('Error fetching packages:', error)
    toast.error('Failed to load packages')
  }
}

const handleBooking = async () => {
  if (!isFormValid.value) {
    toast.error('Please fill in all required fields')
    return
  }

  loading.value = true
  try {
    // Create booking
    const bookingResponse = await api.post('/bookings', {
      package_id: form.value.package_id,
      class_date: form.value.class_date
    })

    // Initialize payment
    const paymentResponse = await api.post('/payments/init', {
      booking_id: bookingResponse.data.booking_id,
      email: form.value.email
    })

    // Redirect to Paystack
    if (paymentResponse.data.paystack?.data?.authorization_url) {
      window.location.href = paymentResponse.data.paystack.data.authorization_url
    } else {
      toast.error('Payment initialization failed')
    }
  } catch (error) {
    console.error('Booking error:', error)
    const message = error.response?.data?.error || 'Booking failed. Please try again.'
    toast.error(message)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPackages()
})
</script>