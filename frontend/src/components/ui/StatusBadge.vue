<script setup>
import { computed } from 'vue'
import { formatFollowupStatus, formatOpportunityStatus, formatPriority, formatProspectStage } from '../../utils/formatters'

const props = defineProps({
  value: { type: String, default: '' },
  type: { type: String, default: 'stage' },
})

const label = computed(() => {
  if (props.type === 'opportunity') return formatOpportunityStatus(props.value)
  if (props.type === 'followup') return formatFollowupStatus(props.value)
  if (props.type === 'priority') return formatPriority(props.value)
  return formatProspectStage(props.value)
})
</script>

<template><span class="badge status" :class="[type, value]">{{ label }}</span></template>

<style scoped>
.status { background: #eef3f8; color: #526174; }
.stage.new { background:#eef6ff; color:#2563eb; } .stage.contacted { background:#eafbff; color:#087b94; } .stage.interested, .stage.won { background:#ecfdf5; color:#047857; } .stage.quoted, .stage.negotiation { background:#fff7ed; color:#c15d0b; } .stage.lost, .stage.discarded { background:#fff1f2; color:#be123c; }
.opportunity.detected { background:#eff6ff; color:#1d4ed8; } .opportunity.offered { background:#ecfeff; color:#0e7490; } .opportunity.quoted, .opportunity.negotiating { background:#fff7ed; color:#c2410c; } .opportunity.won { background:#ecfdf5; color:#047857; } .opportunity.lost { background:#fff1f2; color:#be123c; }
.followup.pending { background:#fff7ed; color:#c2410c; } .followup.completed { background:#ecfdf5; color:#047857; } .followup.cancelled { background:#f1f5f9; color:#64748b; }
.priority.high { background:#fff1f2; color:#be123c; } .priority.medium { background:#fff7ed; color:#c2410c; } .priority.low { background:#f0fdf4; color:#15803d; }
</style>
