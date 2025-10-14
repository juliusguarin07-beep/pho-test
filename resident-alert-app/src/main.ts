import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './assets/main.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')

// Hide initial loading screen once app is mounted
const loadingScreen = document.getElementById('app-loading')
if (loadingScreen) {
  setTimeout(() => {
    loadingScreen.style.opacity = '0'
    loadingScreen.style.transition = 'opacity 0.5s ease-out'
    setTimeout(() => {
      loadingScreen.remove()
    }, 500)
  }, 1000) // Show loading for at least 1 second for smooth experience
}

// Register service worker for PWA
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js').catch(() => {
      // Service worker registration failed
    })
  })
}
