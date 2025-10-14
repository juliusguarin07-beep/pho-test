<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-primary-600 to-blue-700">
    <!-- Logo -->
    <div class="text-center">
      <div class="mb-6 animate-bounce">
        <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center shadow-2xl">
          <svg class="w-14 h-14 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
        </div>
      </div>

      <h1 class="text-3xl font-bold text-white mb-2">PHO Alert</h1>
      <p class="text-primary-100 mb-6">Disease Surveillance</p>

      <!-- Loading Spinner -->
      <div class="flex items-center justify-center gap-2">
        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
        <div class="w-2 h-2 bg-white rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
        <div class="w-2 h-2 bg-white rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// This component is just for visual display
// No logic needed as it's controlled by parent
</script>
