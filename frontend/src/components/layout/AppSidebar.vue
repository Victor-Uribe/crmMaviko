<script setup>
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import {
  LayoutDashboard,
  Users,
  CalendarClock,
  UserRoundCheck,
  Trash2,
  X,
  Sparkles,
  Inbox,
} from 'lucide-vue-next'
import { useProspectInboxStore } from '../../stores/prospectInbox'

defineProps({ open: { type: Boolean, default: false } })
const emit = defineEmits(['close'])
const inbox = useProspectInboxStore()

const primary = [
  { to: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { to: '/prospects', label: 'Prospectos', icon: Users },
  { to: '/followups', label: 'Seguimientos', icon: CalendarClock },
  { to: '/clients', label: 'Clientes', icon: UserRoundCheck },
]

const tools = [
  { to: '/trash', label: 'Papelera', icon: Trash2 },
]

onMounted(() => {
  if (!inbox.initialized) inbox.refreshCount()
})
</script>

<template>
  <div v-if="open" class="sidebar-backdrop" @click="emit('close')" />
  <aside class="sidebar" :class="{ open }">
    <div class="brand-row">
      <RouterLink to="/dashboard" class="brand" @click="emit('close')">
        <span class="brand-mark"><Sparkles :size="17" /></span>
        <span>
          <strong>MAVIKO</strong>
          <small>CRM</small>
        </span>
      </RouterLink>
      <button class="mobile-close" type="button" aria-label="Cerrar menú" @click="emit('close')">
        <X :size="19" />
      </button>
    </div>

    <div class="nav-section">
      <p>General</p>
      <nav>
        <RouterLink v-for="item in primary" :key="item.to" :to="item.to" @click="emit('close')">
          <component :is="item.icon" :size="17" stroke-width="1.8" />
          <span>{{ item.label }}</span>
        </RouterLink>
      </nav>
    </div>

    <div class="nav-section">
      <p>Prospección</p>
      <nav>
        <RouterLink to="/prospecting/inbox" @click="emit('close')">
          <Inbox :size="17" stroke-width="1.8" />
          <span>Por revisar</span>
          <span v-if="inbox.pendingCount > 0" class="nav-count">{{ inbox.pendingCount > 99 ? '99+' : inbox.pendingCount }}</span>
        </RouterLink>
      </nav>
    </div>

    <div class="nav-section">
      <p>Herramientas</p>
      <nav>
        <RouterLink v-for="item in tools" :key="item.to" :to="item.to" @click="emit('close')">
          <component :is="item.icon" :size="17" stroke-width="1.8" />
          <span>{{ item.label }}</span>
        </RouterLink>
      </nav>
    </div>

    <div class="sidebar-footer">
      <div class="workspace-card">
        <span class="workspace-icon">M</span>
        <div>
          <strong>MAVIKO Digital</strong>
          <span>Prospección comercial</span>
        </div>
      </div>
      <span class="version">CRM v1.1</span>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  position: fixed;
  inset: 0 auto 0 0;
  z-index: 40;
  width: var(--sidebar-width);
  display: flex;
  flex-direction: column;
  border-right: 1px solid var(--border);
  background: #fff;
}
.brand-row { height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; border-bottom: 1px solid var(--border); }
.brand { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
.brand-mark { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 9px; background: linear-gradient(145deg, var(--navy-2), var(--primary)); color: #fff; }
.brand span:last-child { display: flex; align-items: baseline; gap: 6px; }
.brand strong { color: var(--navy); font-size: 15px; letter-spacing: 0.02em; }
.brand small { color: var(--primary); font-size: 10px; font-weight: 800; letter-spacing: 0.08em; }
.mobile-close { display: none; border: 0; background: transparent; cursor: pointer; }
.nav-section { padding: 16px 12px 2px; }
.nav-section > p { margin: 0 8px 8px; color: #9aa6b5; font-size: 9px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
nav { display: flex; flex-direction: column; gap: 3px; }
nav a { min-height: 38px; display: flex; align-items: center; gap: 10px; padding: 0 10px; border-radius: 8px; color: #4a5568; text-decoration: none; font-size: 13px; font-weight: 600; }
nav a > span:nth-child(2) { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
nav a:hover { background: #f5f8fa; color: var(--navy); }
nav a.router-link-active { background: #f0f4f7; color: var(--navy); }
nav a.router-link-active svg { color: var(--primary); }
.nav-count { min-width: 22px; height: 20px; display: inline-grid; place-items: center; margin-left: auto; padding: 0 6px; border-radius: 999px; background: #e7f8fb; color: #087e98; font-size: 9px; font-weight: 800; }
.sidebar-footer { margin-top: auto; padding: 14px 12px 18px; border-top: 1px solid var(--border); }
.workspace-card { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 10px; background: #f7fafc; }
.workspace-icon { width: 30px; height: 30px; display: grid; place-items: center; border-radius: 8px; background: var(--primary); color: #fff; font-weight: 800; }
.workspace-card div { display: flex; flex-direction: column; min-width: 0; }
.workspace-card strong { color: var(--navy); font-size: 12px; }
.workspace-card span:last-child { color: var(--muted); font-size: 10px; }
.version { display: block; margin-top: 10px; color: #a1acb9; font-size: 10px; text-align: center; }
.sidebar-backdrop { display: none; }
@media (max-width: 980px) {
  .sidebar { transform: translateX(-100%); transition: transform .2s ease; box-shadow: 12px 0 34px rgba(7,17,31,.13); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-backdrop { display: block; position: fixed; inset: 0; z-index: 35; background: rgba(7,17,31,.36); }
  .mobile-close { display: inline-grid; place-items: center; }
}
</style>
