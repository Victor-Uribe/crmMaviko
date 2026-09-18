<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { UserRoundCheck, ArrowRight } from 'lucide-vue-next'
import api from '../services/api'
import EmptyState from '../components/ui/EmptyState.vue'
import LoadingBlock from '../components/ui/LoadingBlock.vue'
import { useToastStore } from '../stores/toast'

const router=useRouter();const toast=useToastStore();const clients=ref([]);const loading=ref(true)
const load=async()=>{try{const response=await api.get('/prospects',{params:{stage:'won',per_page:100}});clients.value=response.data.data?.data??[]}catch(error){toast.error(error?.response?.data?.message||'No fue posible cargar los clientes.')}finally{loading.value=false}}
onMounted(load)
</script>
<template>
<section class="page"><div class="page-header"><div><h1 class="page-title">Clientes</h1><p class="page-subtitle">Prospectos que ya fueron marcados como ganados.</p></div></div>
<section class="card clients-card"><LoadingBlock v-if="loading"/><EmptyState v-else-if="!clients.length" title="Aún no hay clientes" description="Cuando una venta avance a la etapa Ganado aparecerá aquí."/>
<div v-else class="client-grid"><button v-for="client in clients" :key="client.id" type="button" class="client" @click="router.push(`/prospects/${client.id}`)"><span class="avatar"><UserRoundCheck :size="17" /></span><div><strong>{{ client.business_name }}</strong><span>{{ client.category || 'Sin categoría' }} · {{ client.city || 'Sin ciudad' }}</span></div><ArrowRight :size="16" /></button></div></section></section>
</template>
<style scoped>.clients-card{box-shadow:none;padding:14px}.client-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.client{display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:11px;padding:14px;border:1px solid var(--border);border-radius:10px;background:#fff;text-align:left;cursor:pointer}.client:hover{border-color:#c8d8e3;background:#fbfdfe}.avatar{width:36px;height:36px;display:grid;place-items:center;border-radius:9px;background:#ecfdf5;color:#07865f}.client div{display:flex;flex-direction:column;min-width:0}.client strong{color:var(--navy);font-size:12px}.client span:not(.avatar){margin-top:3px;color:var(--muted);font-size:10px}@media(max-width:700px){.client-grid{grid-template-columns:1fr}}</style>
