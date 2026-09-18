<script setup>
import { computed, reactive, ref, watch } from 'vue'
import BaseModal from '../ui/BaseModal.vue'
import api, { getApiMessage, getValidationErrors } from '../../services/api'
import { useToastStore } from '../../stores/toast'

const props = defineProps({ open: Boolean, prospectId: { type: [Number, String], required: true }, contact: { type: Object, default: null } })
const emit = defineEmits(['close', 'saved'])
const toast = useToastStore(); const saving = ref(false); const errors = ref({}); const isEdit = computed(() => Boolean(props.contact?.id))
const blank = () => ({ type:'whatsapp', label:'', value:'', is_primary:false, is_verified:false, source_url:'', notes:'' })
const form = reactive(blank())
const reset = () => { Object.assign(form, blank(), props.contact ? { type:props.contact.type??'whatsapp',label:props.contact.label??'',value:props.contact.value??'',is_primary:Boolean(props.contact.is_primary),is_verified:Boolean(props.contact.is_verified),source_url:props.contact.source_url??'',notes:props.contact.notes??'' } : {}); errors.value={} }
watch(() => [props.open, props.contact], reset, { deep:true })
const fieldError = (name) => errors.value?.[name]?.[0] ?? ''
const submit = async () => {
  saving.value=true; errors.value={}
  try {
    const response = isEdit.value ? await api.patch(`/prospects/${props.prospectId}/contacts/${props.contact.id}`, form) : await api.post(`/prospects/${props.prospectId}/contacts`, form)
    toast.success(response.data.message || 'Contacto guardado.'); emit('saved', response.data.data); emit('close')
  } catch(error){ errors.value=getValidationErrors(error); if(!Object.keys(errors.value).length) toast.error(getApiMessage(error,'No fue posible guardar el contacto.')) } finally { saving.value=false }
}
</script>
<template>
  <BaseModal :open="open" :title="isEdit ? 'Editar contacto' : 'Agregar contacto'" width="620px" @close="emit('close')">
    <form id="contact-form" class="form-grid" @submit.prevent="submit">
      <div class="field"><label>Tipo *</label><select v-model="form.type" class="select"><option value="whatsapp">WhatsApp</option><option value="phone">Teléfono</option><option value="email">Email</option><option value="facebook">Facebook</option><option value="instagram">Instagram</option><option value="website">Sitio web</option></select><span v-if="fieldError('type')" class="field-error">{{ fieldError('type') }}</span></div>
      <div class="field"><label>Etiqueta</label><input v-model="form.label" class="input" placeholder="Ventas, recepción..." /></div>
      <div class="field span-2"><label>Valor *</label><input v-model="form.value" class="input" placeholder="Teléfono, correo o URL" /><span v-if="fieldError('value')" class="field-error">{{ fieldError('value') }}</span></div>
      <div class="check-row span-2"><label><input v-model="form.is_primary" type="checkbox" /> Contacto principal de este tipo</label><label><input v-model="form.is_verified" type="checkbox" /> Verificado</label></div>
      <div class="field span-2"><label>URL de origen</label><input v-model="form.source_url" class="input" type="url" placeholder="https://..." /><span v-if="fieldError('source_url')" class="field-error">{{ fieldError('source_url') }}</span></div>
      <div class="field span-2"><label>Notas</label><textarea v-model="form.notes" class="textarea" /></div>
    </form>
    <template #footer><button class="btn btn-secondary" type="button" @click="emit('close')">Cancelar</button><button class="btn btn-primary" type="submit" form="contact-form" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar contacto' }}</button></template>
  </BaseModal>
</template>
<style scoped>.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.span-2{grid-column:span 2}.check-row{display:flex;gap:22px;flex-wrap:wrap;padding:4px 0}.check-row label{display:flex;align-items:center;gap:7px;color:var(--text-2);font-size:12px}@media(max-width:600px){.form-grid{grid-template-columns:1fr}.span-2{grid-column:span 1}}</style>
