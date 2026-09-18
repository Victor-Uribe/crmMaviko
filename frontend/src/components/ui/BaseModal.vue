<script setup>
import { X } from 'lucide-vue-next'

defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  width: { type: String, default: '680px' },
})
const emit = defineEmits(['close'])
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="modal-backdrop" @mousedown.self="emit('close')">
      <section class="modal" :style="{ maxWidth: width }" role="dialog" aria-modal="true">
        <header class="modal-header">
          <div>
            <h2>{{ title }}</h2>
            <p v-if="subtitle">{{ subtitle }}</p>
          </div>
          <button type="button" aria-label="Cerrar" @click="emit('close')"><X :size="18" /></button>
        </header>
        <div class="modal-body"><slot /></div>
        <footer v-if="$slots.footer" class="modal-footer"><slot name="footer" /></footer>
      </section>
    </div>
  </Teleport>
</template>

<style scoped>
.modal-backdrop { position: fixed; inset: 0; z-index: 80; display: grid; place-items: center; padding: 18px; background: rgba(7,17,31,.42); backdrop-filter: blur(2px); }
.modal { width: 100%; max-height: calc(100vh - 36px); display: flex; flex-direction: column; overflow: hidden; border: 1px solid rgba(255,255,255,.6); border-radius: 14px; background: #fff; box-shadow: 0 28px 70px rgba(7,17,31,.22); }
.modal-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; padding: 18px 20px; border-bottom: 1px solid var(--border); }
h2 { margin: 0; color: var(--navy); font-size: 18px; } p { margin: 4px 0 0; color: var(--muted); font-size: 12px; }
.modal-header button { width: 32px; height: 32px; display: grid; place-items: center; border: 0; border-radius: 8px; background: #f5f8fa; cursor: pointer; }
.modal-body { overflow-y: auto; padding: 20px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 20px; border-top: 1px solid var(--border); background: #fbfcfe; }
</style>
