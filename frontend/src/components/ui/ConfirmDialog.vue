<script setup>
import BaseModal from './BaseModal.vue'

defineProps({
  open: Boolean,
  title: { type: String, default: 'Confirmar acción' },
  message: { type: String, default: '¿Deseas continuar?' },
  confirmText: { type: String, default: 'Confirmar' },
  danger: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
})
const emit = defineEmits(['close', 'confirm'])
</script>

<template>
  <BaseModal :open="open" :title="title" width="460px" @close="emit('close')">
    <p class="message">{{ message }}</p>
    <template #footer>
      <button class="btn btn-secondary" type="button" @click="emit('close')">Cancelar</button>
      <button class="btn" :class="danger ? 'btn-danger' : 'btn-primary'" type="button" :disabled="loading" @click="emit('confirm')">
        {{ loading ? 'Procesando...' : confirmText }}
      </button>
    </template>
  </BaseModal>
</template>

<style scoped>.message { margin: 0; color: var(--text-2); line-height: 1.6; font-size: 14px; }</style>
