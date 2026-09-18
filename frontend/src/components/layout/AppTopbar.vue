<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { Menu, Search, Bell, Plus, ChevronDown, LogOut, UserRound } from 'lucide-vue-next'
import { useAuthStore } from '../../stores/auth'
import { useToastStore } from '../../stores/toast'

const emit = defineEmits(['toggle-sidebar'])
const router = useRouter()
const auth = useAuthStore()
const toast = useToastStore()
const search = ref('')
const menuOpen = ref(false)
const menuRef = ref(null)

const userName = computed(() => auth.user?.name || 'Usuario MAVIKO')
const userEmail = computed(() => auth.user?.email || '')
const initials = computed(() => userName.value.trim().split(/\s+/).slice(0, 2).map(x => x[0]).join('').toUpperCase())

const submitSearch = () => {
  const value = search.value.trim()
  router.push({ name: 'prospects', query: value ? { search: value } : {} })
}

const logout = async () => {
  menuOpen.value = false
  try {
    await auth.logout()
    router.replace({ name: 'login' })
  } catch (error) {
    toast.error(error?.response?.data?.message || 'No fue posible cerrar la sesión.')
  }
}

const handleOutside = (event) => {
  if (menuRef.value && !menuRef.value.contains(event.target)) menuOpen.value = false
}

onMounted(() => document.addEventListener('click', handleOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleOutside))
</script>

<template>
  <header class="topbar">
    <button class="menu-button" type="button" aria-label="Abrir menú" @click="emit('toggle-sidebar')">
      <Menu :size="20" />
    </button>

    <form class="global-search" @submit.prevent="submitSearch">
      <Search :size="16" />
      <input v-model="search" type="search" placeholder="Buscar prospectos..." aria-label="Buscar prospectos" />
      <kbd>⌘ K</kbd>
    </form>

    <div class="topbar-actions">
      <button class="icon-button" type="button" aria-label="Notificaciones"><Bell :size="17" /></button>
      <button class="quick-button" type="button" @click="router.push({ name: 'prospects', query: { create: '1' } })">
        <Plus :size="16" />
        <span>Prospecto</span>
      </button>

      <div ref="menuRef" class="user-menu-wrap">
        <button class="user-card" type="button" @click.stop="menuOpen = !menuOpen">
          <span class="avatar">{{ initials }}</span>
          <div>
            <strong>{{ userName }}</strong>
            <span>{{ userEmail || 'MAVIKO Digital' }}</span>
          </div>
          <ChevronDown :size="14" :class="{ rotated: menuOpen }" />
        </button>

        <div v-if="menuOpen" class="user-menu">
          <div class="menu-identity"><UserRound :size="16" /><div><strong>{{ userName }}</strong><span>{{ userEmail }}</span></div></div>
          <button type="button" @click="logout"><LogOut :size="15" /><span>Cerrar sesión</span></button>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
.topbar { position: sticky; top: 0; z-index: 25; height: 64px; display: flex; align-items: center; gap: 18px; padding: 0 22px; border-bottom: 1px solid var(--border); background: rgba(255,255,255,.96); backdrop-filter: blur(12px); }
.menu-button { display: none; width: 38px; height: 38px; border: 1px solid var(--border); border-radius: 9px; background: #fff; cursor: pointer; }
.global-search { width: min(390px, 42vw); height: 38px; display: flex; align-items: center; gap: 8px; padding: 0 10px; border: 1px solid var(--border); border-radius: 9px; background: #fff; color: var(--muted); }
.global-search input { flex: 1; min-width: 0; border: 0; outline: 0; color: var(--text); font-size: 12px; background: transparent; }
kbd { padding: 2px 6px; border: 1px solid var(--border); border-radius: 5px; color: #9aa6b5; font-family: inherit; font-size: 10px; }
.topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 8px; }
.icon-button { width: 36px; height: 36px; display: grid; place-items: center; border: 1px solid transparent; border-radius: 9px; background: transparent; color: var(--text-2); cursor: pointer; }
.icon-button:hover { background: #f5f8fa; }
.quick-button { height: 36px; display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--border); border-radius: 9px; padding: 0 11px; background: #fff; color: var(--text); font-size: 12px; font-weight: 700; cursor: pointer; }
.user-menu-wrap { position: relative; margin-left: 7px; padding-left: 14px; border-left: 1px solid var(--border); }
.user-card { display: flex; align-items: center; gap: 8px; border: 0; padding: 0; background: transparent; cursor: pointer; text-align: left; }
.avatar { width: 30px; height: 30px; display: grid; place-items: center; border-radius: 50%; background: #e9f8fb; color: #067b96; font-size: 11px; font-weight: 800; }
.user-card div { display: flex; flex-direction: column; max-width: 160px; }
.user-card strong { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--navy); font-size: 11px; }
.user-card span:not(.avatar) { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--muted); font-size: 9px; }
.user-card svg { color: var(--muted); transition: transform .16s ease; }.user-card svg.rotated{transform:rotate(180deg)}
.user-menu { position: absolute; top: 45px; right: 0; width: 240px; overflow: hidden; border: 1px solid var(--border); border-radius: 11px; background: #fff; box-shadow: 0 14px 40px rgba(7,17,31,.12); }
.menu-identity { display: flex; align-items: center; gap: 9px; padding: 13px; border-bottom: 1px solid var(--border); color: var(--muted); }.menu-identity div{display:flex;flex-direction:column;min-width:0}.menu-identity strong{color:var(--navy);font-size:11px}.menu-identity span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px;color:var(--muted);font-size:9px}.user-menu>button{width:100%;display:flex;align-items:center;gap:9px;border:0;padding:11px 13px;background:#fff;color:#b4233d;font-size:11px;font-weight:700;cursor:pointer}.user-menu>button:hover{background:#fff5f6}
@media (max-width: 980px) { .menu-button { display: grid; place-items: center; } }
@media (max-width: 720px) { .global-search { flex: 1; width: auto; } .global-search kbd, .quick-button span, .user-card div, .user-card>svg { display: none; } .user-menu-wrap { padding-left: 8px; } }
</style>
