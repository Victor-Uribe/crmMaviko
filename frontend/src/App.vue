<script setup>
import { computed, ref } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import AppSidebar from './components/layout/AppSidebar.vue'
import AppTopbar from './components/layout/AppTopbar.vue'
import ToastStack from './components/ui/ToastStack.vue'

const sidebarOpen = ref(false)
const route = useRoute()
const authLayout = computed(() => route.meta.layout === 'auth')
</script>

<template>
  <RouterView v-if="authLayout" />

  <div v-else class="app-shell">
    <AppSidebar :open="sidebarOpen" @close="sidebarOpen = false" />
    <div class="app-main">
      <AppTopbar @toggle-sidebar="sidebarOpen = !sidebarOpen" />
      <main class="app-content">
        <RouterView />
      </main>
    </div>
  </div>

  <ToastStack />
</template>

<style scoped>
.app-shell { min-height: 100vh; }
.app-main { min-height: 100vh; margin-left: var(--sidebar-width); }
.app-content { padding: 24px 28px 40px; }
@media (max-width: 980px) {
  .app-main { margin-left: 0; }
  .app-content { padding: 20px 16px 36px; }
}
</style>
