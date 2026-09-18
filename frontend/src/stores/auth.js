import { defineStore } from 'pinia'
import api, { backend, ensureCsrfCookie } from '../services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    initialized: false,
    loading: false,
  }),

  getters: {
    isAuthenticated: (state) => Boolean(state.user),
  },

  actions: {
    async fetchUser() {
      try {
        const response = await api.get('/me')
        this.user = response.data?.data ?? response.data
      } catch (error) {
        if (error?.response?.status === 401 || error?.response?.status === 419) {
          this.user = null
        } else {
          throw error
        }
      } finally {
        this.initialized = true
      }
    },

    async login(credentials) {
      this.loading = true
      try {
        await ensureCsrfCookie()
        await backend.post('/login', credentials)
        await this.fetchUser()
        return this.user
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true
      try {
        await backend.post('/logout')
      } finally {
        this.user = null
        this.initialized = true
        this.loading = false
      }
    },
  },
})
