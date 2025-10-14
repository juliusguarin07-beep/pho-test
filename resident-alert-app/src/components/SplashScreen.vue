<template>
  <div class="fixed inset-0 z-50 bg-gradient-to-br from-primary-600 via-primary-700 to-blue-800">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute w-96 h-96 bg-white/10 rounded-full blur-3xl -top-20 -left-20 animate-pulse"></div>
      <div class="absolute w-96 h-96 bg-white/10 rounded-full blur-3xl -bottom-20 -right-20 animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <!-- Content -->
    <div class="relative flex flex-col items-center justify-center h-full px-6 text-white">
      <!-- Logo/Icon -->
      <div class="mb-8 animate-bounce-slow">
        <div class="relative">
          <div class="absolute inset-0 bg-white/20 rounded-full blur-xl"></div>
          <div class="relative w-32 h-32 bg-white rounded-full flex items-center justify-center shadow-2xl">
            <svg class="w-20 h-20 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>

      <!-- App Name -->
      <h1 class="text-4xl font-bold text-center mb-2 animate-fade-in">
        PHO Alert
      </h1>
      <p class="text-xl text-primary-100 text-center mb-2 animate-fade-in" style="animation-delay: 0.2s;">
        Disease Surveillance
      </p>
      <p class="text-sm text-primary-200 text-center mb-12 animate-fade-in" style="animation-delay: 0.4s;">
        Provincial Health Office - Pangasinan
      </p>

      <!-- Feature Cards -->
      <div class="w-full max-w-sm space-y-4 mb-12">
        <div class="flex items-start gap-4 p-4 bg-white/10 backdrop-blur-sm rounded-xl animate-slide-up" style="animation-delay: 0.6s;">
          <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="font-bold mb-1">Real-Time Alerts</h3>
            <p class="text-sm text-primary-100">Get instant notifications about outbreak alerts in your area</p>
          </div>
        </div>

        <div class="flex items-start gap-4 p-4 bg-white/10 backdrop-blur-sm rounded-xl animate-slide-up" style="animation-delay: 0.8s;">
          <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="font-bold mb-1">Interactive Map</h3>
            <p class="text-sm text-primary-100">View outbreak locations on an interactive color-coded map</p>
          </div>
        </div>

        <div class="flex items-start gap-4 p-4 bg-white/10 backdrop-blur-sm rounded-xl animate-slide-up" style="animation-delay: 1s;">
          <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="font-bold mb-1">Prevention Tips</h3>
            <p class="text-sm text-primary-100">Access preventive measures and emergency contacts</p>
          </div>
        </div>
      </div>

      <!-- Get Started Button -->
      <button
        @click="handleGetStarted"
        class="w-full max-w-sm px-8 py-4 bg-white text-primary-700 rounded-full font-bold text-lg shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-200 animate-fade-in"
        style="animation-delay: 1.2s;"
      >
        Get Started
      </button>

      <!-- Skip Button -->
      <button
        @click="handleSkip"
        class="mt-4 text-sm text-primary-200 hover:text-white transition-colors animate-fade-in"
        style="animation-delay: 1.4s;"
      >
        Skip Introduction
      </button>

      <!-- Version -->
      <p class="absolute bottom-4 text-xs text-primary-300 animate-fade-in" style="animation-delay: 1.6s;">
        Version 1.0.0
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits<{
  complete: []
}>()

const handleGetStarted = () => {
  // Mark as completed and close splash
  localStorage.setItem('pho-alert-welcomed', 'true')
  emit('complete')
}

const handleSkip = () => {
  // Mark as completed and close splash
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
    transform: translateY(-20px);
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
    transform: translateY(30px);
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
  animation: fade-in 1s ease-out forwards;
  opacity: 0;
}

.animate-slide-up {
  animation: slide-up 0.8s ease-out forwards;
  opacity: 0;
}
</style>
