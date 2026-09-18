<script setup>
import { computed, reactive, ref, watch } from 'vue'
import BaseModal from '../ui/BaseModal.vue'
import api, { getApiMessage, getValidationErrors } from '../../services/api'
import { useToastStore } from '../../stores/toast'

const props = defineProps({ open:Boolean, prospectId:{type:[Number,String],required:true}, opportunity:{type:Object,default:null}, services:{type:Array,default:()=>[]} })
const emit=defineEmits(['close','saved']); const toast=useToastStore(); const saving=ref(false); const errors=ref({}); const isEdit=computed(()=>Boolean(props.opportunity?.id))
const blank=()=>({service_id:'',priority:'medium',status:'detected',estimated_amount:'',notes:''}); const form=reactive(blank())
const reset=()=>{Object.assign(form,blank(),props.opportunity?{service_id:props.opportunity.service_id??'',priority:props.opportunity.priority??'medium',status:props.opportunity.status??'detected',estimated_amount:props.opportunity.estimated_amount??'',notes:props.opportunity.notes??''}:{});errors.value={}}
watch(()=>[props.open,props.opportunity],reset,{deep:true}); const fieldError=(n)=>errors.value?.[n]?.[0]??''
const submit=async()=>{saving.value=true;errors.value={};try{const payload={...form,service_id:Number(form.service_id),estimated_amount:form.estimated_amount===''?null:Number(form.estimated_amount)};const response=isEdit.value?await api.patch(`/prospects/${props.prospectId}/opportunities/${props.opportunity.id}`,payload):await api.post(`/prospects/${props.prospectId}/opportunities`,payload);toast.success(response.data.message||'Oportunidad guardada.');emit('saved',response.data.data);emit('close')}catch(error){errors.value=getValidationErrors(error);if(!Object.keys(errors.value).length)toast.error(getApiMessage(error,'No fue posible guardar la oportunidad.'))}finally{saving.value=false}}
</script>
<template>
<BaseModal :open="open" :title="isEdit?'Editar oportunidad':'Agregar oportunidad'" width="620px" @close="emit('close')">
<form id="opp-form" class="form-grid" @submit.prevent="submit">
<div class="field span-2"><label>Servicio *</label><select v-model="form.service_id" class="select" :disabled="isEdit"><option value="" disabled>Selecciona un servicio</option><option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }}</option></select><span v-if="fieldError('service_id')" class="field-error">{{ fieldError('service_id') }}</span></div>
<div class="field"><label>Prioridad</label><select v-model="form.priority" class="select"><option value="low">Baja</option><option value="medium">Media</option><option value="high">Alta</option></select></div>
<div class="field"><label>Estado</label><select v-model="form.status" class="select"><option value="detected">Detectada</option><option value="offered">Ofrecida</option><option value="quoted">Cotizada</option><option value="negotiating">Negociando</option><option value="won">Ganada</option><option value="lost">Perdida</option></select></div>
<div class="field span-2"><label>Monto estimado</label><input v-model="form.estimated_amount" class="input" type="number" min="0" step="0.01" placeholder="3000" /><span v-if="fieldError('estimated_amount')" class="field-error">{{ fieldError('estimated_amount') }}</span></div>
<div class="field span-2"><label>Notas</label><textarea v-model="form.notes" class="textarea" placeholder="Necesidad detectada, alcance, objeciones..." /></div>
</form>
<template #footer><button class="btn btn-secondary" type="button" @click="emit('close')">Cancelar</button><button class="btn btn-primary" type="submit" form="opp-form" :disabled="saving">{{ saving?'Guardando...':'Guardar oportunidad' }}</button></template>
</BaseModal>
</template>
<style scoped>.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.span-2{grid-column:span 2}@media(max-width:600px){.form-grid{grid-template-columns:1fr}.span-2{grid-column:span 1}}</style>
