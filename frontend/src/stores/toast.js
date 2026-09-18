import { defineStore } from 'pinia'

let nextId = 1

export const useToastStore = defineStore('toast', {
  state: () => ({ items: [] }),
  actions: {
    show(message, type = 'success', duration = 3200) {
      const id = nextId++
      this.items.push({ id, message, type })
      if (duration) window.setTimeout(() => this.remove(id), duration)
    },
    success(message) { this.show(message, 'success') },
    error(message) { this.show(message, 'error', 4500) },
    info(message) { this.show(message, 'info') },
    remove(id) { this.items = this.items.filter((item) => item.id !== id) },
  },
})
