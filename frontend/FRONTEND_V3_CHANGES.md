# MAVIKO CRM — Actualización Frontend v3

## 1. Nuevo módulo: Prospectos por revisar

Ruta Vue:

```text
/prospecting/inbox
```

Se agregó al sidebar en una sección independiente **Prospección** y muestra el contador de prospectos pendientes.

La vista consume:

```text
GET  /api/prospect-import-items/pending
POST /api/prospect-import-items/{id}/approve
POST /api/prospect-import-items/{id}/reject
```

Funciones incluidas:

- contador de pendientes;
- selección individual;
- seleccionar todos los visibles;
- aprobación individual;
- aprobación múltiple desde el frontend;
- rechazo con confirmación;
- score visual;
- ciudad/estado y fuente;
- contactos encontrados;
- oportunidades detectadas;
- vista previa del mensaje de WhatsApp;
- detalle del payload recibido;
- paginación.

Al aprobar, el backend es responsable de crear `prospects`, `prospect_contacts` y `prospect_opportunities`.

## 2. Nuevo embudo

Se eliminó el embudo trapezoidal porque ocupaba demasiado espacio y dificultaba leer los datos.

La nueva versión usa una visualización de conversión vertical tipo SaaS:

- una etapa por fila;
- indicador numérico de etapa;
- barra proporcional respecto a la base;
- total de registros;
- porcentaje respecto a la etapa previa;
- clic en la fila para abrir `/prospects?stage=...`.

Esto prioriza lectura y comparación antes que una forma decorativa de embudo.

## 3. Archivos nuevos

```text
src/views/ProspectInboxView.vue
src/stores/prospectInbox.js
FRONTEND_V3_CHANGES.md
package.json
```

## 4. Archivos modificados

```text
src/components/dashboard/ProspectStages.vue
src/components/layout/AppSidebar.vue
src/router/index.js
README.md
```

## 5. Prueba rápida

Con Laravel y Vue activos, envía primero un lote a:

```text
POST /api/integrations/prospects
```

Después abre:

```text
http://localhost:5173/prospecting/inbox
```

El prospecto debe aparecer en la bandeja. Al aprobarlo debe desaparecer de la bandeja y aparecer en `/prospects`.
