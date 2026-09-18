<script setup>
import { ArrowUpRight } from 'lucide-vue-next'
defineProps({ title: String, value: [String, Number], helper: { type: String, default: '' }, icon: { type: Object, default: null }, tone: { type: String, default: 'default' } })
</script>
<template>
  <article class="stat-card card" :class="tone">
    <div class="stat-top"><span class="stat-icon"><component v-if="icon" :is="icon" :size="16" /></span><ArrowUpRight :size="14" class="arrow" /></div>
    <p>{{ title }}</p><strong>{{ value }}</strong><span v-if="helper" class="helper">{{ helper }}</span>
  </article>
</template>
<style scoped>
.stat-card { min-height:132px; padding:16px; box-shadow:none; }
.stat-top { display:flex; align-items:center; justify-content:space-between; }
.stat-icon { width:30px;height:30px;display:grid;place-items:center;border-radius:8px;background:#f3f7fa;color:var(--text-2); }
.arrow { color:#a3afbc; } p { margin:13px 0 5px;color:var(--text-2);font-size:11px;font-weight:650; } strong { display:block;color:var(--navy);font-size:26px;letter-spacing:-.02em; } .helper { display:block;margin-top:6px;color:var(--muted);font-size:10px; }
.danger .stat-icon { background:#fff1f2;color:#c73b55; } .primary .stat-icon { background:#e9fafd;color:#07809b; } .success .stat-icon { background:#ecfdf5;color:#07865f; }
</style>
