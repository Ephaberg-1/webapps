<template>
  <div class="fixed bottom-4 right-4 z-50">
    <!-- Chat Toggle Button -->
    <button
      v-if="!isOpen"
      @click="toggleChat"
      class="bg-primary-600 text-white p-3 rounded-full shadow-lg hover:bg-primary-700 transition-colors"
    >
      <ChatBubbleLeftRightIcon v-if="!isLoading" class="h-6 w-6" />
      <svg v-else class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </button>

    <!-- Chat Window -->
    <div
      v-if="isOpen"
      class="bg-white rounded-lg shadow-xl border border-gray-200 w-80 h-96 flex flex-col"
    >
      <!-- Header -->
      <div class="bg-primary-600 text-white p-4 rounded-t-lg flex items-center justify-between">
        <div class="flex items-center">
          <SparklesIcon class="h-5 w-5 mr-2" />
          <h3 class="font-semibold">AI Assistant</h3>
        </div>
        <button @click="toggleChat" class="text-white hover:text-gray-200">
          <XMarkIcon class="h-5 w-5" />
        </button>
      </div>

      <!-- Messages -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4" ref="messagesContainer">
        <div
          v-for="(message, index) in messages"
          :key="index"
          :class="[
            'flex',
            message.type === 'user' ? 'justify-end' : 'justify-start'
          ]"
        >
          <div
            :class="[
              'max-w-xs px-3 py-2 rounded-lg text-sm',
              message.type === 'user'
                ? 'bg-primary-600 text-white'
                : 'bg-gray-100 text-gray-900'
            ]"
          >
            {{ message.content }}
          </div>
        </div>
        
        <!-- Loading indicator -->
        <div v-if="isLoading" class="flex justify-start">
          <div class="bg-gray-100 text-gray-900 max-w-xs px-3 py-2 rounded-lg text-sm">
            <div class="flex items-center space-x-1">
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Input -->
      <div class="p-4 border-t border-gray-200">
        <form @submit.prevent="sendMessage" class="flex space-x-2">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Ask me anything..."
            class="flex-1 input text-sm"
            :disabled="isLoading"
          />
          <button
            type="submit"
            :disabled="!newMessage.trim() || isLoading"
            class="btn-primary px-3 py-2 text-sm"
          >
            <PaperAirplaneIcon class="h-4 w-4" />
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted } from 'vue'
import { 
  ChatBubbleLeftRightIcon, 
  XMarkIcon, 
  SparklesIcon,
  PaperAirplaneIcon 
} from '@heroicons/vue/24/outline'
import { useToast } from 'vue-toastification'
import api from '@/services/api'

const isOpen = ref(false)
const isLoading = ref(false)
const messages = ref([])
const newMessage = ref('')
const messagesContainer = ref(null)
const toast = useToast()

const toggleChat = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value && messages.value.length === 0) {
    // Add welcome message
    messages.value.push({
      type: 'assistant',
      content: 'Hello! I\'m your AI assistant. I can help you with questions about our cybersecurity classes, booking process, or any other platform-related inquiries. How can I help you today?'
    })
  }
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const sendMessage = async () => {
  const message = newMessage.value.trim()
  if (!message || isLoading.value) return

  // Add user message
  messages.value.push({
    type: 'user',
    content: message
  })
  newMessage.value = ''
  await scrollToBottom()

  // Send to AI
  isLoading.value = true
  try {
    const response = await api.post('/ai', {
      prompt: message
    })
    
    messages.value.push({
      type: 'assistant',
      content: response.data.answer || 'I apologize, but I\'m unable to process your request at the moment. Please try again later.'
    })
  } catch (error) {
    console.error('AI chat error:', error)
    messages.value.push({
      type: 'assistant',
      content: 'Sorry, I\'m having trouble connecting right now. Please try again in a moment.'
    })
    toast.error('Failed to get AI response')
  } finally {
    isLoading.value = false
    await scrollToBottom()
  }
}

onMounted(() => {
  // Auto-scroll when messages change
  scrollToBottom()
})
</script>