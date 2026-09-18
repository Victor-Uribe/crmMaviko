<script setup>
import { onMounted, ref } from 'vue'
import { Users, Clock3, CircleAlert, WalletCards, RefreshCw } from 'lucide-vue-next'
import api from '../services/api'
import StatCard from '../components/ui/StatCard.vue'
import LoadingBlock from '../components/ui/LoadingBlock.vue'
import PipelineOverview from '../components/dashboard/PipelineOverview.vue'
import AgendaList from '../components/dashboard/AgendaList.vue'
import ProspectStages from '../components/dashboard/ProspectStages.vue'
import { useToastStore } from '../stores/toast'

const dashboard = ref(null)
const loading = ref(true)
const toast = useToastStore()

const loadDashboard = async () => {
  loading.value = true
  try {
    const response = await api.get('/dashboard')
    dashboard.value = response.data.data
  } catch (error) {
    toast.error(error?.response?.data?.message || 'No fue posible cargar el dashboard.')
  } finally { loading.value = false }
}

onMounted(loadDashboard)
</script>

<template>
  <section class="page">
    <div class="page-header">
      <div><h1 class="page-title">Dashboard</h1><p class="page-subtitle">Resumen de la operación comercial de MAVIKO.</p></div>
      <button class="btn btn-secondary" type="button" :disabled="loading" @click="loadDashboard"><RefreshCw :size="15" :class="{ spin: loading }" /> Actualizar</button>
    </div>

    <LoadingBlock v-if="loading" />

    <template v-else-if="dashboard">
      <div class="stats-grid">
        <StatCard title="Prospectos activos" :value="dashboard.prospects?.total ?? 0" helper="Base comercial actual" :icon="Users" tone="primary" />
        <StatCard title="Seguimientos de hoy" :value="dashboard.followups?.today ?? 0" helper="Pendientes para hoy" :icon="Clock3" />
        <StatCard title="Seguimientos vencidos" :value="dashboard.followups?.overdue ?? 0" helper="Requieren atención" :icon="CircleAlert" tone="danger" />
        <StatCard title="Valor potencial" :value="$formatCurrency(dashboard.opportunities?.potential_amount ?? 0)" helper="Oportunidades abiertas" :icon="WalletCards" tone="success" />
      </div>

      <div class="main-grid">
        <PipelineOverview :data="dashboard.opportunities" />
        <ProspectStages :data="dashboard.prospects" />
      </div>

      <div class="agenda-grid">
        <AgendaList title="Seguimientos de hoy" subtitle="Próximas actividades programadas" :items="dashboard.agenda?.today ?? []" :count="dashboard.followups?.today ?? 0" />
        <AgendaList title="Seguimientos vencidos" subtitle="Acciones que necesitan seguimiento" :items="dashboard.agenda?.overdue ?? []" :count="dashboard.followups?.overdue ?? 0" overdue />
      </div>
    </template>
  </section>
</template>

<style scoped>
.stats-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; }
.main-grid { display:grid; grid-template-columns:minmax(0,1.45fr) minmax(330px,.85fr); gap:14px; margin-top:14px; }
.agenda-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; margin-top:14px; }
.spin { animation:spin .8s linear infinite; } @keyframes spin{to{transform:rotate(360deg)}}
@media(max-width:1180px){.stats-grid{grid-template-columns:repeat(2,1fr)}.main-grid{grid-template-columns:1fr}.agenda-grid{grid-template-columns:1fr}}
@media(max-width:620px){.stats-grid{grid-template-columns:1fr}}
</style>
