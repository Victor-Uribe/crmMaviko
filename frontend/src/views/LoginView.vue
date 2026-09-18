<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { LockKeyhole, Mail, ArrowRight, ShieldCheck, Sparkles } from 'lucide-vue-next'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const error = ref('')
const form = reactive({ email: '', password: '', remember: true })

const submit = async () => {
  error.value = ''
  try {
    await auth.login(form)
    router.replace(String(route.query.redirect || '/dashboard'))
  } catch (exception) {
    error.value = exception?.response?.data?.message || 'Correo o contraseña incorrectos.'
  }
}
</script>

<template>
  <main class="login-page">
    <section class="brand-panel">
      <div class="brand-lockup">
        <span class="brand-mark"><Sparkles :size="22" /></span>
        <div><strong>MAVIKO</strong><span>CRM</span></div>
      </div>

      <div class="brand-copy">
        <span class="eyebrow">Prospección comercial</span>
        <h1>Convierte oportunidades en clientes sin perder el seguimiento.</h1>
        <p>Centraliza prospectos, contactos, oportunidades, mensajes y próximas acciones en un solo lugar.</p>
      </div>

      <div class="security-note"><ShieldCheck :size="18" /><span>Acceso privado para el equipo MAVIKO.</span></div>
    </section>

    <section class="form-panel">
      <form class="login-card" @submit.prevent="submit">
        <div class="mobile-brand"><span>M</span><strong>MAVIKO CRM</strong></div>
        <div class="form-head"><span>Bienvenido</span><h2>Inicia sesión</h2><p>Usa tu cuenta para acceder a la información comercial.</p></div>

        <div v-if="error" class="login-error">{{ error }}</div>

        <label class="login-field">
          <span>Correo electrónico</span>
          <div><Mail :size="16" /><input v-model.trim="form.email" type="email" autocomplete="email" required placeholder="tu@correo.com" /></div>
        </label>

        <label class="login-field">
          <span>Contraseña</span>
          <div><LockKeyhole :size="16" /><input v-model="form.password" type="password" autocomplete="current-password" required placeholder="••••••••" /></div>
        </label>

        <label class="remember"><input v-model="form.remember" type="checkbox" /> <span>Mantener sesión iniciada</span></label>

        <button class="login-button" type="submit" :disabled="auth.loading">
          <span>{{ auth.loading ? 'Ingresando...' : 'Ingresar al CRM' }}</span><ArrowRight :size="16" />
        </button>
      </form>
    </section>
  </main>
</template>

<style scoped>
.login-page{min-height:100vh;display:grid;grid-template-columns:minmax(420px,.9fr) minmax(480px,1.1fr);background:#f6f8fb}.brand-panel{display:flex;flex-direction:column;padding:46px 54px;background:radial-gradient(circle at 18% 12%,rgba(0,178,217,.24),transparent 28%),linear-gradient(145deg,#07111f,#0b1d34 62%,#0f2a4a);color:#fff}.brand-lockup{display:flex;align-items:center;gap:11px}.brand-mark{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;background:linear-gradient(145deg,#00b2d9,#38d9f2);color:#07111f}.brand-lockup>div{display:flex;align-items:baseline;gap:7px}.brand-lockup strong{font-size:20px;letter-spacing:.04em}.brand-lockup>div span{color:#38d9f2;font-size:11px;font-weight:800;letter-spacing:.12em}.brand-copy{max-width:560px;margin:auto 0}.eyebrow{display:inline-block;margin-bottom:16px;color:#38d9f2;font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.brand-copy h1{margin:0;font-size:38px;line-height:1.12;letter-spacing:-.035em}.brand-copy p{max-width:500px;margin:19px 0 0;color:#c9d7e7;font-size:14px;line-height:1.7}.security-note{display:flex;align-items:center;gap:9px;color:#aebfd2;font-size:11px}.form-panel{display:grid;place-items:center;padding:36px}.login-card{width:min(430px,100%);padding:34px;border:1px solid var(--border);border-radius:18px;background:#fff;box-shadow:0 18px 60px rgba(7,17,31,.08)}.mobile-brand{display:none}.form-head>span{color:var(--primary);font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em}.form-head h2{margin:8px 0 0;color:var(--navy);font-size:27px}.form-head p{margin:7px 0 26px;color:var(--muted);font-size:12px;line-height:1.55}.login-error{margin-bottom:16px;padding:10px 12px;border:1px solid #ffd3dc;border-radius:9px;background:#fff3f5;color:#b4233d;font-size:11px}.login-field{display:block;margin-top:15px}.login-field>span{display:block;margin-bottom:7px;color:var(--text-2);font-size:11px;font-weight:700}.login-field>div{height:44px;display:flex;align-items:center;gap:9px;padding:0 12px;border:1px solid #d8e1ea;border-radius:9px;color:#8a98a9}.login-field>div:focus-within{border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,178,217,.09)}.login-field input{width:100%;border:0;outline:0;background:transparent;color:var(--text);font-size:12px}.remember{display:flex;align-items:center;gap:7px;margin:18px 0;color:var(--muted);font-size:11px}.remember input{accent-color:var(--primary)}.login-button{width:100%;height:44px;display:flex;align-items:center;justify-content:center;gap:9px;border:0;border-radius:9px;background:var(--navy-2);color:#fff;font-size:12px;font-weight:750;cursor:pointer}.login-button:hover{background:#0f2a4a}.login-button:disabled{opacity:.6;cursor:not-allowed}@media(max-width:900px){.login-page{grid-template-columns:1fr}.brand-panel{display:none}.form-panel{padding:20px}.mobile-brand{display:flex;align-items:center;gap:8px;margin-bottom:28px;color:var(--navy);font-size:12px}.mobile-brand span{width:30px;height:30px;display:grid;place-items:center;border-radius:8px;background:var(--primary);color:#fff;font-weight:800}.login-card{padding:28px}}
</style>
