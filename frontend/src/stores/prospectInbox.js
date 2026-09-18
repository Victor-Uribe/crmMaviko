import { defineStore } from 'pinia'
import api from '../services/api'

export const useProspectInboxStore = defineStore('prospectInbox', {
  state: () => ({
    pendingCount: 0,
    loading: false,
    initialized: false,
  }),

  actions: {
    async refreshCount() {
      if (this.loading) return

      this.loading = true
      try {
        const response = await api.get('/prospect-import-items/pending')
        this.pendingCount = Number(response.data?.data?.total ?? 0)
        this.initialized = true
      } catch {
        // El contador es auxiliar; no bloqueamos el resto del CRM si falla.
      } finally {
        this.loading = false
      }
    },
  },
})
