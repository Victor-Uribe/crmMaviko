<script setup>
import { computed } from 'vue'
import { MessageCircle, Mail, Phone, Copy, Send, Sparkles } from 'lucide-vue-next'
import { useToastStore } from '../../stores/toast'

const props = defineProps({
  prospect: { type: Object, required: true },
  contacts: { type: Array, default: () => [] },
  opportunities: { type: Array, default: () => [] },
})

const toast = useToastStore()

const primaryOf = (type) => props.contacts.find(x => x.type === type && x.is_primary) || props.contacts.find(x => x.type === type)
const whatsapp = computed(() => primaryOf('whatsapp'))
const email = computed(() => primaryOf('email'))
const phone = computed(() => primaryOf('phone'))

const preferredOpportunity = computed(() => {
  const priority = { high: 3, medium: 2, low: 1 }
  const open = props.opportunities.filter(x => !['won', 'lost'].includes(x.status))
  return [...open].sort((a, b) => (priority[b.priority] || 0) - (priority[a.priority] || 0))[0] || props.opportunities[0] || null
})

const suggestedMessage = computed(() => {
  if (props.prospect?.suggested_message) return props.prospect.suggested_message

  const business = props.prospect?.business_name || 'su negocio'
  const service = preferredOpportunity.value?.service?.name

  if (service) {
    return `Hola, ¿qué tal? Soy Víctor de MAVIKO Digital. Estuve revisando ${business} y creo que podemos apoyarles con ${service}. Trabajamos soluciones para PyMEs con precio claro y soporte incluido. ¿Te puedo compartir una propuesta breve sin compromiso?`
  }

  return `Hola, ¿qué tal? Soy Víctor de MAVIKO Digital. Estuve revisando ${business} y veo oportunidad de apoyarles con una solución digital para mejorar su presencia y operación. ¿Te puedo compartir una propuesta breve sin compromiso?`
})

const subject = computed(() => `Propuesta para ${props.prospect?.business_name || 'su negocio'} | MAVIKO Digital`)
const cleanPhone = (item) => item?.normalized_value || String(item?.value || '').replace(/\D/g, '')
const whatsappUrl = computed(() => whatsapp.value ? `https://wa.me/${cleanPhone(whatsapp.value)}?text=${encodeURIComponent(suggestedMessage.value)}` : '')
const emailUrl = computed(() => email.value ? `mailto:${email.value.value}?subject=${encodeURIComponent(subject.value)}&body=${encodeURIComponent(suggestedMessage.value)}` : '')
const phoneUrl = computed(() => phone.value ? `tel:${cleanPhone(phone.value)}` : '')

const copyMessage = async () => {
  try {
    await navigator.clipboard.writeText(suggestedMessage.value)
    toast.success('Mensaje copiado al portapapeles.')
  } catch {
    toast.error('No fue posible copiar el mensaje.')
  }
}
</script>

<template>
  <section class="card quick-contact">
    <header>
      <div class="title-icon"><Sparkles :size="15" /></div>
      <div><h2>Contacto rápido</h2><p>Mensaje recomendado para el primer acercamiento.</p></div>
    </header>

    <div class="message-box">{{ suggestedMessage }}</div>

    <div class="actions">
      <a v-if="whatsapp" class="action whatsapp" :href="whatsappUrl" target="_blank" rel="noopener">
        <MessageCircle :size="14" /><span>WhatsApp</span>
      </a>
      <a v-if="email" class="action" :href="emailUrl">
        <Mail :size="14" /><span>Correo</span>
      </a>
      <a v-if="phone" class="action" :href="phoneUrl">
        <Phone :size="14" /><span>Llamar</span>
      </a>
      <button class="action" type="button" @click="copyMessage">
        <Copy :size="14" /><span>Copiar</span>
      </button>
    </div>

    <div v-if="!whatsapp && !email && !phone" class="no-contact">
      Agrega WhatsApp, correo o teléfono para habilitar acciones directas.
    </div>

    <footer v-if="preferredOpportunity">
      <Send :size="12" /> Enfocado en: <strong>{{ preferredOpportunity.service?.name || 'oportunidad principal' }}</strong>
    </footer>
  </section>
</template>

<style scoped>
.quick-contact{overflow:hidden;box-shadow:none}.quick-contact>header{display:flex;align-items:center;gap:9px;padding:14px;border-bottom:1px solid var(--border)}.title-icon{width:30px;height:30px;display:grid;place-items:center;border-radius:8px;background:#e9fafd;color:#087e98}.quick-contact h2{margin:0;color:var(--navy);font-size:13px}.quick-contact header p{margin:3px 0 0;color:var(--muted);font-size:9px}.message-box{margin:13px;padding:12px;border:1px solid #dce6ee;border-radius:9px;background:#f9fbfd;color:var(--text-2);font-size:10px;line-height:1.55}.actions{display:grid;grid-template-columns:repeat(2,1fr);gap:7px;padding:0 13px 13px}.action{min-height:34px;display:flex;align-items:center;justify-content:center;gap:6px;border:1px solid var(--border);border-radius:8px;background:#fff;color:var(--text-2);font-size:10px;font-weight:700;text-decoration:none;cursor:pointer}.action:hover{background:#f5f9fb;color:var(--navy)}.action.whatsapp{border-color:#bcead3;background:#f0fbf5;color:#087a4e}.no-contact{margin:0 13px 13px;padding:10px;border-radius:8px;background:#fff7ed;color:#a5550d;font-size:9px;line-height:1.45}.quick-contact footer{display:flex;align-items:center;gap:5px;padding:10px 13px;border-top:1px solid var(--border);color:var(--muted);font-size:9px}.quick-contact footer strong{color:var(--navy)}
</style>
