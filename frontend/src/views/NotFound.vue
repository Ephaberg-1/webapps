<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
      <div class="mb-8">
        <ExclamationTriangleIcon class="mx-auto h-24 w-24 text-gray-400" />
      </div>
      
      <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
      <h2 class="text-2xl font-semibold text-gray-900 mb-4">Page Not Found</h2>
      <p class="text-gray-600 mb-8">
        Sorry, we couldn't find the page you're looking for. 
        It might have been moved, deleted, or you entered the wrong URL.
      </p>
      
      <div class="space-y-4">
        <router-link to="/" class="btn-primary w-full justify-center">
          Go Back Home
        </router-link>
        <router-link to="/packages" class="btn-secondary w-full justify-center">
          Browse Classes
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
</script>