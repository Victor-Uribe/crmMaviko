<script setup>
import { computed } from 'vue'
import { BarChart3 } from 'lucide-vue-next'
import { formatOpportunityStatus } from '../../utils/formatters'

const props = defineProps({ data: { type: Object, default: () => ({}) } })
const stages = computed(() => [
  ['detected', props.data.detected ?? 0],
  ['offered', props.data.offered ?? 0],
  ['quoted', props.data.quoted ?? 0],
  ['negotiating', props.data.negotiating ?? 0],
  ['won', props.data.won ?? 0],
])
const max = computed(() => Math.max(1, ...stages.value.map(([, v]) => Number(v || 0))))
</script>

<template>
  <section class="card pipeline-card">
    <header>
      <div class="title"><span><BarChart3 :size="16" /></span><div><h2>Pipeline comercial</h2><p>Distribución de oportunidades por etapa</p></div></div>
      <span class="total">{{ data.total ?? 0 }} oportunidades</span>
    </header>
    <div class="bars">
      <div v-for="([key, value]) in stages" :key="key" class="bar-item">
        <div class="bar-meta"><span>{{ formatOpportunityStatus(key) }}</span><strong>{{ value }}</strong></div>
        <div class="track"><span :style="{ width: `${Math.max(6, (Number(value || 0) / max) * 100)}%` }" :class="key"></span></div>
      </div>
    </div>
    <footer>
      <div><span>Potencial abierto</span><strong>{{ $formatCurrency(data.potential_amount ?? 0) }}</strong></div>
      <div><span>Ganado</span><strong>{{ $formatCurrency(data.won_amount ?? 0) }}</strong></div>
    </footer>
  </section>
</template>

<style scoped>
.pipeline-card { overflow:hidden; box-shadow:none; }
header { display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 18px;border-bottom:1px solid var(--border); }
.title { display:flex;align-items:center;gap:10px; }.title>span{width:30px;height:30px;display:grid;place-items:center;border-radius:8px;background:#f3f7fa;color:var(--text-2)}
h2{margin:0;font-size:13px;color:var(--navy)}p{margin:3px 0 0;font-size:10px;color:var(--muted)}.total{font-size:10px;color:var(--muted)}
.bars{display:grid;gap:16px;padding:20px 18px 18px}.bar-meta{display:flex;justify-content:space-between;gap:14px;margin-bottom:6px;font-size:11px;color:var(--text-2)}.bar-meta strong{color:var(--navy)}.track{height:10px;border-radius:999px;background:#eff3f7;overflow:hidden}.track span{display:block;height:100%;border-radius:999px;background:#6d5ce7}.track span.offered{background:#38d9f2}.track span.quoted{background:#4f9cf9}.track span.negotiating{background:#8b7cf6}.track span.won{background:#18b8b0}
footer{display:grid;grid-template-columns:1fr 1fr;border-top:1px solid var(--border)}footer div{padding:14px 18px}footer div+div{border-left:1px solid var(--border)}footer span{display:block;color:var(--muted);font-size:10px;margin-bottom:3px}footer strong{color:var(--navy);font-size:16px}
</style>
