<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ArrowLeft, Edit3, Trash2, Plus, MapPin, Globe2, Link2, MessageCircle, Mail, Phone, ExternalLink, CheckCircle2, CalendarPlus, Building2 } from 'lucide-vue-next'
import api from '../services/api'
import StatusBadge from '../components/ui/StatusBadge.vue'
import EmptyState from '../components/ui/EmptyState.vue'
import LoadingBlock from '../components/ui/LoadingBlock.vue'
import ConfirmDialog from '../components/ui/ConfirmDialog.vue'
import ProspectFormModal from '../components/prospects/ProspectFormModal.vue'
import ContactFormModal from '../components/contacts/ContactFormModal.vue'
import OpportunityFormModal from '../components/opportunities/OpportunityFormModal.vue'
import FollowupFormModal from '../components/followups/FollowupFormModal.vue'
import QuickContactCard from '../components/prospects/QuickContactCard.vue'
import { useToastStore } from '../stores/toast'

const route=useRoute();const router=useRouter();const toast=useToastStore();const id=computed(()=>route.params.id)
const prospect=ref(null);const contacts=ref([]);const opportunities=ref([]);const followups=ref([]);const services=ref([]);const loading=ref(true)
const prospectModal=ref(false);const contactModal=ref(false);const opportunityModal=ref(false);const followupModal=ref(false)
const editingContact=ref(null);const editingOpportunity=ref(null);const editingFollowup=ref(null)
const confirm=ref({open:false,type:'',id:null,label:''});const deleting=ref(false)

const load=async()=>{loading.value=true;try{const [p,c,o,f,s]=await Promise.all([api.get(`/prospects/${id.value}`),api.get(`/prospects/${id.value}/contacts`),api.get(`/prospects/${id.value}/opportunities`),api.get(`/prospects/${id.value}/followups`),api.get('/services')]);prospect.value=p.data.data;contacts.value=c.data.data??[];opportunities.value=o.data.data??[];followups.value=f.data.data??[];services.value=s.data.data??[]}catch(error){if(error?.response?.status===404){toast.error('El prospecto no existe.');router.replace('/prospects')}else toast.error(error?.response?.data?.message||'No fue posible cargar el prospecto.')}finally{loading.value=false}}
const openContact=(item=null)=>{editingContact.value=item;contactModal.value=true};const openOpportunity=(item=null)=>{editingOpportunity.value=item;opportunityModal.value=true};const openFollowup=(item=null)=>{editingFollowup.value=item;followupModal.value=true}
const upsert=(list,item)=>{const index=list.value.findIndex(x=>x.id===item.id);if(index>=0)list.value.splice(index,1,item);else list.value.unshift(item)}
const askDelete=(type,item)=>{confirm.value={open:true,type,id:item.id,label:item.business_name||item.service?.name||item.title||item.value||`#${item.id}`}}
const doDelete=async()=>{deleting.value=true;try{const c=confirm.value;if(c.type==='prospect'){await api.delete(`/prospects/${id.value}`);toast.success('Prospecto enviado a la papelera.');router.push('/prospects');return}if(c.type==='contact'){await api.delete(`/prospects/${id.value}/contacts/${c.id}`);contacts.value=contacts.value.filter(x=>x.id!==c.id)}if(c.type==='opportunity'){await api.delete(`/prospects/${id.value}/opportunities/${c.id}`);opportunities.value=opportunities.value.filter(x=>x.id!==c.id)}if(c.type==='followup'){await api.delete(`/prospects/${id.value}/followups/${c.id}`);followups.value=followups.value.filter(x=>x.id!==c.id)}toast.success('Registro eliminado correctamente.');confirm.value.open=false}catch(error){toast.error(error?.response?.data?.message||'No fue posible eliminar el registro.')}finally{deleting.value=false}}
const completeFollowup=async(item)=>{try{const response=await api.patch(`/prospects/${id.value}/followups/${item.id}`,{status:'completed'});upsert(followups,response.data.data);toast.success('Seguimiento completado.')}catch(error){toast.error(error?.response?.data?.message||'No fue posible completar el seguimiento.')}}
const contactHref=(contact)=>{if(contact.type==='whatsapp')return `https://wa.me/${contact.normalized_value||String(contact.value).replace(/\D/g,'')}`;if(contact.type==='email')return `mailto:${contact.value}`;if(contact.type==='phone')return `tel:${contact.normalized_value||contact.value}`;if(['website','facebook','instagram'].includes(contact.type))return contact.value;return null}
watch(id,load);onMounted(load)
</script>

<template>
<section class="page detail-page">
<LoadingBlock v-if="loading" />
<template v-else-if="prospect">
  <div class="detail-head">
    <div>
      <button class="back-link" type="button" @click="router.push('/prospects')"><ArrowLeft :size="15" /> Prospectos</button>
      <div class="business-title"><span class="logo-placeholder">{{ prospect.business_name?.charAt(0)?.toUpperCase() }}</span><div><div class="title-line"><h1>{{ prospect.business_name }}</h1><StatusBadge :value="prospect.stage" type="stage" /></div><p>{{ prospect.category || 'Sin categoría' }} <template v-if="prospect.city">· {{ prospect.city }}</template></p></div></div>
    </div>
    <div class="head-actions"><button class="btn btn-secondary" @click="prospectModal=true"><Edit3 :size="14" /> Editar</button><button class="btn btn-danger btn-icon" title="Eliminar" @click="askDelete('prospect',prospect)"><Trash2 :size="15" /></button></div>
  </div>

  <div class="summary-grid">
    <article class="summary-card card"><span>Score</span><strong>{{ prospect.quality_score ?? 0 }}<small>/100</small></strong><div class="score-line"><span :style="{width:`${prospect.quality_score??0}%`}"></span></div></article>
    <article class="summary-card card"><span>Ubicación</span><strong class="summary-text"><MapPin :size="14" />{{ [prospect.city,prospect.state].filter(Boolean).join(', ') || 'Sin ubicación' }}</strong></article>
    <article class="summary-card card"><span>Origen</span><strong class="summary-text"><Globe2 :size="14" />{{ prospect.source || 'Sin origen' }}</strong></article>
    <article class="summary-card card"><span>Oportunidades</span><strong>{{ opportunities.length }}</strong></article>
  </div>

  <div class="detail-grid">
    <div class="detail-main">
      <section class="card section-card">
        <header><div><h2>Información comercial</h2><p>Contexto y oportunidad detectada.</p></div></header>
        <div class="info-body">
          <div v-if="prospect.description"><span>Descripción</span><p>{{ prospect.description }}</p></div>
          <div v-if="prospect.opportunity"><span>Oportunidad detectada</span><p>{{ prospect.opportunity }}</p></div>
          <div v-if="prospect.address"><span>Dirección</span><p>{{ prospect.address }}</p></div>
          <a v-if="prospect.source_url" :href="prospect.source_url" target="_blank" rel="noopener" class="source-link"><Link2 :size="14" /> Ver fuente original <ExternalLink :size="13" /></a>
          <EmptyState v-if="!prospect.description&&!prospect.opportunity&&!prospect.address&&!prospect.source_url" title="Sin información adicional" description="Edita el prospecto para agregar contexto comercial." />
        </div>
      </section>

      <section class="card section-card">
        <header><div><h2>Oportunidades</h2><p>Servicios de MAVIKO que pueden convertirse en venta.</p></div><button class="btn btn-secondary" @click="openOpportunity()"><Plus :size="14" /> Agregar</button></header>
        <EmptyState v-if="!opportunities.length" title="Sin oportunidades" description="Agrega el primer servicio potencial para este prospecto." />
        <div v-else class="record-list">
          <article v-for="item in opportunities" :key="item.id" class="record-row">
            <span class="record-icon opportunity"><Building2 :size="16" /></span>
            <div class="record-content"><div><strong>{{ item.service?.name || 'Servicio' }}</strong><span>{{ item.notes || 'Sin notas' }}</span></div><small>{{ $formatCurrency(item.estimated_amount) }}</small></div>
            <StatusBadge :value="item.priority" type="priority" /><StatusBadge :value="item.status" type="opportunity" />
            <div class="row-actions"><button @click="openOpportunity(item)"><Edit3 :size="14" /></button><button @click="askDelete('opportunity',item)"><Trash2 :size="14" /></button></div>
          </article>
        </div>
      </section>

      <section class="card section-card">
        <header><div><h2>Seguimientos</h2><p>Historial y próximas acciones comerciales.</p></div><button class="btn btn-primary" @click="openFollowup()"><CalendarPlus :size="14" /> Programar</button></header>
        <EmptyState v-if="!followups.length" title="Sin seguimientos" description="Programa la siguiente acción para no perder el prospecto." />
        <div v-else class="record-list">
          <article v-for="item in followups" :key="item.id" class="record-row followup-row">
            <div class="date-tile"><strong>{{ $formatDate(item.scheduled_at) }}</strong><span>{{ $formatTime(item.scheduled_at) }}</span></div>
            <div class="record-content"><div><strong>{{ item.title }}</strong><span>{{ item.type }}<template v-if="item.opportunity?.service"> · {{ item.opportunity.service.name }}</template></span></div></div>
            <StatusBadge :value="item.priority" type="priority" /><StatusBadge :value="item.status" type="followup" />
            <div class="row-actions"><button v-if="item.status==='pending'" title="Completar" @click="completeFollowup(item)"><CheckCircle2 :size="14" /></button><button @click="openFollowup(item)"><Edit3 :size="14" /></button><button @click="askDelete('followup',item)"><Trash2 :size="14" /></button></div>
          </article>
        </div>
      </section>
    </div>

    <aside class="detail-side">
      <QuickContactCard :prospect="prospect" :contacts="contacts" :opportunities="opportunities" />

      <section class="card section-card contacts-card">
        <header><div><h2>Contactos</h2><p>{{ contacts.length }} registrados</p></div><button class="small-add" @click="openContact()"><Plus :size="15" /></button></header>
        <EmptyState v-if="!contacts.length" title="Sin contactos" description="Agrega WhatsApp, correo, teléfono o redes." />
        <div v-else class="contact-list">
          <article v-for="item in contacts" :key="item.id" class="contact-row">
            <span class="contact-icon"><MessageCircle v-if="item.type==='whatsapp'" :size="15"/><Mail v-else-if="item.type==='email'" :size="15"/><Phone v-else-if="item.type==='phone'" :size="15"/><Globe2 v-else :size="15"/></span>
            <div><div><strong>{{ $formatContactType(item.type) }}</strong><span v-if="item.is_primary" class="primary-tag">Principal</span></div><a v-if="contactHref(item)" :href="contactHref(item)" target="_blank" rel="noopener">{{ ['phone','whatsapp'].includes(item.type)?$formatPhone(item.value):item.value }}</a><span v-else>{{ item.value }}</span><small v-if="item.label">{{ item.label }}</small></div>
            <div class="contact-actions"><button @click="openContact(item)"><Edit3 :size="13" /></button><button @click="askDelete('contact',item)"><Trash2 :size="13" /></button></div>
          </article>
        </div>
      </section>

      <section class="card meta-card">
        <h3>Datos del registro</h3><div><span>Creado</span><strong>{{ $formatDateTime(prospect.created_at) }}</strong></div><div><span>Actualizado</span><strong>{{ $formatDateTime(prospect.updated_at) }}</strong></div><div><span>ID interno</span><strong>#{{ prospect.id }}</strong></div>
      </section>
    </aside>
  </div>

  <ProspectFormModal :open="prospectModal" :prospect="prospect" @close="prospectModal=false" @saved="prospect=$event" />
  <ContactFormModal :open="contactModal" :prospect-id="id" :contact="editingContact" @close="contactModal=false" @saved="upsert(contacts,$event)" />
  <OpportunityFormModal :open="opportunityModal" :prospect-id="id" :opportunity="editingOpportunity" :services="services" @close="opportunityModal=false" @saved="upsert(opportunities,$event)" />
  <FollowupFormModal :open="followupModal" :prospect-id="id" :followup="editingFollowup" :contacts="contacts" :opportunities="opportunities" @close="followupModal=false" @saved="upsert(followups,$event)" />
  <ConfirmDialog :open="confirm.open" title="Eliminar registro" :message="`Se eliminará ${confirm.label}. Esta acción debe usarse solo para registros creados por error.`" confirm-text="Eliminar" danger :loading="deleting" @close="confirm.open=false" @confirm="doDelete" />
</template>
</section>
</template>

<style scoped>
.detail-head{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:18px}.back-link{display:inline-flex;align-items:center;gap:6px;margin-bottom:12px;padding:0;border:0;background:transparent;color:var(--muted);font-size:11px;font-weight:650;cursor:pointer}.back-link:hover{color:var(--primary)}.business-title{display:flex;align-items:center;gap:12px}.logo-placeholder{width:46px;height:46px;display:grid;place-items:center;border-radius:12px;background:linear-gradient(145deg,#0b1d34,#00b2d9);color:#fff;font-size:17px;font-weight:800}.title-line{display:flex;align-items:center;gap:9px;flex-wrap:wrap}.title-line h1{margin:0;color:var(--navy);font-size:25px;letter-spacing:-.02em}.business-title p{margin:4px 0 0;color:var(--muted);font-size:11px}.head-actions{display:flex;gap:7px}.summary-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px}.summary-card{min-height:92px;padding:14px;box-shadow:none}.summary-card>span{display:block;margin-bottom:8px;color:var(--muted);font-size:10px}.summary-card>strong{color:var(--navy);font-size:21px}.summary-card small{font-size:10px;color:var(--muted);font-weight:600}.summary-text{display:flex;align-items:center;gap:7px;font-size:12px!important;font-weight:700}.score-line{height:4px;margin-top:9px;border-radius:999px;background:#edf2f6;overflow:hidden}.score-line span{display:block;height:100%;background:var(--primary)}.detail-grid{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:14px}.detail-main{display:flex;flex-direction:column;gap:14px}.section-card{overflow:hidden;box-shadow:none;margin:0}.section-card>header{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;border-bottom:1px solid var(--border)}.section-card h2{margin:0;color:var(--navy);font-size:13px}.section-card header p{margin:3px 0 0;color:var(--muted);font-size:10px}.info-body{padding:16px}.info-body>div+div{margin-top:14px}.info-body span{display:block;margin-bottom:5px;color:var(--muted);font-size:10px;font-weight:700;text-transform:uppercase}.info-body p{margin:0;color:var(--text-2);font-size:12px;line-height:1.65}.source-link{display:inline-flex;align-items:center;gap:6px;margin-top:15px;color:#087e98;font-size:11px;font-weight:700;text-decoration:none}.record-list{display:flex;flex-direction:column}.record-row{display:grid;grid-template-columns:auto minmax(0,1fr) auto auto auto;align-items:center;gap:10px;padding:13px 16px;border-bottom:1px solid var(--border)}.record-row:last-child{border-bottom:0}.record-icon{width:34px;height:34px;display:grid;place-items:center;border-radius:9px}.record-icon.opportunity{background:#eef8fa;color:#087e98}.record-content{display:flex;align-items:center;justify-content:space-between;gap:14px;min-width:0}.record-content>div{display:flex;flex-direction:column;min-width:0}.record-content strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--navy);font-size:11px}.record-content span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:3px;color:var(--muted);font-size:10px}.record-content small{color:var(--navy);font-size:11px;font-weight:750}.row-actions{display:flex;gap:2px}.row-actions button,.contact-actions button{width:29px;height:29px;display:grid;place-items:center;border:0;border-radius:7px;background:transparent;color:#8a97a6;cursor:pointer}.row-actions button:hover,.contact-actions button:hover{background:#f2f6f8;color:var(--navy)}.date-tile{display:flex;flex-direction:column;min-width:86px}.date-tile strong{color:var(--navy);font-size:10px}.date-tile span{margin-top:2px;color:var(--primary);font-size:9px}.followup-row{grid-template-columns:95px minmax(0,1fr) auto auto auto}.detail-side{display:flex;flex-direction:column;gap:14px}.small-add{width:30px;height:30px;display:grid;place-items:center;border:1px solid var(--border);border-radius:8px;background:#fff;cursor:pointer}.contact-list{display:flex;flex-direction:column}.contact-row{display:grid;grid-template-columns:auto 1fr auto;gap:10px;padding:12px 14px;border-bottom:1px solid var(--border)}.contact-row:last-child{border-bottom:0}.contact-icon{width:32px;height:32px;display:grid;place-items:center;border-radius:8px;background:#f3f7fa;color:#6f7d8d}.contact-row>div:nth-child(2){min-width:0;display:flex;flex-direction:column}.contact-row>div:nth-child(2)>div{display:flex;align-items:center;gap:6px}.contact-row strong{color:var(--navy);font-size:10px}.contact-row a,.contact-row>div:nth-child(2)>span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:3px;color:#087e98;font-size:10px;text-decoration:none}.contact-row small{margin-top:3px;color:var(--muted);font-size:9px}.primary-tag{padding:2px 5px;border-radius:999px;background:#e9fafd;color:#087e98!important;font-size:8px!important;font-weight:800}.contact-actions{display:flex;align-items:start}.meta-card{padding:15px;box-shadow:none}.meta-card h3{margin:0 0 12px;color:var(--navy);font-size:12px}.meta-card div{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-top:1px solid var(--border)}.meta-card span{color:var(--muted);font-size:9px}.meta-card strong{color:var(--text-2);font-size:9px;font-weight:650}@media(max-width:1100px){.summary-grid{grid-template-columns:repeat(2,1fr)}.detail-grid{grid-template-columns:1fr}.detail-side{display:grid;grid-template-columns:1fr 1fr}}@media(max-width:720px){.detail-head{align-items:flex-start;flex-direction:column}.summary-grid{grid-template-columns:1fr 1fr}.record-row,.followup-row{grid-template-columns:1fr auto}.record-row>.record-icon,.date-tile,.record-row>.badge:nth-of-type(1){display:none}.record-content{display:block}.detail-side{display:flex}.head-actions{width:100%}.head-actions .btn-secondary{flex:1}}@media(max-width:480px){.summary-grid{grid-template-columns:1fr}}
</style>
