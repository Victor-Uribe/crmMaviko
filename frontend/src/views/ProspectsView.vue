<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Plus, Search, SlidersHorizontal, ChevronLeft, ChevronRight, ExternalLink } from 'lucide-vue-next'
import api from '../services/api'
import ProspectFormModal from '../components/prospects/ProspectFormModal.vue'
import StatusBadge from '../components/ui/StatusBadge.vue'
import EmptyState from '../components/ui/EmptyState.vue'
import LoadingBlock from '../components/ui/LoadingBlock.vue'
import { useToastStore } from '../stores/toast'

const route = useRoute(); const router = useRouter(); const toast = useToastStore()
const prospects = ref([]); const loading = ref(false); const createOpen = ref(false)
const filters = reactive({ search:'', stage:'', city:'', min_score:'' })
const pagination = reactive({ currentPage:1,lastPage:1,total:0,perPage:15 })
let timer = null

const loadProspects = async (page=1) => {
  loading.value=true
  try {
    const response=await api.get('/prospects',{params:{page,search:filters.search||undefined,stage:filters.stage||undefined,city:filters.city||undefined,min_score:filters.min_score||undefined,per_page:pagination.perPage}})
    const result=response.data.data
    prospects.value=result.data??[];pagination.currentPage=result.current_page??1;pagination.lastPage=result.last_page??1;pagination.total=result.total??prospects.value.length;pagination.perPage=result.per_page??15
  } catch(error){toast.error(error?.response?.data?.message||'No fue posible cargar los prospectos.')} finally{loading.value=false}
}

const syncQuery = () => {
  filters.search = route.query.search ?? ''
  if (route.query.create === '1') createOpen.value = true
}
const clearFilters=()=>{Object.assign(filters,{search:'',stage:'',city:'',min_score:''});router.replace({name:'prospects'});loadProspects(1)}
const onSearchInput=()=>{clearTimeout(timer);timer=setTimeout(()=>loadProspects(1),350)}
const created=()=>loadProspects(1)
watch(()=>route.query,()=>{syncQuery();loadProspects(1)},{deep:true})
onMounted(()=>{syncQuery();loadProspects()})
</script>

<template>
<section class="page">
  <div class="page-header">
    <div><h1 class="page-title">Prospectos</h1><p class="page-subtitle">Califica, prioriza y convierte oportunidades comerciales.</p></div>
    <button class="btn btn-primary" type="button" @click="createOpen=true"><Plus :size="16" /> Nuevo prospecto</button>
  </div>

  <section class="filters-card card">
    <div class="field search-field"><label>Buscar</label><div class="search-box"><Search :size="15" /><input v-model="filters.search" placeholder="Nombre, giro o ciudad..." @input="onSearchInput" /></div></div>
    <div class="field"><label>Etapa</label><select v-model="filters.stage" class="select" @change="loadProspects(1)"><option value="">Todas</option><option value="new">Nuevo</option><option value="contacted">Contactado</option><option value="interested">Interesado</option><option value="quoted">Cotizado</option><option value="negotiation">Negociación</option><option value="won">Ganado</option><option value="lost">Perdido</option><option value="discarded">Descartado</option></select></div>
    <div class="field"><label>Ciudad</label><input v-model="filters.city" class="input" placeholder="Ixtapaluca" @keyup.enter="loadProspects(1)" /></div>
    <div class="field"><label>Score mínimo</label><input v-model="filters.min_score" class="input" type="number" min="0" max="100" placeholder="0" @change="loadProspects(1)" /></div>
    <button class="btn btn-secondary clear-btn" type="button" @click="clearFilters"><SlidersHorizontal :size="15" /> Limpiar</button>
  </section>

  <section class="card table-shell list-card">
    <header class="list-header"><div><strong>{{ pagination.total }}</strong> prospectos</div><span>15 por página</span></header>
    <LoadingBlock v-if="loading" />
    <EmptyState v-else-if="!prospects.length" title="No se encontraron prospectos" description="Prueba con otros filtros o registra un nuevo negocio.">
      <button class="btn btn-primary empty-action" type="button" @click="createOpen=true"><Plus :size="15" /> Nuevo prospecto</button>
    </EmptyState>
    <div v-else class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Negocio</th><th>Ubicación</th><th>Etapa</th><th>Score</th><th>Origen</th><th>Registro</th><th></th></tr></thead>
        <tbody>
          <tr v-for="prospect in prospects" :key="prospect.id" class="clickable" @click="router.push(`/prospects/${prospect.id}`)">
            <td><div class="business-cell"><span class="business-avatar">{{ prospect.business_name?.charAt(0)?.toUpperCase() }}</span><div><strong>{{ prospect.business_name }}</strong><span>{{ prospect.category || 'Sin categoría' }}</span></div></div></td>
            <td><strong class="cell-main">{{ prospect.city || '—' }}</strong><span class="cell-sub">{{ prospect.state || 'Sin estado' }}</span></td>
            <td><StatusBadge :value="prospect.stage" type="stage" /></td>
            <td><div class="score-wrap"><strong>{{ prospect.quality_score ?? 0 }}</strong><div class="score-track"><span :style="{width:`${prospect.quality_score ?? 0}%`}"></span></div></div></td>
            <td>{{ prospect.source || '—' }}</td>
            <td>{{ $formatDate(prospect.created_at) }}</td>
            <td><ExternalLink :size="15" class="row-icon" /></td>
          </tr>
        </tbody>
      </table>
    </div>
    <footer v-if="pagination.lastPage>1" class="pagination"><span>Página {{ pagination.currentPage }} de {{ pagination.lastPage }}</span><div><button class="btn btn-secondary btn-icon" :disabled="pagination.currentPage<=1" @click="loadProspects(pagination.currentPage-1)"><ChevronLeft :size="16" /></button><button class="btn btn-secondary btn-icon" :disabled="pagination.currentPage>=pagination.lastPage" @click="loadProspects(pagination.currentPage+1)"><ChevronRight :size="16" /></button></div></footer>
  </section>

  <ProspectFormModal :open="createOpen" @close="createOpen=false" @saved="created" />
</section>
</template>

<style scoped>
.filters-card{display:grid;grid-template-columns:minmax(260px,2fr) repeat(3,minmax(140px,1fr)) auto;align-items:end;gap:12px;padding:14px;box-shadow:none;margin-bottom:14px}.search-box{height:42px;display:flex;align-items:center;gap:8px;padding:0 11px;border:1px solid #d7e0e9;border-radius:9px;background:#fff;color:#8995a4}.search-box:focus-within{border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,178,217,.1)}.search-box input{width:100%;border:0;outline:0;background:transparent;font-size:12px;color:var(--text)}.clear-btn{margin-top:auto}.list-card{box-shadow:none}.list-header{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted)}.list-header strong{color:var(--navy)}.clickable{cursor:pointer}.business-cell{display:flex;align-items:center;gap:10px}.business-avatar{width:34px;height:34px;display:grid;place-items:center;flex:0 0 auto;border-radius:9px;background:#eef8fa;color:#087e98;font-size:12px;font-weight:800}.business-cell div,.cell-main,.cell-sub{display:block}.business-cell strong,.cell-main{color:var(--navy);font-size:12px}.business-cell span:last-child,.cell-sub{margin-top:3px;color:var(--muted);font-size:10px}.score-wrap{display:flex;align-items:center;gap:8px}.score-wrap strong{min-width:22px;color:var(--navy);font-size:11px}.score-track{width:58px;height:5px;border-radius:999px;background:#edf2f6;overflow:hidden}.score-track span{display:block;height:100%;background:var(--primary);border-radius:999px}.row-icon{color:#a4afbb}.pagination{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border);color:var(--muted);font-size:11px}.pagination div{display:flex;gap:7px}.empty-action{margin-top:14px}@media(max-width:1150px){.filters-card{grid-template-columns:repeat(2,1fr)}.clear-btn{width:max-content}}@media(max-width:620px){.filters-card{grid-template-columns:1fr}.clear-btn{width:100%}}
</style>
