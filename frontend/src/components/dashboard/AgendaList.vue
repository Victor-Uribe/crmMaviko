<script setup>
import { Clock3, ArrowRight } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import StatusBadge from '../ui/StatusBadge.vue'
import EmptyState from '../ui/EmptyState.vue'

const props = defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  items: { type: Array, default: () => [] },
  count: { type: Number, default: 0 },
  overdue: { type: Boolean, default: false },
})
const router = useRouter()
</script>

<template>
  <section class="card agenda-card">
    <header><div><h2>{{ title }}</h2><p>{{ subtitle }}</p></div><span class="count" :class="{ overdue }">{{ count }}</span></header>
    <div v-if="items.length" class="agenda-list">
      <button v-for="item in items" :key="item.id" type="button" class="agenda-item" @click="router.push(`/prospects/${item.prospect_id}`)">
        <span class="time"><Clock3 :size="14" />{{ overdue ? $formatDate(item.scheduled_at) : $formatTime(item.scheduled_at) }}</span>
        <span class="content"><strong>{{ item.prospect?.business_name || 'Prospecto' }}</strong><small>{{ item.title }}</small></span>
        <StatusBadge :value="item.priority" type="priority" />
        <ArrowRight :size="15" class="arrow" />
      </button>
    </div>
    <EmptyState v-else title="Todo al día" :description="overdue ? 'No tienes seguimientos vencidos.' : 'No tienes actividades pendientes para hoy.'" />
  </section>
</template>

<style scoped>
.agenda-card { box-shadow:none; overflow:hidden; } header{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--border)}h2{margin:0;color:var(--navy);font-size:13px}p{margin:3px 0 0;color:var(--muted);font-size:10px}.count{min-width:28px;height:25px;display:grid;place-items:center;border-radius:999px;background:#e8fafd;color:#087f99;font-size:10px;font-weight:800}.count.overdue{background:#fff1f2;color:#c32f4b}.agenda-list{display:flex;flex-direction:column}.agenda-item{display:grid;grid-template-columns:105px 1fr auto 18px;align-items:center;gap:12px;width:100%;padding:13px 18px;border:0;border-bottom:1px solid #edf1f5;background:#fff;text-align:left;cursor:pointer}.agenda-item:last-child{border-bottom:0}.agenda-item:hover{background:#fbfdfe}.time{display:flex;align-items:center;gap:6px;color:#718096;font-size:10px}.content{display:flex;flex-direction:column;min-width:0}.content strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--navy);font-size:11px}.content small{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:3px;color:var(--muted);font-size:10px}.arrow{color:#a1acb9}@media(max-width:650px){.agenda-item{grid-template-columns:1fr auto}.time,.arrow{display:none}}
</style>
