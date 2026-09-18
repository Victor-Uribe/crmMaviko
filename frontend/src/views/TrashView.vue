<script setup>
import { onMounted, ref } from 'vue'
import { RotateCcw } from 'lucide-vue-next'
import api from '../services/api'
import EmptyState from '../components/ui/EmptyState.vue'
import LoadingBlock from '../components/ui/LoadingBlock.vue'
import { useToastStore } from '../stores/toast'

const toast=useToastStore();const items=ref([]);const loading=ref(true);const restoring=ref(null)
const load=async()=>{loading.value=true;try{const response=await api.get('/prospects/trash');const data=response.data.data;items.value=data?.data??data??[]}catch(error){toast.error(error?.response?.data?.message||'No fue posible cargar la papelera.')}finally{loading.value=false}}
const restore=async(id)=>{restoring.value=id;try{const response=await api.patch(`/prospects/${id}/restore`);toast.success(response.data.message||'Prospecto restaurado.');await load()}catch(error){toast.error(error?.response?.data?.message||'No fue posible restaurar el prospecto.')}finally{restoring.value=null}}
onMounted(load)
</script>
<template>
<section class="page"><div class="page-header"><div><h1 class="page-title">Papelera</h1><p class="page-subtitle">Prospectos eliminados mediante Soft Delete.</p></div></div>
<section class="card table-shell trash-card"><LoadingBlock v-if="loading"/><EmptyState v-else-if="!items.length" title="La papelera está vacía" description="Los prospectos eliminados aparecerán aquí y podrás restaurarlos."/>
<div v-else class="table-scroll"><table class="data-table"><thead><tr><th>Negocio</th><th>Ciudad</th><th>Eliminado</th><th></th></tr></thead><tbody><tr v-for="item in items" :key="item.id"><td><strong>{{ item.business_name }}</strong><span>{{ item.category||'Sin categoría' }}</span></td><td>{{ item.city||'—' }}</td><td>{{ $formatDateTime(item.deleted_at) }}</td><td class="text-right"><button class="btn btn-secondary" type="button" :disabled="restoring===item.id" @click="restore(item.id)"><RotateCcw :size="14"/>{{ restoring===item.id?'Restaurando...':'Restaurar' }}</button></td></tr></tbody></table></div></section></section>
</template>
<style scoped>.trash-card{box-shadow:none}td strong{display:block;color:var(--navy);font-size:12px}td span{display:block;margin-top:3px;color:var(--muted);font-size:10px}</style>
