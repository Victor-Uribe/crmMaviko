<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { Funnel, ArrowRight, TrendingUp } from 'lucide-vue-next'

const props = defineProps({
  data: { type: Object, default: () => ({}) },
})

const router = useRouter()

const stages = [
  { key: 'new', label: 'Nuevos', color: '#0ea5c6' },
  { key: 'contacted', label: 'Contactados', color: '#22a9ca' },
  { key: 'interested', label: 'Interesados', color: '#3b9bd6' },
  { key: 'quoted', label: 'Cotizados', color: '#5b8bd9' },
  { key: 'negotiation', label: 'Negociación', color: '#6f76cf' },
  { key: 'won', label: 'Ganados', color: '#675fc2' },
]

const total = computed(() => Number(props.data?.total ?? 0))
const base = computed(() => Math.max(1, Number(props.data?.new ?? 0), total.value))

const stageRows = computed(() => stages.map((stage, index) => {
  const value = Number(props.data?.[stage.key] ?? 0)
  const previous = index === 0
    ? base.value
    : Number(props.data?.[stages[index - 1].key] ?? 0)

  const conversion = index === 0
    ? (base.value > 0 ? Math.round((value / base.value) * 100) : 0)
    : (previous > 0 ? Math.round((value / previous) * 100) : 0)

  const share = base.value > 0 ? Math.round((value / base.value) * 100) : 0

  return {
    ...stage,
    value,
    conversion,
    share,
    index: index + 1,
  }
}))

const contacted = computed(() => Number(props.data?.contacted ?? 0))
const contactRate = computed(() => {
  const newCount = Number(props.data?.new ?? 0)
  return newCount > 0 ? Math.round((contacted.value / newCount) * 100) : 0
})

const openStage = (stage) => {
  router.push({ name: 'prospects', query: { stage } })
}
</script>

<template>
  <section class="card funnel-card">
    <header class="funnel-header">
      <div class="title-block">
        <span class="header-icon"><Funnel :size="16" /></span>
        <div>
          <h2>Embudo de prospectos</h2>
          <p>Avance y conversión de la base comercial</p>
        </div>
      </div>

      <div class="summary-pill">
        <TrendingUp :size="13" />
        <span>{{ contactRate }}% contacto</span>
      </div>
    </header>

    <div class="funnel-body">
      <div class="stage-list">
        <button
          v-for="stage in stageRows"
          :key="stage.key"
          type="button"
          class="stage-row"
          @click="openStage(stage.key)"
        >
          <span class="step-dot" :style="{ '--stage-color': stage.color }">
            {{ stage.index }}
          </span>

          <div class="stage-main">
            <div class="stage-meta">
              <div class="stage-name">
                <strong>{{ stage.label }}</strong>
                <span v-if="stage.index > 1">{{ stage.conversion }}% desde la etapa anterior</span>
                <span v-else>{{ stage.share }}% de la base activa</span>
              </div>

              <div class="stage-value">
                <strong>{{ stage.value }}</strong>
                <span>{{ stage.share }}%</span>
              </div>
            </div>

            <div class="meter" :aria-label="`${stage.label}: ${stage.value}`">
              <span
                class="meter-fill"
                :style="{
                  width: `${stage.share}%`,
                  '--stage-color': stage.color,
                }"
              />
            </div>
          </div>

          <ArrowRight :size="15" class="row-arrow" />
        </button>
      </div>
    </div>

    <footer class="funnel-footer">
      <span>{{ total }} prospectos activos</span>
      <span>Haz clic en una etapa para filtrar la lista</span>
    </footer>
  </section>
</template>

<style scoped>
.funnel-card {
  overflow: hidden;
  box-shadow: none;
}

.funnel-header {
  min-height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px 18px;
  border-bottom: 1px solid var(--border);
}

.title-block {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.header-icon {
  width: 30px;
  height: 30px;
  flex: 0 0 auto;
  display: grid;
  place-items: center;
  border-radius: 8px;
  background: #edf9fb;
  color: #087e98;
}

h2 {
  margin: 0;
  color: var(--navy);
  font-size: 13px;
}

p {
  margin: 3px 0 0;
  color: var(--muted);
  font-size: 10px;
}

.summary-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 9px;
  border-radius: 999px;
  background: #f1f8fb;
  color: #2f6474;
  font-size: 10px;
  font-weight: 700;
  white-space: nowrap;
}

.funnel-body {
  padding: 13px 17px 14px;
}

.stage-list {
  position: relative;
  display: grid;
  gap: 3px;
}

.stage-list::before {
  content: '';
  position: absolute;
  top: 23px;
  bottom: 23px;
  left: 15px;
  width: 1px;
  background: #dce5ec;
}

.stage-row {
  position: relative;
  z-index: 1;
  width: 100%;
  display: grid;
  grid-template-columns: 30px minmax(0, 1fr) 18px;
  align-items: center;
  gap: 10px;
  border: 1px solid transparent;
  border-radius: 9px;
  padding: 8px 8px 8px 0;
  background: transparent;
  color: inherit;
  text-align: left;
  cursor: pointer;
  transition: background .15s ease, border-color .15s ease, transform .15s ease;
}

.stage-row:hover {
  border-color: #e4edf3;
  background: #f9fcfd;
  transform: translateX(2px);
}

.step-dot {
  width: 30px;
  height: 30px;
  display: grid;
  place-items: center;
  border: 3px solid #fff;
  border-radius: 50%;
  background: var(--stage-color);
  box-shadow: 0 0 0 1px #dce5ec;
  color: #fff;
  font-size: 10px;
  font-weight: 800;
}

.stage-main {
  min-width: 0;
}

.stage-meta {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 6px;
}

.stage-name {
  min-width: 0;
  display: flex;
  align-items: baseline;
  gap: 7px;
}

.stage-name strong {
  color: var(--navy);
  font-size: 11px;
  white-space: nowrap;
}

.stage-name span {
  overflow: hidden;
  color: var(--muted);
  font-size: 9px;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.stage-value {
  display: flex;
  align-items: baseline;
  gap: 7px;
  flex: 0 0 auto;
}

.stage-value strong {
  color: var(--navy);
  font-size: 13px;
}

.stage-value span {
  min-width: 30px;
  color: var(--muted);
  font-size: 9px;
  font-weight: 700;
  text-align: right;
}

.meter {
  height: 7px;
  overflow: hidden;
  border-radius: 999px;
  background: #eef3f6;
}

.meter-fill {
  display: block;
  height: 100%;
  min-width: 0;
  border-radius: inherit;
  background: var(--stage-color);
  transition: width .25s ease;
}

.row-arrow {
  color: #a9b5c2;
  transition: transform .15s ease, color .15s ease;
}

.stage-row:hover .row-arrow {
  color: var(--primary);
  transform: translateX(2px);
}

.funnel-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 11px 16px;
  border-top: 1px solid var(--border);
  color: var(--muted);
  font-size: 9px;
}

@media (max-width: 560px) {
  .summary-pill { display: none; }
  .stage-name span { display: none; }
  .stage-row { grid-template-columns: 30px minmax(0, 1fr); }
  .row-arrow { display: none; }
  .funnel-footer { flex-direction: column; align-items: flex-start; }
}
</style>
