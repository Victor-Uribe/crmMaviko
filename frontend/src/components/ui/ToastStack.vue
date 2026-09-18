<script setup>
import { CheckCircle2, AlertCircle, Info, X } from 'lucide-vue-next'
import { useToastStore } from '../../stores/toast'

const toast = useToastStore()
const iconFor = (type) => type === 'error' ? AlertCircle : type === 'info' ? Info : CheckCircle2
</script>

<template>
  <div class="toast-stack" aria-live="polite">
    <div v-for="item in toast.items" :key="item.id" class="toast" :class="item.type">
      <component :is="iconFor(item.type)" :size="18" />
      <span>{{ item.message }}</span>
      <button type="button" aria-label="Cerrar" @click="toast.remove(item.id)"><X :size="15" /></button>
    </div>
  </div>
</template>

<style scoped>
.toast-stack { position: fixed; right: 18px; bottom: 18px; z-index: 100; display: flex; flex-direction: column; gap: 8px; width: min(380px, calc(100vw - 36px)); }
.toast { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 10px; padding: 12px 13px; border: 1px solid var(--border); border-radius: 10px; background: #fff; box-shadow: 0 12px 32px rgba(7,17,31,.14); color: var(--text-2); font-size: 12px; }
.toast.success > svg { color: var(--success); } .toast.error > svg { color: var(--danger); } .toast.info > svg { color: var(--info); }
.toast button { border: 0; background: transparent; color: #98a4b2; cursor: pointer; }
</style>
