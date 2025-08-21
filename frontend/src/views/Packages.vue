<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
          Cybersecurity Training Packages
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Choose the perfect training package for your cybersecurity journey. 
          All classes include hands-on labs and expert instruction.
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <!-- Packages Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="pkg in packages" 
          :key="pkg.id"
          class="card hover:shadow-lg transition-shadow duration-300"
        >
          <div class="card-header">
            <h3 class="text-xl font-semibold text-gray-900">{{ pkg.name }}</h3>
            <p class="text-3xl font-bold text-primary-600 mt-2">
              ₦{{ (pkg.price_cents / 100).toLocaleString() }}
            </p>
          </div>
          
          <div class="card-body">
            <p class="text-gray-600 mb-6">{{ pkg.description }}</p>
            
            <div class="space-y-3 mb-6">
              <div class="flex items-center">
                <CheckIcon class="h-5 w-5 text-green-500 mr-3" />
                <span class="text-sm text-gray-700">Expert-led instruction</span>
              </div>
              <div class="flex items-center">
                <CheckIcon class="h-5 w-5 text-green-500 mr-3" />
                <span class="text-sm text-gray-700">Hands-on lab exercises</span>
              </div>
              <div class="flex items-center">
                <CheckIcon class="h-5 w-5 text-green-500 mr-3" />
                <span class="text-sm text-gray-700">Certificate of completion</span>
              </div>
              <div class="flex items-center">
                <CheckIcon class="h-5 w-5 text-green-500 mr-3" />
                <span class="text-sm text-gray-700">Community access</span>
              </div>
            </div>
            
            <router-link 
              :to="`/booking/${pkg.id}`"
              class="btn-primary w-full justify-center"
            >
              Book This Class
            </router-link>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && packages.length === 0" class="text-center py-12">
        <div class="mx-auto h-12 w-12 text-gray-400">
          <AcademicCapIcon class="h-12 w-12" />
        </div>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No packages available</h3>
        <p class="mt-1 text-sm text-gray-500">
          Check back later for new training packages.
        </p>
      </div>

      <!-- CTA Section -->
      <div class="mt-16 text-center">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">
            Need a Custom Training Program?
          </h2>
          <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
            We offer customized training solutions for organizations and teams. 
            Contact us to discuss your specific cybersecurity training needs.
          </p>
          <button class="btn-secondary">
            Contact Us
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { CheckIcon, AcademicCapIcon } from '@heroicons/vue/24/outline'
import { useToast } from 'vue-toastification'
import api from '@/services/api'

const packages = ref([])
const loading = ref(true)
const toast = useToast()

const fetchPackages = async () => {
  try {
    const response = await api.get('/packages')
    packages.value = response.data.packages || []
  } catch (error) {
    console.error('Error fetching packages:', error)
    toast.error('Failed to load packages. Please try again.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPackages()
})
</script>