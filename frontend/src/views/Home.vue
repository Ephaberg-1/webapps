<template>
  <div class="min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 text-white">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
          <h1 class="text-4xl md:text-6xl font-bold mb-6 text-shadow">
            Master Cybersecurity
            <span class="text-primary-200">From Experts</span>
          </h1>
          <p class="text-xl md:text-2xl mb-8 text-primary-100 max-w-3xl mx-auto">
            Learn from industry professionals and gain hands-on experience in ethical hacking, 
            network security, and threat detection.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link 
              to="/packages" 
              class="btn bg-white text-primary-700 hover:bg-gray-100 px-8 py-3 text-lg font-semibold"
            >
              View Classes
            </router-link>
            <router-link 
              to="/register" 
              class="btn border-2 border-white text-white hover:bg-white hover:text-primary-700 px-8 py-3 text-lg font-semibold"
            >
              Get Started
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Why Choose Our Classes?
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Comprehensive training designed by cybersecurity professionals for real-world applications.
          </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <ShieldCheckIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Expert Instructors</h3>
            <p class="text-gray-600">
              Learn from certified cybersecurity professionals with years of industry experience.
            </p>
          </div>
          
          <div class="text-center">
            <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <ComputerDesktopIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Hands-on Labs</h3>
            <p class="text-gray-600">
              Practice on real-world scenarios with our virtual lab environment.
            </p>
          </div>
          
          <div class="text-center">
            <div class="bg-primary-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <UserGroupIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Community Support</h3>
            <p class="text-gray-600">
              Join our community of learners and professionals for ongoing support.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
          Ready to Start Your Cybersecurity Journey?
        </h2>
        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
          Choose from our range of classes designed for beginners to advanced professionals.
        </p>
        <router-link 
          to="/packages" 
          class="btn-primary px-8 py-3 text-lg font-semibold"
        >
          Browse Classes
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { 
  ShieldCheckIcon, 
  ComputerDesktopIcon, 
  UserGroupIcon 
} from '@heroicons/vue/24/outline'
</script>