import { createRouter, createWebHistory } from 'vue-router'
import DashboardView from '../views/DashboardView.vue'
import ProspectsView from '../views/ProspectsView.vue'
import ProspectDetailView from '../views/ProspectDetailView.vue'
import FollowupsView from '../views/FollowupsView.vue'
import ClientsView from '../views/ClientsView.vue'
import TrashView from '../views/TrashView.vue'
import ProspectInboxView from '../views/ProspectInboxView.vue'
import LoginView from '../views/LoginView.vue'
import NotFoundView from '../views/NotFoundView.vue'
import { useAuthStore } from '../stores/auth'

const protectedRoute = { requiresAuth: true }

const router = createRouter({
  history: createWebHistory(),
  scrollBehavior: () => ({ top: 0 }),
  routes: [
    { path: '/login', name: 'login', component: LoginView, meta: { title: 'Iniciar sesión', layout: 'auth', guestOnly: true } },
    { path: '/', redirect: '/dashboard' },
    { path: '/dashboard', name: 'dashboard', component: DashboardView, meta: { title: 'Dashboard', ...protectedRoute } },
    { path: '/prospects', name: 'prospects', component: ProspectsView, meta: { title: 'Prospectos', ...protectedRoute } },
    { path: '/prospects/:id', name: 'prospect-detail', component: ProspectDetailView, meta: { title: 'Detalle del prospecto', ...protectedRoute } },
    { path: '/followups', name: 'followups', component: FollowupsView, meta: { title: 'Seguimientos', ...protectedRoute } },
    { path: '/clients', name: 'clients', component: ClientsView, meta: { title: 'Clientes', ...protectedRoute } },
    { path: '/prospecting/inbox', name: 'prospect-inbox', component: ProspectInboxView, meta: { title: 'Prospectos por revisar', ...protectedRoute } },
    { path: '/trash', name: 'trash', component: TrashView, meta: { title: 'Papelera', ...protectedRoute } },
    { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFoundView, meta: { title: 'Página no encontrada' } },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (!auth.initialized) {
    try {
      await auth.fetchUser()
    } catch {
      // Si el backend está caído, las vistas protegidas mostrarán el error
      // correspondiente después del intento de autenticación.
    }
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return { name: 'dashboard' }
  }

  return true
})

router.afterEach((to) => {
  document.title = `${to.meta.title || 'CRM'} · MAVIKO`
})

export default router
