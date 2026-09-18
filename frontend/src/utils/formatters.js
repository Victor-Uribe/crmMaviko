const toDate = (value) => {
  if (!value) return null
  if (value instanceof Date) return value
  if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/.test(value)) {
    return new Date(value.replace(' ', 'T'))
  }
  return new Date(value)
}

export const formatCurrency = (value, currency = 'MXN') => new Intl.NumberFormat('es-MX', {
  style: 'currency',
  currency,
  minimumFractionDigits: 0,
  maximumFractionDigits: 2,
}).format(Number(value ?? 0))

export const formatNumber = (value) => new Intl.NumberFormat('es-MX').format(Number(value ?? 0))

export const formatDate = (value) => {
  const date = toDate(value)
  if (!date || Number.isNaN(date.getTime())) return '—'
  return new Intl.DateTimeFormat('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(date)
}

export const formatTime = (value) => {
  const date = toDate(value)
  if (!date || Number.isNaN(date.getTime())) return '—'
  return new Intl.DateTimeFormat('es-MX', {
    hour: '2-digit',
    minute: '2-digit',
  }).format(date)
}

export const formatDateTime = (value) => {
  const date = toDate(value)
  if (!date || Number.isNaN(date.getTime())) return '—'
  return new Intl.DateTimeFormat('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date)
}

export const formatPhone = (value) => {
  if (!value) return '—'
  const digits = String(value).replace(/\D/g, '')
  if (digits.length === 12 && digits.startsWith('52')) {
    return digits.replace(/52(\d{2})(\d{4})(\d{4})/, '+52 $1 $2 $3')
  }
  if (digits.length === 10) {
    return digits.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')
  }
  return value
}

export const formatProspectStage = (value) => ({
  new: 'Nuevo',
  contacted: 'Contactado',
  interested: 'Interesado',
  quoted: 'Cotizado',
  negotiation: 'Negociación',
  won: 'Ganado',
  lost: 'Perdido',
  discarded: 'Descartado',
}[value] ?? value ?? '—')

export const formatOpportunityStatus = (value) => ({
  detected: 'Detectada',
  offered: 'Ofrecida',
  quoted: 'Cotizada',
  negotiating: 'Negociando',
  won: 'Ganada',
  lost: 'Perdida',
}[value] ?? value ?? '—')

export const formatFollowupStatus = (value) => ({
  pending: 'Pendiente',
  completed: 'Completado',
  cancelled: 'Cancelado',
}[value] ?? value ?? '—')

export const formatPriority = (value) => ({ low: 'Baja', medium: 'Media', high: 'Alta' }[value] ?? value ?? '—')

export const formatContactType = (value) => ({
  whatsapp: 'WhatsApp',
  phone: 'Teléfono',
  email: 'Email',
  facebook: 'Facebook',
  instagram: 'Instagram',
  website: 'Sitio web',
}[value] ?? value ?? '—')

export const toDateTimeLocal = (value) => {
  const date = toDate(value)
  if (!date || Number.isNaN(date.getTime())) return ''
  const pad = (n) => String(n).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}
