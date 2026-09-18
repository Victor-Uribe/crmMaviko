# MAVIKO CRM — Frontend Vue 3

Frontend completo para el backend Laravel que se construyó durante la sesión.

## Enfoque visual

El diseño toma como referencia la captura compartida: interfaz SaaS clara, sidebar compacto, topbar ligera, tarjetas blancas, bordes suaves, alta densidad de información y muy poco ruido visual. Se adaptó a la identidad de MAVIKO usando navy + cyan.

## Incluye

- Dashboard conectado a `/api/dashboard`.
- Listado de prospectos con búsqueda, filtros y paginación.
- Alta y edición de prospectos.
- Detalle completo del prospecto.
- CRUD visual de contactos.
- CRUD visual de oportunidades.
- CRUD visual de seguimientos.
- Acción rápida para completar seguimientos.
- Agenda global: hoy, vencidos y próximos 7 días.
- Clientes usando prospectos con `stage=won` mientras no exista una entidad `clients` independiente.
- Papelera y restauración de prospectos.
- Formatters globales (`$formatDateTime`, `$formatCurrency`, etc.).
- Toasts, modales, confirmaciones, estados vacíos y loading.
- Responsive para escritorio, tablet y móvil.

## Cómo integrarlo en tu proyecto actual

Tu proyecto ya fue creado con `create-vue`, Router y Pinia, así que no necesitas volver a crear Vue.

1. Haz respaldo de tu `frontend/src` actual.
2. Copia la carpeta `src` de este paquete sobre tu `frontend/src`.
3. Copia `vite.config.js` si quieres usar el alias `@`.
4. Copia `.env.example` como `.env`.
5. Instala las dos dependencias adicionales:

```bash
npm install axios lucide-vue-next
```

Si por alguna razón tu proyecto no tiene Router o Pinia:

```bash
npm install vue-router pinia
```

6. Revisa `.env`:

```env
VITE_API_URL=http://127.0.0.1:8000/api
```

7. Ejecuta:

```bash
npm run dev
```

## Backend esperado

Este frontend utiliza estos endpoints ya construidos:

```text
GET    /api/dashboard
GET    /api/services

GET    /api/prospects
POST   /api/prospects
GET    /api/prospects/trash
PATCH  /api/prospects/{id}/restore
GET    /api/prospects/{prospect}
PATCH  /api/prospects/{prospect}
DELETE /api/prospects/{prospect}

GET    /api/prospects/{prospect}/contacts
POST   /api/prospects/{prospect}/contacts
PATCH  /api/prospects/{prospect}/contacts/{contactId}
DELETE /api/prospects/{prospect}/contacts/{contactId}

GET    /api/prospects/{prospect}/opportunities
POST   /api/prospects/{prospect}/opportunities
PATCH  /api/prospects/{prospect}/opportunities/{opportunityId}
DELETE /api/prospects/{prospect}/opportunities/{opportunityId}

GET    /api/prospects/{prospect}/followups
POST   /api/prospects/{prospect}/followups
PATCH  /api/prospects/{prospect}/followups/{followupId}
DELETE /api/prospects/{prospect}/followups/{followupId}

GET    /api/followups/today
GET    /api/followups/overdue
GET    /api/followups/upcoming?days=7
```

## Estructura principal

```text
src/
├── assets/
│   └── main.css
├── components/
│   ├── contacts/
│   ├── dashboard/
│   ├── followups/
│   ├── layout/
│   ├── opportunities/
│   ├── prospects/
│   └── ui/
├── plugins/
│   └── formatters.js
├── router/
│   └── index.js
├── services/
│   └── api.js
├── stores/
│   └── toast.js
├── utils/
│   └── formatters.js
├── views/
│   ├── DashboardView.vue
│   ├── ProspectsView.vue
│   ├── ProspectDetailView.vue
│   ├── FollowupsView.vue
│   ├── ClientsView.vue
│   ├── TrashView.vue
│   └── NotFoundView.vue
├── App.vue
└── main.js
```

## Decisiones importantes

- `DELETE` en oportunidades/seguimientos está disponible para registros creados por error. Comercialmente conviene usar `lost`, `cancelled`, etc. para conservar historial.
- `Clientes` es una vista de prospectos ganados. Cuando el backend tenga una tabla `clients`, esta pantalla se conecta a esa nueva API.
- Los formatters son globales. En cualquier template puedes usar:

```vue
{{ $formatDateTime(valor) }}
{{ $formatDate(valor) }}
{{ $formatTime(valor) }}
{{ $formatCurrency(valor) }}
{{ $formatPhone(valor) }}
{{ $formatProspectStage(valor) }}
```

- La URL del backend no está hardcodeada en los componentes; vive en `VITE_API_URL`.

## Actualización v2: seguridad y contacto rápido

Esta versión agrega:

- Login protegido con Laravel Sanctum.
- Guardas de Vue Router para impedir acceso anónimo al CRM.
- Menú del usuario con cerrar sesión.
- Nuevo embudo visual y navegable de prospectos.
- Panel de contacto rápido en el detalle del prospecto.
- WhatsApp con mensaje precargado, correo con asunto/cuerpo, llamada y copiar mensaje.
- Documentación del flujo de importación diaria de prospectos.

Configura `.env` del frontend:

```env
VITE_BACKEND_URL=http://127.0.0.1:8000
VITE_API_URL=http://127.0.0.1:8000/api
```

Antes de probar el login aplica `BACKEND_AUTH_SETUP.md` en Laravel.

El diseño de importación está documentado en `PROSPECT_IMPORT_PLAN.md` y hay un ejemplo en `prospect-import-example.json`.

## Actualización v3: bandeja automática de prospección

Se agrega el flujo que conecta la integración privada con la operación diaria del CRM.

Nueva pantalla:

```text
/prospecting/inbox
```

Endpoints requeridos:

```text
GET  /api/prospect-import-items/pending
POST /api/prospect-import-items/{prospectImportItem}/approve
POST /api/prospect-import-items/{prospectImportItem}/reject
```

El sidebar muestra un contador de pendientes y la pantalla permite aprobar/rechazar prospectos, revisar contactos, oportunidades, score y mensaje sugerido antes de crear el prospecto definitivo.

También se reemplazó el embudo trapezoidal por una visualización de conversión más limpia y legible, con barras proporcionales, porcentaje por etapa y navegación al listado filtrado.

Consulta `FRONTEND_V3_CHANGES.md` para el detalle de esta actualización.
