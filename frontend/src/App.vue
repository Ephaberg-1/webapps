<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/" class="flex-shrink-0 flex items-center">
              <ShieldCheckIcon class="h-8 w-8 text-primary-600" />
              <span class="ml-2 text-xl font-bold text-gray-900">CyberSec Classes</span>
            </router-link>
          </div>
          
          <div class="flex items-center space-x-4">
            <router-link 
              to="/packages" 
              class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            >
              Packages
            </router-link>
            
            <template v-if="authStore.isAuthenticated">
              <router-link 
                to="/dashboard" 
                class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              >
                Dashboard
              </router-link>
              <router-link 
                v-if="authStore.user?.role === 'admin'"
                to="/admin" 
                class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              >
                Admin
              </router-link>
              <button 
                @click="logout" 
                class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              >
                Logout
              </button>
            </template>
            <template v-else>
              <router-link 
                to="/login" 
                class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              >
                Login
              </router-link>
              <router-link 
                to="/register" 
                class="btn-primary"
              >
                Sign Up
              </router-link>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 class="text-lg font-semibold mb-4">CyberSec Classes</h3>
            <p class="text-gray-300">
              Professional cybersecurity training from industry experts. 
              Learn the skills you need to protect digital assets.
            </p>
          </div>
          <div>
            <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
            <ul class="space-y-2">
              <li><router-link to="/packages" class="text-gray-300 hover:text-white transition-colors">Packages</router-link></li>
              <li><router-link to="/dashboard" class="text-gray-300 hover:text-white transition-colors">Dashboard</router-link></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Support</a></li>
            </ul>
          </div>
          <div>
            <h3 class="text-lg font-semibold mb-4">Contact</h3>
            <p class="text-gray-300">
              Email: info@cybersecclasses.com<br>
              Phone: +1 (555) 123-4567
            </p>
          </div>
        </div>
        <div class="mt-8 pt-8 border-t border-gray-700 text-center text-gray-300">
          <p>&copy; 2024 CyberSec Classes. All rights reserved.</p>
        </div>
      </div>
    </footer>

    <!-- AI Chat Widget -->
    <AIChat v-if="authStore.isAuthenticated" />
  </div>
</template>

<script setup>
import { ShieldCheckIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/store/auth'
import AIChat from '@/components/AIChat.vue'

const authStore = useAuthStore()

const logout = async () => {
  await authStore.logout()
}
</script>