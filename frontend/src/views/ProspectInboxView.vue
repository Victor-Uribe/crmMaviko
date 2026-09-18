<script setup>
import { computed, onMounted, ref } from 'vue'
import {
  Inbox,
  RefreshCw,
  Check,
  X,
  MapPin,
  MessageCircle,
  Mail,
  Phone,
  Globe2,
  ChevronDown,
  Building2,
  Sparkles,
} from 'lucide-vue-next'
import api, { getApiMessage } from '../services/api'
import LoadingBlock from '../components/ui/LoadingBlock.vue'
import EmptyState from '../components/ui/EmptyState.vue'
import ConfirmDialog from '../components/ui/ConfirmDialog.vue'
import { useToastStore } from '../stores/toast'
import { useProspectInboxStore } from '../stores/prospectInbox'

const toast = useToastStore()
const inboxStore = useProspectInboxStore()

const items = ref([])
const loading = ref(true)
const processing = ref(new Set())
const selected = ref(new Set())
const pagination = ref({ current_page: 1, last_page: 1, total: 0 })
const confirm = ref({ open: false, item: null, action: null })
const bulkLoading = ref(false)

const selectedCount = computed(() => selected.value.size)
const allVisibleSelected = computed(() => items.value.length > 0 && items.value.every((item) => selected.value.has(item.id)))

const scoreTone = (score) => {
  const value = Number(score ?? 0)
  if (value >= 85) return 'high'
  if (value >= 70) return 'medium'
  return 'low'
}

const contactIcon = (type) => {
  if (type === 'whatsapp') return MessageCircle
  if (type === 'email') return Mail
  if (type === 'phone') return Phone
  return Globe2
}

const payloadLocation = (item) => {
  const payload = item.payload ?? {}
  return [payload.city, payload.state].filter(Boolean).join(', ') || 'Sin ubicación'
}

const loadItems = async (page = 1) => {
  loading.value = true

  try {
    const response = await api.get('/prospect-import-items/pending', { params: { page } })
    const result = response.data?.data ?? {}

    items.value = result.data ?? []
    pagination.value = {
      current_page: Number(result.current_page ?? 1),
      last_page: Number(result.last_page ?? 1),
      total: Number(result.total ?? 0),
    }

    selected.value = new Set()
    inboxStore.pendingCount = pagination.value.total
    inboxStore.initialized = true
  } catch (error) {
    toast.error(getApiMessage(error, 'No fue posible cargar los prospectos por revisar.'))
  } finally {
    loading.value = false
  }
}

const toggleOne = (id) => {
  const next = new Set(selected.value)
  next.has(id) ? next.delete(id) : next.add(id)
  selected.value = next
}

const toggleVisible = () => {
  if (allVisibleSelected.value) {
    selected.value = new Set()
    return
  }

  selected.value = new Set(items.value.map((item) => item.id))
}

const setProcessing = (id, value) => {
  const next = new Set(processing.value)
  value ? next.add(id) : next.delete(id)
  processing.value = next
}

const approve = async (item, silent = false) => {
  setProcessing(item.id, true)
  try {
    await api.post(`/prospect-import-items/${item.id}/approve`)
    items.value = items.value.filter((current) => current.id !== item.id)
    selected.value.delete(item.id)
    pagination.value.total = Math.max(0, pagination.value.total - 1)
    inboxStore.pendingCount = pagination.value.total
    if (!silent) toast.success(`${item.business_name} fue aprobado e importado.`)
    return true
  } catch (error) {
    if (!silent) toast.error(getApiMessage(error, 'No fue posible aprobar el prospecto.'))
    return false
  } finally {
    setProcessing(item.id, false)
  }
}

const reject = async (item) => {
  setProcessing(item.id, true)
  try {
    await api.post(`/prospect-import-items/${item.id}/reject`)
    items.value = items.value.filter((current) => current.id !== item.id)
    selected.value.delete(item.id)
    pagination.value.total = Math.max(0, pagination.value.total - 1)
    inboxStore.pendingCount = pagination.value.total
    toast.info(`${item.business_name} fue rechazado.`)
  } catch (error) {
    toast.error(getApiMessage(error, 'No fue posible rechazar el prospecto.'))
  } finally {
    setProcessing(item.id, false)
    confirm.value = { open: false, item: null, action: null }
  }
}

const askReject = (item) => {
  confirm.value = { open: true, item, action: 'reject' }
}

const approveSelected = async () => {
  const ids = [...selected.value]
  if (!ids.length) return

  bulkLoading.value = true
  let success = 0
  let failed = 0

  for (const id of ids) {
    const item = items.value.find((current) => current.id === id)
    if (!item) continue

    const ok = await approve(item, true)
    ok ? success++ : failed++
  }

  if (success) toast.success(`${success} prospecto${success === 1 ? '' : 's'} aprobado${success === 1 ? '' : 's'}.`)
  if (failed) toast.error(`${failed} prospecto${failed === 1 ? '' : 's'} no se pudieron importar.`)

  bulkLoading.value = false
  await inboxStore.refreshCount()
}

onMounted(() => loadItems())
</script>

<template>
  <section class="page inbox-page">
    <div class="page-header">
      <div>
        <h1 class="page-title">Prospectos por revisar</h1>
        <p class="page-subtitle">Valida los prospectos recibidos automáticamente antes de agregarlos a tu cartera comercial.</p>
      </div>

      <div class="header-actions">
        <button class="btn btn-secondary" type="button" :disabled="loading" @click="loadItems(pagination.current_page)">
          <RefreshCw :size="15" :class="{ spin: loading }" />
          Actualizar
        </button>
        <button class="btn btn-primary" type="button" :disabled="!selectedCount || bulkLoading" @click="approveSelected">
          <Check :size="15" />
          {{ bulkLoading ? 'Aprobando...' : `Aprobar seleccionados (${selectedCount})` }}
        </button>
      </div>
    </div>

    <div class="summary-strip card">
      <div class="summary-icon"><Inbox :size="18" /></div>
      <div>
        <strong>{{ pagination.total }}</strong>
        <span>pendientes de revisión</span>
      </div>
      <div class="summary-copy">Los registros aprobados pasarán a Prospectos con sus contactos, oportunidades y mensajes sugeridos.</div>
      <label class="select-visible">
        <input type="checkbox" :checked="allVisibleSelected" @change="toggleVisible" />
        Seleccionar página
      </label>
    </div>

    <LoadingBlock v-if="loading" />

    <EmptyState
      v-else-if="items.length === 0"
      title="Bandeja al día"
      description="No hay prospectos pendientes de revisión en este momento."
    />

    <div v-else class="inbox-list">
      <article v-for="item in items" :key="item.id" class="prospect-review card" :class="{ selected: selected.has(item.id) }">
        <div class="review-select">
          <input type="checkbox" :checked="selected.has(item.id)" @change="toggleOne(item.id)" />
        </div>

        <div class="review-main">
          <div class="review-topline">
            <div class="business-title">
              <span class="business-icon"><Building2 :size="16" /></span>
              <div>
                <h2>{{ item.business_name }}</h2>
                <p>{{ item.payload?.category || 'Sin categoría' }}</p>
              </div>
            </div>

            <span class="score" :class="scoreTone(item.quality_score)">
              <strong>{{ item.quality_score ?? 0 }}</strong>
              <small>score</small>
            </span>
          </div>

          <div class="meta-row">
            <span><MapPin :size="13" /> {{ payloadLocation(item) }}</span>
            <span v-if="item.batch?.source"><Sparkles :size="13" /> {{ item.batch.source }}</span>
            <span v-if="item.payload?.source">Fuente: {{ item.payload.source }}</span>
          </div>

          <p v-if="item.payload?.opportunity" class="opportunity-copy">
            {{ item.payload.opportunity }}
          </p>

          <div v-if="item.payload?.contacts?.length" class="chip-group">
            <span v-for="(contact, index) in item.payload.contacts" :key="`${contact.type}-${index}`" class="contact-chip">
              <component :is="contactIcon(contact.type)" :size="12" />
              {{ contact.type }}
            </span>
          </div>

          <div v-if="item.payload?.opportunities?.length" class="services-row">
            <span class="services-label">Oportunidades</span>
            <span v-for="(opportunity, index) in item.payload.opportunities" :key="`${opportunity.service_code}-${index}`" class="service-chip">
              {{ opportunity.service_code?.replaceAll('_', ' ') }}
            </span>
          </div>

          <div v-if="item.payload?.outreach?.whatsapp" class="message-preview">
            <div class="message-title"><MessageCircle :size="14" /> Mensaje sugerido</div>
            <p>{{ item.payload.outreach.whatsapp }}</p>
          </div>

          <details class="payload-details">
            <summary>Ver información recibida <ChevronDown :size="14" /></summary>
            <div class="detail-grid">
              <div><span>Negocio</span><strong>{{ item.business_name }}</strong></div>
              <div><span>Ciudad</span><strong>{{ item.payload?.city || '—' }}</strong></div>
              <div><span>Estado</span><strong>{{ item.payload?.state || '—' }}</strong></div>
              <div><span>Calidad</span><strong>{{ item.quality_score ?? 0 }}/100</strong></div>
            </div>
          </details>
        </div>

        <div class="review-actions">
          <button class="btn btn-danger" type="button" :disabled="processing.has(item.id)" @click="askReject(item)">
            <X :size="14" /> Rechazar
          </button>
          <button class="btn btn-primary" type="button" :disabled="processing.has(item.id)" @click="approve(item)">
            <Check :size="14" /> {{ processing.has(item.id) ? 'Procesando...' : 'Aprobar' }}
          </button>
        </div>
      </article>
    </div>

    <div v-if="pagination.last_page > 1" class="pagination-row">
      <button class="btn btn-secondary" type="button" :disabled="pagination.current_page <= 1" @click="loadItems(pagination.current_page - 1)">Anterior</button>
      <span>Página {{ pagination.current_page }} de {{ pagination.last_page }}</span>
      <button class="btn btn-secondary" type="button" :disabled="pagination.current_page >= pagination.last_page" @click="loadItems(pagination.current_page + 1)">Siguiente</button>
    </div>

    <ConfirmDialog
      :open="confirm.open"
      title="Rechazar prospecto"
      :message="`¿Deseas rechazar ${confirm.item?.business_name ?? 'este prospecto'}? No se agregará a tu cartera comercial.`"
      confirm-text="Sí, rechazar"
      danger
      :loading="confirm.item ? processing.has(confirm.item.id) : false"
      @close="confirm = { open: false, item: null, action: null }"
      @confirm="reject(confirm.item)"
    />
  </section>
</template>

<style scoped>
.header-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.summary-strip { display: grid; grid-template-columns: auto auto minmax(0, 1fr) auto; align-items: center; gap: 13px; margin-bottom: 14px; padding: 13px 15px; box-shadow: none; }
.summary-icon { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 9px; background: #eaf9fc; color: #0a8baa; }
.summary-strip > div:nth-child(2) { display: flex; flex-direction: column; }
.summary-strip strong { color: var(--navy); font-size: 18px; line-height: 1; }
.summary-strip span { color: var(--muted); font-size: 10px; margin-top: 4px; }
.summary-copy { color: var(--text-2); font-size: 11px; line-height: 1.45; }
.select-visible { display: inline-flex; align-items: center; gap: 7px; color: var(--text-2); font-size: 11px; font-weight: 650; cursor: pointer; }
.select-visible input, .review-select input { accent-color: var(--primary); }
.inbox-list { display: grid; gap: 10px; }
.prospect-review { display: grid; grid-template-columns: 28px minmax(0, 1fr) auto; gap: 12px; padding: 16px; box-shadow: none; transition: border-color .15s ease, background .15s ease; }
.prospect-review.selected { border-color: #a8e5f0; background: #fbfeff; }
.review-select { padding-top: 7px; }
.review-select input { width: 15px; height: 15px; cursor: pointer; }
.review-main { min-width: 0; }
.review-topline { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; }
.business-title { display: flex; align-items: center; gap: 10px; min-width: 0; }
.business-icon { width: 32px; height: 32px; flex: 0 0 auto; display: grid; place-items: center; border-radius: 8px; background: #f2f6f9; color: #64748b; }
h2 { margin: 0; color: var(--navy); font-size: 14px; }
.business-title p { margin: 3px 0 0; color: var(--muted); font-size: 10px; }
.score { min-width: 54px; display: grid; grid-template-columns: auto auto; align-items: baseline; justify-content: center; gap: 3px; padding: 6px 8px; border-radius: 8px; background: #f4f7f9; color: #526174; }
.score strong { font-size: 14px; }.score small { font-size: 8px; text-transform: uppercase; letter-spacing: .04em; }.score.high { background: #ecfdf5; color: #047857; }.score.medium { background: #fff7ed; color: #b45309; }.score.low { background: #f8fafc; color: #64748b; }
.meta-row { display: flex; align-items: center; flex-wrap: wrap; gap: 10px 16px; margin-top: 10px; color: var(--muted); font-size: 10px; }
.meta-row span { display: inline-flex; align-items: center; gap: 4px; }
.opportunity-copy { max-width: 900px; margin: 11px 0 0; color: var(--text-2); font-size: 11px; line-height: 1.55; }
.chip-group, .services-row { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
.contact-chip, .service-chip { display: inline-flex; align-items: center; gap: 4px; min-height: 23px; padding: 3px 7px; border-radius: 999px; font-size: 9px; font-weight: 700; text-transform: capitalize; }
.contact-chip { background: #edf9fb; color: #087e98; }.service-chip { background: #f3f0ff; color: #5b50ad; }.services-label { margin-right: 2px; color: var(--muted); font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
.message-preview { margin-top: 12px; padding: 10px 12px; border: 1px solid #dceef3; border-radius: 9px; background: #f8fdfe; }
.message-title { display: flex; align-items: center; gap: 6px; color: #087e98; font-size: 10px; font-weight: 750; }
.message-preview p { display: -webkit-box; overflow: hidden; margin: 6px 0 0; color: var(--text-2); font-size: 10px; line-height: 1.5; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
.payload-details { margin-top: 10px; }
.payload-details summary { display: inline-flex; align-items: center; gap: 4px; color: var(--muted); font-size: 10px; font-weight: 650; cursor: pointer; list-style: none; }.payload-details summary::-webkit-details-marker { display: none; }
.detail-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; margin-top: 10px; }.detail-grid div { padding: 8px 10px; border-radius: 8px; background: #f8fafc; }.detail-grid span { display: block; margin-bottom: 3px; color: var(--muted); font-size: 8px; text-transform: uppercase; }.detail-grid strong { color: var(--navy); font-size: 10px; }
.review-actions { display: flex; align-items: flex-start; gap: 7px; padding-top: 1px; }
.review-actions .btn { min-height: 34px; padding: 0 10px; font-size: 11px; }
.pagination-row { display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-top: 14px; color: var(--muted); font-size: 11px; }
.spin { animation: spin .8s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }
@media (max-width: 900px) { .summary-strip { grid-template-columns: auto auto 1fr; }.select-visible { grid-column: 1 / -1; }.prospect-review { grid-template-columns: 26px minmax(0, 1fr); }.review-actions { grid-column: 2; }.detail-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 620px) { .header-actions { width: 100%; }.header-actions .btn { flex: 1; }.summary-copy { display: none; }.summary-strip { grid-template-columns: auto 1fr; }.prospect-review { grid-template-columns: 1fr; }.review-select { position: absolute; }.review-main { padding-left: 24px; }.review-actions { grid-column: 1; padding-left: 24px; }.review-actions .btn { flex: 1; }.detail-grid { grid-template-columns: 1fr 1fr; } }
</style>
