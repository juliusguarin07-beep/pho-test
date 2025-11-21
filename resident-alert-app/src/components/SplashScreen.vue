<template>
  <div class="fixed inset-0 z-50 bg-white">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Subtle gradient orbs -->
      <div class="absolute w-96 h-96 bg-blue-100/30 rounded-full blur-3xl -top-48 -left-48 animate-pulse"></div>
      <div class="absolute w-96 h-96 bg-blue-50/30 rounded-full blur-3xl -bottom-48 -right-48 animate-pulse" style="animation-delay: 1.5s;"></div>
    </div>

    <!-- Welcome Screen -->
    <div v-if="currentStep === 'welcome'" class="relative flex flex-col items-center justify-center h-full px-6 text-gray-900">
      <!-- Logo Section -->
      <div class="mb-12 animate-bounce-slow">
        <div class="relative">
          <!-- Glow effect -->
          <div class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-blue-500/20 rounded-full blur-2xl"></div>
          <!-- Icon Container -->
          <div class="relative w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl flex items-center justify-center shadow-lg backdrop-blur-sm border border-blue-400/30">
            <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Header Text -->
      <h1 class="text-5xl font-bold text-center mb-3 animate-fade-in tracking-tight text-gray-900">
        PHO Alert
      </h1>
      <p class="text-xl text-gray-600 text-center mb-2 animate-fade-in font-light" style="animation-delay: 0.15s;">
        Disease Surveillance System
      </p>
      <p class="text-sm text-gray-500 text-center mb-16 animate-fade-in" style="animation-delay: 0.3s;">
        Provincial Health Office - Pangasinan
      </p>

      <!-- Features Grid -->
      <div class="w-full max-w-sm space-y-3 mb-16">
        <!-- Real-Time Alerts -->
        <div class="flex items-start gap-4 p-4 bg-gray-50/80 backdrop-blur-md rounded-2xl border border-gray-200 hover:border-blue-300 transition-all animate-slide-up" style="animation-delay: 0.45s;">
          <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mt-0.5">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-gray-900 text-sm mb-0.5">Real-Time Alerts</h3>
            <p class="text-xs text-gray-600 leading-relaxed">Instant notifications about disease outbreaks</p>
          </div>
        </div>

        <!-- Interactive Map -->
        <div class="flex items-start gap-4 p-4 bg-gray-50/80 backdrop-blur-md rounded-2xl border border-gray-200 hover:border-blue-300 transition-all animate-slide-up" style="animation-delay: 0.6s;">
          <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mt-0.5">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-gray-900 text-sm mb-0.5">Interactive Map</h3>
            <p class="text-xs text-gray-600 leading-relaxed">View outbreak locations with color-coded alerts</p>
          </div>
        </div>

        <!-- Prevention Tips -->
        <div class="flex items-start gap-4 p-4 bg-gray-50/80 backdrop-blur-md rounded-2xl border border-gray-200 hover:border-blue-300 transition-all animate-slide-up" style="animation-delay: 0.75s;">
          <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mt-0.5">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-gray-900 text-sm mb-0.5">Prevention Tips</h3>
            <p class="text-xs text-gray-600 leading-relaxed">Preventive measures and emergency contacts</p>
          </div>
        </div>
      </div>

      <!-- Get Started Button -->
      <button
        @click="nextStep"
        class="w-full max-w-sm px-8 py-3.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full font-semibold text-sm shadow-md hover:shadow-lg hover:shadow-blue-500/20 transform hover:scale-105 transition-all duration-300 animate-fade-in"
        style="animation-delay: 0.9s;"
      >
        Get Started
      </button>

      <!-- Skip Button -->
      <button
        @click="handleComplete"
        class="mt-3 text-xs text-gray-500 hover:text-gray-700 transition-colors animate-fade-in font-medium"
        style="animation-delay: 1.05s;"
      >
        Skip for now
      </button>

      <!-- Footer -->
      <div class="absolute bottom-6 left-0 right-0 flex flex-col items-center gap-2 text-xs text-gray-400">
        <div class="flex items-center gap-2">
          <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
          <span>v1.0.0</span>
          <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
        </div>
      </div>
    </div>

    <!-- Permissions Screen -->
    <div v-if="currentStep === 'permissions'" class="relative flex flex-col items-center justify-between h-full px-6 py-12 text-gray-900">
      <!-- Header -->
      <div class="flex-1 flex flex-col items-center justify-center">
        <div class="mb-8">
          <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl flex items-center justify-center shadow-md backdrop-blur-sm border border-blue-400/30">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
        </div>
        <h2 class="text-3xl font-bold text-center mb-3">Enable Notifications</h2>
        <p class="text-sm text-gray-600 text-center mb-8">Get real-time alerts about disease outbreaks in your area</p>

        <div class="w-full max-w-sm space-y-3">
          <div class="flex items-start gap-3 p-4 bg-gray-50 backdrop-blur-md rounded-xl border border-gray-200">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">Instant Updates</p>
              <p class="text-xs text-gray-600 mt-0.5">Never miss critical alerts</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="w-full max-w-sm space-y-3">
        <button
          @click="nextStep"
          class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full font-semibold text-sm shadow-md hover:shadow-lg transform hover:scale-105 transition-all"
        >
          Enable Notifications
        </button>
        <button
          @click="nextStep"
          class="w-full px-6 py-3 bg-gray-100 border border-gray-300 text-gray-900 rounded-full font-semibold text-sm hover:bg-gray-200 transition-all"
        >
          Maybe Later
        </button>
      </div>
    </div>

    <!-- Location Screen -->
    <div v-if="currentStep === 'location'" class="relative flex flex-col items-center justify-between h-full px-6 py-12 text-gray-900">
      <!-- Header -->
      <div class="flex-1 flex flex-col items-center justify-center">
        <div class="mb-8">
          <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl flex items-center justify-center shadow-md backdrop-blur-sm border border-blue-400/30">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
        </div>
        <h2 class="text-3xl font-bold text-center mb-3">Your Location</h2>
        <p class="text-sm text-gray-600 text-center mb-8">We'll use this to show relevant alerts from your area</p>

        <div class="w-full max-w-sm space-y-3">
          <div class="flex items-start gap-3 p-4 bg-gray-50 backdrop-blur-md rounded-xl border border-gray-200">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">Localized Content</p>
              <p class="text-xs text-gray-600 mt-0.5">See alerts specific to Pangasinan</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="w-full max-w-sm space-y-3">
        <button
          @click="nextStep"
          class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full font-semibold text-sm shadow-md hover:shadow-lg transform hover:scale-105 transition-all"
        >
          Use My Location
        </button>
        <button
          @click="nextStep"
          class="w-full px-6 py-3 bg-gray-100 border border-gray-300 text-gray-900 rounded-full font-semibold text-sm hover:bg-gray-200 transition-all"
        >
          Skip
        </button>
      </div>
    </div>

    <!-- Completion Screen -->
    <div v-if="currentStep === 'complete'" class="relative flex flex-col items-center justify-center h-full px-6 text-gray-900">
      <!-- Success Animation -->
      <div class="mb-8 animate-bounce-slow">
        <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-500 rounded-3xl flex items-center justify-center shadow-md backdrop-blur-sm border border-green-400/30">
          <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>

      <h2 class="text-4xl font-bold text-center mb-3">All Set!</h2>
      <p class="text-lg text-gray-600 text-center mb-12">You're ready to stay informed about disease outbreaks</p>

      <div class="w-full max-w-sm space-y-3 mb-16">
        <div class="flex items-start gap-3 p-4 bg-gray-50 backdrop-blur-md rounded-xl border border-gray-200">
          <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">Notifications Enabled</p>
          </div>
        </div>
        <div class="flex items-start gap-3 p-4 bg-gray-50 backdrop-blur-md rounded-xl border border-gray-200">
          <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">Location Set</p>
          </div>
        </div>
      </div>

      <!-- Start Button -->
      <button
        @click="handleComplete"
        class="w-full max-w-sm px-8 py-3.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full font-semibold text-sm shadow-md hover:shadow-lg transform hover:scale-105 transition-all"
      >
        Start Using PHO Alert
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const currentStep = ref<'welcome' | 'permissions' | 'location' | 'complete'>('welcome')

const emit = defineEmits<{
  complete: []
}>()

const nextStep = () => {
  if (currentStep.value === 'welcome') {
    currentStep.value = 'permissions'
  } else if (currentStep.value === 'permissions') {
    currentStep.value = 'location'
  } else if (currentStep.value === 'location') {
    currentStep.value = 'complete'
  }
}

const handleComplete = () => {
  localStorage.setItem('pho-alert-welcomed', 'true')
  emit('complete')
}
</script>

<style scoped>
@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-16px);
  }
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-bounce-slow {
  animation: bounce-slow 3s ease-in-out infinite;
}

.animate-fade-in {
  animation: fade-in 0.8s ease-out forwards;
  opacity: 0;
}

.animate-slide-up {
  animation: slide-up 0.6s ease-out forwards;
  opacity: 0;
}
</style>
