import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/Home.vue'),
    meta: { title: 'Home - CyberSec Classes' }
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    meta: { title: 'Login - CyberSec Classes', guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/Register.vue'),
    meta: { title: 'Register - CyberSec Classes', guest: true }
  },
  {
    path: '/packages',
    name: 'Packages',
    component: () => import('@/views/Packages.vue'),
    meta: { title: 'Packages - CyberSec Classes' }
  },
  {
    path: '/booking/:packageId?',
    name: 'Booking',
    component: () => import('@/views/Booking.vue'),
    meta: { title: 'Book Class - CyberSec Classes', requiresAuth: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { title: 'Dashboard - CyberSec Classes', requiresAuth: true }
  },
  {
    path: '/admin',
    name: 'Admin',
    component: () => import('@/views/Admin.vue'),
    meta: { title: 'Admin - CyberSec Classes', requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue'),
    meta: { title: '404 - Page Not Found' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  // Set page title
  document.title = to.meta.title || 'CyberSec Classes'
  
  const authStore = useAuthStore()
  
  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // Try to refresh token
    const authenticated = await authStore.checkAuth()
    if (!authenticated) {
      next('/login')
      return
    }
  }
  
  // Check if route requires admin access
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/dashboard')
    return
  }
  
  // Redirect authenticated users away from guest routes
  if (to.meta.guest && authStore.isAuthenticated) {
    next('/dashboard')
    return
  }
  
  next()
})

export default router