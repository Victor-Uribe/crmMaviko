<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { CalendarClock, AlertTriangle, CalendarDays, ArrowRight } from 'lucide-vue-next'
import api from '../services/api'
import StatusBadge from '../components/ui/StatusBadge.vue'
import EmptyState from '../components/ui/EmptyState.vue'
import LoadingBlock from '../components/ui/LoadingBlock.vue'
import { useToastStore } from '../stores/toast'

const router=useRouter();const toast=useToastStore();const active=ref('today');const items=ref([]);const loading=ref(false)
const tabs=[{key:'today',label:'Hoy',icon:CalendarClock},{key:'overdue',label:'Vencidos',icon:AlertTriangle},{key:'upcoming',label:'Próximos 7 días',icon:CalendarDays}]
const load=async(key=active.value)=>{active.value=key;loading.value=true;try{const url=key==='upcoming'?'/followups/upcoming?days=7':`/followups/${key}`;const response=await api.get(url);items.value=response.data.data??[]}catch(error){toast.error(error?.response?.data?.message||'No fue posible cargar los seguimientos.')}finally{loading.value=false}}
onMounted(()=>load())
</script>
<template>
<section class="page">
<div class="page-header"><div><h1 class="page-title">Seguimientos</h1><p class="page-subtitle">Tu agenda comercial en un solo lugar.</p></div></div>
<div class="tabs card"><button v-for="tab in tabs" :key="tab.key" type="button" :class="{active:active===tab.key}" @click="load(tab.key)"><component :is="tab.icon" :size="15" />{{ tab.label }}</button></div>
<section class="card list-card">
<LoadingBlock v-if="loading" />
<EmptyState v-else-if="!items.length" title="Sin seguimientos" description="No hay actividades en este periodo." />
<div v-else class="followup-list">
<button v-for="item in items" :key="item.id" class="followup-row" type="button" @click="router.push(`/prospects/${item.prospect_id}`)">
<div class="date-box"><strong>{{ $formatDate(item.scheduled_at) }}</strong><span>{{ $formatTime(item.scheduled_at) }}</span></div>
<div class="followup-main"><div><strong>{{ item.prospect?.business_name || 'Prospecto' }}</strong><span>{{ item.title }}</span></div><small>{{ item.type }}<template v-if="item.opportunity?.service"> · {{ item.opportunity.service.name }}</template></small></div>
<StatusBadge :value="item.priority" type="priority" /><StatusBadge :value="item.status" type="followup" /><ArrowRight :size="16" class="arrow" />
</button>
</div>
</section>
</section>
</template>
<style scoped>
.tabs{display:flex;gap:4px;width:max-content;padding:5px;box-shadow:none;margin-bottom:14px}.tabs button{height:34px;display:flex;align-items:center;gap:7px;border:0;border-radius:7px;padding:0 11px;background:transparent;color:var(--muted);font-size:11px;font-weight:700;cursor:pointer}.tabs button.active{background:#eef7f9;color:#087e98}.list-card{box-shadow:none;overflow:hidden}.followup-row{width:100%;display:grid;grid-template-columns:125px 1fr auto auto 20px;align-items:center;gap:14px;padding:14px 18px;border:0;border-bottom:1px solid var(--border);background:#fff;text-align:left;cursor:pointer}.followup-row:last-child{border-bottom:0}.followup-row:hover{background:#fbfdfe}.date-box{display:flex;flex-direction:column;gap:3px}.date-box strong{color:var(--navy);font-size:11px}.date-box span{color:var(--primary);font-size:10px}.followup-main{min-width:0}.followup-main>div{display:flex;flex-direction:column}.followup-main strong{color:var(--navy);font-size:12px}.followup-main span{margin-top:3px;color:var(--text-2);font-size:11px}.followup-main small{display:block;margin-top:5px;color:var(--muted);font-size:9px;text-transform:capitalize}.arrow{color:#a4afbb}@media(max-width:760px){.tabs{width:100%;overflow-x:auto}.followup-row{grid-template-columns:1fr auto}.date-box,.followup-row>.followup:nth-last-child(n){display:none}.arrow{display:none}}
</style>
