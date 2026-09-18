<script setup>
import { computed, reactive, ref, watch } from 'vue'
import BaseModal from '../ui/BaseModal.vue'
import api, { getApiMessage, getValidationErrors } from '../../services/api'
import { useToastStore } from '../../stores/toast'

const props = defineProps({ open: Boolean, prospect: { type: Object, default: null } })
const emit = defineEmits(['close', 'saved'])
const toast = useToastStore()
const saving = ref(false)
const errors = ref({})
const isEdit = computed(() => Boolean(props.prospect?.id))

const blank = () => ({
  business_name: '', category: '', description: '', country: 'México', state: '', city: '', address: '',
  source: '', source_url: '', opportunity: '', quality_score: 0, stage: 'new',
})
const form = reactive(blank())

const reset = () => {
  Object.assign(form, blank(), props.prospect ? {
    business_name: props.prospect.business_name ?? '', category: props.prospect.category ?? '', description: props.prospect.description ?? '',
    country: props.prospect.country ?? 'México', state: props.prospect.state ?? '', city: props.prospect.city ?? '', address: props.prospect.address ?? '',
    source: props.prospect.source ?? '', source_url: props.prospect.source_url ?? '', opportunity: props.prospect.opportunity ?? '',
    quality_score: props.prospect.quality_score ?? 0, stage: props.prospect.stage ?? 'new',
  } : {})
  errors.value = {}
}
watch(() => [props.open, props.prospect], reset, { deep: true })

const fieldError = (name) => errors.value?.[name]?.[0] ?? ''
const submit = async () => {
  saving.value = true
  errors.value = {}
  try {
    const payload = { ...form, quality_score: Number(form.quality_score || 0) }
    const response = isEdit.value
      ? await api.patch(`/prospects/${props.prospect.id}`, payload)
      : await api.post('/prospects', payload)
    toast.success(response.data.message || 'Prospecto guardado correctamente.')
    emit('saved', response.data.data)
    emit('close')
  } catch (error) {
    errors.value = getValidationErrors(error)
    if (!Object.keys(errors.value).length) toast.error(getApiMessage(error, 'No fue posible guardar el prospecto.'))
  } finally { saving.value = false }
}
</script>

<template>
  <BaseModal :open="open" :title="isEdit ? 'Editar prospecto' : 'Nuevo prospecto'" subtitle="Información comercial y de calificación." width="780px" @close="emit('close')">
    <form id="prospect-form" class="form-grid" @submit.prevent="submit">
      <div class="field span-2"><label>Nombre del negocio *</label><input v-model="form.business_name" class="input" placeholder="Ej. Hospital Ixta" /><span v-if="fieldError('business_name')" class="field-error">{{ fieldError('business_name') }}</span></div>
      <div class="field"><label>Giro / categoría</label><input v-model="form.category" class="input" placeholder="Hospital, restaurante..." /></div>
      <div class="field"><label>Etapa</label><select v-model="form.stage" class="select"><option value="new">Nuevo</option><option value="contacted">Contactado</option><option value="interested">Interesado</option><option value="quoted">Cotizado</option><option value="negotiation">Negociación</option><option value="won">Ganado</option><option value="lost">Perdido</option><option value="discarded">Descartado</option></select><span v-if="fieldError('stage')" class="field-error">{{ fieldError('stage') }}</span></div>
      <div class="field"><label>Ciudad</label><input v-model="form.city" class="input" placeholder="Ixtapaluca" /></div>
      <div class="field"><label>Estado</label><input v-model="form.state" class="input" placeholder="Estado de México" /></div>
      <div class="field"><label>País</label><input v-model="form.country" class="input" /></div>
      <div class="field"><label>Score de calidad</label><input v-model="form.quality_score" class="input" type="number" min="0" max="100" /><span v-if="fieldError('quality_score')" class="field-error">{{ fieldError('quality_score') }}</span></div>
      <div class="field"><label>Origen</label><input v-model="form.source" class="input" placeholder="Google Maps, ChatGPT..." /></div>
      <div class="field"><label>URL de origen</label><input v-model="form.source_url" class="input" type="url" placeholder="https://..." /><span v-if="fieldError('source_url')" class="field-error">{{ fieldError('source_url') }}</span></div>
      <div class="field span-2"><label>Dirección</label><input v-model="form.address" class="input" placeholder="Dirección del negocio" /></div>
      <div class="field span-2"><label>Descripción</label><textarea v-model="form.description" class="textarea" placeholder="Qué hace el negocio, tamaño, señales de capacidad..." /></div>
      <div class="field span-2"><label>Oportunidad detectada</label><textarea v-model="form.opportunity" class="textarea" placeholder="Por qué MAVIKO puede ayudarle..." /></div>
    </form>
    <template #footer>
      <button class="btn btn-secondary" type="button" @click="emit('close')">Cancelar</button>
      <button class="btn btn-primary" type="submit" form="prospect-form" :disabled="saving">{{ saving ? 'Guardando...' : (isEdit ? 'Guardar cambios' : 'Crear prospecto') }}</button>
    </template>
  </BaseModal>
</template>

<style scoped>
.form-grid { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px; }.span-2{grid-column:span 2}@media(max-width:650px){.form-grid{grid-template-columns:1fr}.span-2{grid-column:span 1}}
</style>
