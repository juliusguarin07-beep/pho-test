<template>
  <!-- Splash Screen (shown on first launch) -->
  <splash-screen v-if="showSplash" @complete="hideSplash" />

  <!-- Main App -->
  <div v-else class="flex flex-col h-screen bg-gradient-to-br from-slate-50 to-gray-100">
    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto pb-20 safe-top">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-200/50 safe-bottom z-50 shadow-lg shadow-gray-900/10">
      <div class="flex justify-around items-center h-16 max-w-lg mx-auto">
        <router-link
          to="/alerts"
          class="nav-item group"
          :class="{ 'nav-item-active': $route.path.startsWith('/alerts') }"
        >
          <svg class="w-6 h-6 transition-all group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <span class="text-xs mt-1.5 font-medium transition-colors">Alerts</span>
        </router-link>

        <router-link
          to="/map"
          class="nav-item group"
          :class="{ 'nav-item-active': $route.path === '/map' }"
        >
          <svg class="w-6 h-6 transition-all group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
          </svg>
          <span class="text-xs mt-1.5 font-medium transition-colors">Map</span>
        </router-link>

        <router-link
          to="/settings"
          class="nav-item group"
          :class="{ 'nav-item-active': $route.path === '/settings' }"
        >
          <svg class="w-6 h-6 transition-all group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span class="text-xs mt-1.5 font-medium transition-colors">Settings</span>
        </router-link>
      </div>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useSettingsStore } from '@/stores/settings'
import SplashScreen from '@/components/SplashScreen.vue'

const settingsStore = useSettingsStore()
const showSplash = ref(true)

const hideSplash = () => {
  showSplash.value = false
}

onMounted(() => {
  // Always show splash on load, don't check localStorage
  settingsStore.fetchMunicipalities()
  settingsStore.fetchBarangays()
})
</script>

<style scoped>
.nav-item {
  @apply flex flex-col items-center justify-center flex-1 py-2 text-gray-500 transition-all duration-200;
}

.nav-item-active {
  @apply text-blue-600 font-semibold;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
