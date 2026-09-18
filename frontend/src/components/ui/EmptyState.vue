<script setup>
import { Inbox } from 'lucide-vue-next'
defineProps({ title: { type: String, default: 'Sin información' }, description: { type: String, default: '' } })
</script>
<template>
  <div class="empty-state"><span><Inbox :size="20" /></span><strong>{{ title }}</strong><p v-if="description">{{ description }}</p><slot /></div>
</template>
<style scoped>
.empty-state { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:180px; padding:28px; text-align:center; color:var(--muted); }
.empty-state > span { width:42px; height:42px; display:grid; place-items:center; border-radius:12px; background:#f2f6f9; color:#7f8b99; margin-bottom:10px; }
strong { color:var(--text-2); font-size:13px; } p { max-width:420px; margin:5px 0 0; font-size:12px; line-height:1.55; }
</style>
