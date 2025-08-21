<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary-100">
          <UserPlusIcon class="h-6 w-6 text-primary-600" />
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Or
          <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500">
            sign in to your existing account
          </router-link>
        </p>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="space-y-4">
          <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700">
              Full Name
            </label>
            <input
              id="full_name"
              v-model="form.full_name"
              name="full_name"
              type="text"
              required
              class="input mt-1"
              :class="{ 'border-red-500': errors.full_name }"
              placeholder="Enter your full name"
            />
            <p v-if="errors.full_name" class="mt-1 text-sm text-red-600">{{ errors.full_name }}</p>
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email address
            </label>
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              required
              class="input mt-1"
              :class="{ 'border-red-500': errors.email }"
              placeholder="Enter your email"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
          </div>
          
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="input mt-1"
              :class="{ 'border-red-500': errors.password }"
              placeholder="Create a strong password"
            />
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
            
            <!-- Password Policy -->
            <div class="mt-2 p-3 bg-gray-50 rounded-md">
              <p class="text-sm font-medium text-gray-700 mb-2">Password Requirements:</p>
              <ul class="text-xs text-gray-600 space-y-1">
                <li :class="{ 'text-green-600': passwordChecks.length }">
                  ✓ At least 8 characters
                </li>
                <li :class="{ 'text-green-600': passwordChecks.uppercase }">
                  ✓ One uppercase letter
                </li>
                <li :class="{ 'text-green-600': passwordChecks.lowercase }">
                  ✓ One lowercase letter
                </li>
                <li :class="{ 'text-green-600': passwordChecks.number }">
                  ✓ One number
                </li>
                <li :class="{ 'text-green-600': passwordChecks.special }">
                  ✓ One special character
                </li>
              </ul>
            </div>
          </div>
          
          <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-700">
              Confirm Password
            </label>
            <input
              id="confirm_password"
              v-model="form.confirm_password"
              name="confirm_password"
              type="password"
              required
              class="input mt-1"
              :class="{ 'border-red-500': errors.confirm_password }"
              placeholder="Confirm your password"
            />
            <p v-if="errors.confirm_password" class="mt-1 text-sm text-red-600">{{ errors.confirm_password }}</p>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="authStore.isLoading || !isPasswordValid"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="authStore.isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ authStore.isLoading ? 'Creating account...' : 'Create account' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { UserPlusIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/store/auth'
import { useToast } from 'vue-toastification'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const form = reactive({
  full_name: '',
  email: '',
  password: '',
  confirm_password: '',
  recaptcha_token: ''
})

const errors = reactive({
  full_name: '',
  email: '',
  password: '',
  confirm_password: ''
})

const passwordChecks = computed(() => {
  const password = form.password
  return {
    length: password.length >= 8,
    uppercase: /[A-Z]/.test(password),
    lowercase: /[a-z]/.test(password),
    number: /[0-9]/.test(password),
    special: /[^A-Za-z0-9]/.test(password)
  }
})

const isPasswordValid = computed(() => {
  const checks = passwordChecks.value
  return checks.length && checks.uppercase && checks.lowercase && checks.number && checks.special
})

const validateForm = () => {
  errors.full_name = ''
  errors.email = ''
  errors.password = ''
  errors.confirm_password = ''
  
  if (!form.full_name.trim()) {
    errors.full_name = 'Full name is required'
  }
  
  if (!form.email) {
    errors.email = 'Email is required'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
  }
  
  if (!form.password) {
    errors.password = 'Password is required'
  } else if (!isPasswordValid.value) {
    errors.password = 'Password does not meet requirements'
  }
  
  if (!form.confirm_password) {
    errors.confirm_password = 'Please confirm your password'
  } else if (form.password !== form.confirm_password) {
    errors.confirm_password = 'Passwords do not match'
  }
  
  return !Object.values(errors).some(error => error)
}

const executeRecaptcha = () => {
  return new Promise((resolve) => {
    if (typeof grecaptcha !== 'undefined') {
      grecaptcha.ready(() => {
        grecaptcha.execute('YOUR_RECAPTCHA_SITE_KEY', { action: 'register' })
          .then(token => resolve(token))
          .catch(() => resolve(''))
      })
    } else {
      resolve('')
    }
  })
}

const handleRegister = async () => {
  if (!validateForm()) {
    return
  }
  
  try {
    // Execute reCAPTCHA
    form.recaptcha_token = await executeRecaptcha()
    
    const success = await authStore.register(form)
    if (success) {
      router.push('/dashboard')
    }
  } catch (error) {
    console.error('Registration error:', error)
  }
}
</script>