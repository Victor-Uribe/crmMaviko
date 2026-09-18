# MAVIKO CRM — Flujo recomendado para prospectos generados por ChatGPT

## Fase inicial: revisión + importación de JSON

Mientras el CRM está en local, ChatGPT no puede escribir directamente en `127.0.0.1` de tu Mac. El flujo más seguro y simple es:

1. La búsqueda de prospectos produce un lote diario.
2. El lote se entrega como JSON con un formato fijo.
3. El CRM tendrá una pantalla **Importar prospectos**.
4. Se pega o se sube el JSON.
5. Laravel muestra una vista previa y detecta posibles duplicados.
6. El usuario confirma los registros que desea importar.
7. Laravel crea prospecto, contactos, oportunidades y mensajes sugeridos.

Esto reduce la operación diaria a revisar y pulsar **Importar**.

## Formato propuesto

```json
{
  "batch_id": "maviko-2026-09-18",
  "generated_at": "2026-09-18T08:00:00-06:00",
  "source": "chatgpt-prospecting",
  "prospects": [
    {
      "business_name": "Ejemplo Clínica Sur",
      "category": "Clínica privada",
      "description": "Clínica con presencia activa en redes y sin sitio web propio visible.",
      "country": "México",
      "state": "Ciudad de México",
      "city": "Tlalpan",
      "address": null,
      "source": "Google Maps",
      "source_url": "https://ejemplo.com/fuente",
      "opportunity": "Página web con contacto, servicios y captación por WhatsApp.",
      "quality_score": 86,
      "contacts": [
        {
          "type": "whatsapp",
          "label": "Ventas",
          "value": "+52 55 0000 0000",
          "is_primary": true,
          "source_url": "https://ejemplo.com/fuente"
        }
      ],
      "opportunities": [
        {
          "service_code": "pagina_web",
          "priority": "high",
          "estimated_amount": 3000,
          "notes": "No se encontró sitio web propio actualizado."
        }
      ],
      "outreach": {
        "whatsapp": "Hola, ¿qué tal? Soy Víctor de MAVIKO Digital...",
        "email_subject": "Propuesta para Ejemplo Clínica Sur | MAVIKO Digital",
        "email_body": "Hola, equipo de Ejemplo Clínica Sur...",
        "phone_script": "Hola, llamo de MAVIKO Digital..."
      }
    }
  ]
}
```

## Duplicados

No se debe importar automáticamente solo por nombre. La revisión debe comparar, como mínimo:

- nombre normalizado + ciudad;
- WhatsApp/teléfono normalizado;
- correo;
- dominio/sitio web;
- URL de fuente.

El resultado de la vista previa debe clasificar cada registro como `new`, `possible_duplicate` o `existing`.

## Cuando el CRM esté publicado

Se puede evolucionar a un endpoint protegido de ingestión, por ejemplo `POST /api/prospect-imports`, usando un token específico y revocable. No debe exponerse un endpoint anónimo. Aun así, conviene conservar la vista previa antes de insertar prospectos porque una fuente externa puede contener duplicados o datos deficientes.

## Mensajes

El JSON ya puede incluir mensajes sugeridos por canal. A mediano plazo conviene guardarlos en una tabla `prospect_messages` con:

- `prospect_id`
- `prospect_opportunity_id` nullable
- `prospect_contact_id` nullable
- `channel` (`whatsapp`, `email`, `phone`)
- `subject` nullable
- `message`
- `status` (`draft`, `used`, `discarded`)
- `used_at` nullable

El frontend puede abrir WhatsApp, correo o llamada en un clic sin enviar mensajes masivos automáticamente.
