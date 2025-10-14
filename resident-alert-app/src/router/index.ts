import { createRouter, createWebHistory } from 'vue-router'
import AlertsView from '@/views/AlertsView.vue'
import MapView from '@/views/MapView.vue'
import SettingsView from '@/views/SettingsView.vue'
import AlertDetailView from '@/views/AlertDetailView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/alerts'
    },
    {
      path: '/alerts',
      name: 'alerts',
      component: AlertsView,
      meta: { title: 'Alerts' }
    },
    {
      path: '/alerts/:id',
      name: 'alert-detail',
      component: AlertDetailView,
      meta: { title: 'Alert Details' }
    },
    {
      path: '/map',
      name: 'map',
      component: MapView,
      meta: { title: 'Map' }
    },
    {
      path: '/debug-map',
      name: 'debug-map',
      component: () => import('@/views/DebugMapView.vue'),
      meta: { title: 'Debug Map' }
    },
    {
      path: '/settings',
      name: 'settings',
      component: SettingsView,
      meta: { title: 'Settings' }
    }
  ]
})

router.beforeEach((to, _from, next) => {
  document.title = `${to.meta.title || 'PHO Alert'} - Disease Surveillance`
  next()
})

export default router
