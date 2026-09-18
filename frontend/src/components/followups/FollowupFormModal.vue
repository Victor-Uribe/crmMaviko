<script setup>
import { computed, reactive, ref, watch } from 'vue'
import BaseModal from '../ui/BaseModal.vue'
import api, { getApiMessage, getValidationErrors } from '../../services/api'
import { toDateTimeLocal } from '../../utils/formatters'
import { useToastStore } from '../../stores/toast'

const props=defineProps({open:Boolean,prospectId:{type:[Number,String],required:true},followup:{type:Object,default:null},contacts:{type:Array,default:()=>[]},opportunities:{type:Array,default:()=>[]}})
const emit=defineEmits(['close','saved']);const toast=useToastStore();const saving=ref(false);const errors=ref({});const isEdit=computed(()=>Boolean(props.followup?.id))
const blank=()=>({prospect_contact_id:'',prospect_opportunity_id:'',type:'whatsapp',title:'',notes:'',priority:'medium',status:'pending',scheduled_at:'',outcome:''});const form=reactive(blank())
const reset=()=>{Object.assign(form,blank(),props.followup?{prospect_contact_id:props.followup.prospect_contact_id??'',prospect_opportunity_id:props.followup.prospect_opportunity_id??'',type:props.followup.type??'whatsapp',title:props.followup.title??'',notes:props.followup.notes??'',priority:props.followup.priority??'medium',status:props.followup.status??'pending',scheduled_at:toDateTimeLocal(props.followup.scheduled_at),outcome:props.followup.outcome??''}:{});errors.value={}}
watch(()=>[props.open,props.followup],reset,{deep:true});const fieldError=(n)=>errors.value?.[n]?.[0]??''
const submit=async()=>{saving.value=true;errors.value={};try{const payload={...form,prospect_contact_id:form.prospect_contact_id?Number(form.prospect_contact_id):null,prospect_opportunity_id:form.prospect_opportunity_id?Number(form.prospect_opportunity_id):null,outcome:form.outcome||null};const response=isEdit.value?await api.patch(`/prospects/${props.prospectId}/followups/${props.followup.id}`,payload):await api.post(`/prospects/${props.prospectId}/followups`,payload);toast.success(response.data.message||'Seguimiento guardado.');emit('saved',response.data.data);emit('close')}catch(error){errors.value=getValidationErrors(error);if(!Object.keys(errors.value).length)toast.error(getApiMessage(error,'No fue posible guardar el seguimiento.'))}finally{saving.value=false}}
</script>
<template>
<BaseModal :open="open" :title="isEdit?'Editar seguimiento':'Programar seguimiento'" width="720px" @close="emit('close')">
<form id="followup-form" class="form-grid" @submit.prevent="submit">
<div class="field"><label>Tipo *</label><select v-model="form.type" class="select"><option value="whatsapp">WhatsApp</option><option value="phone">Llamada</option><option value="email">Email</option><option value="meeting">Reunión</option><option value="task">Tarea</option></select></div>
<div class="field"><label>Fecha y hora *</label><input v-model="form.scheduled_at" class="input" type="datetime-local" /><span v-if="fieldError('scheduled_at')" class="field-error">{{ fieldError('scheduled_at') }}</span></div>
<div class="field span-2"><label>Título *</label><input v-model="form.title" class="input" placeholder="Enviar propuesta, llamar para seguimiento..." /><span v-if="fieldError('title')" class="field-error">{{ fieldError('title') }}</span></div>
<div class="field"><label>Prioridad</label><select v-model="form.priority" class="select"><option value="low">Baja</option><option value="medium">Media</option><option value="high">Alta</option></select></div>
<div class="field"><label>Estado</label><select v-model="form.status" class="select"><option value="pending">Pendiente</option><option value="completed">Completado</option><option value="cancelled">Cancelado</option></select></div>
<div class="field"><label>Contacto relacionado</label><select v-model="form.prospect_contact_id" class="select"><option value="">Sin contacto</option><option v-for="contact in contacts" :key="contact.id" :value="contact.id">{{ $formatContactType(contact.type) }} · {{ contact.label || contact.value }}</option></select></div>
<div class="field"><label>Oportunidad relacionada</label><select v-model="form.prospect_opportunity_id" class="select"><option value="">Sin oportunidad</option><option v-for="opportunity in opportunities" :key="opportunity.id" :value="opportunity.id">{{ opportunity.service?.name || `#${opportunity.id}` }}</option></select></div>
<div class="field span-2"><label>Resultado</label><select v-model="form.outcome" class="select"><option value="">Sin resultado</option><option value="responded">Respondió</option><option value="no_response">Sin respuesta</option><option value="interested">Interesado</option><option value="not_interested">No interesado</option><option value="quoted">Cotizado</option><option value="rescheduled">Reprogramado</option></select></div>
<div class="field span-2"><label>Notas</label><textarea v-model="form.notes" class="textarea" /></div>
</form>
<template #footer><button class="btn btn-secondary" type="button" @click="emit('close')">Cancelar</button><button class="btn btn-primary" type="submit" form="followup-form" :disabled="saving">{{ saving?'Guardando...':'Guardar seguimiento' }}</button></template>
</BaseModal>
</template>
<style scoped>.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.span-2{grid-column:span 2}@media(max-width:600px){.form-grid{grid-template-columns:1fr}.span-2{grid-column:span 1}}</style>
