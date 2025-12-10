# API de Especialistas Disponibles

## Endpoint

```
GET /api/especialistas/disponibles
```

## Descripción

Obtiene la lista de especialistas disponibles para un servicio específico en una fecha determinada, incluyendo las horas libres de cada especialista.

## Parámetros Query String

| Parámetro  | Tipo    | Requerido | Descripción                 |
| ---------- | ------- | --------- | --------------------------- |
| `servicio` | integer | Sí        | ID del servicio             |
| `fecha`    | string  | Sí        | Fecha en formato YYYY-MM-DD |

## Ejemplo de Petición

```bash
GET /api/especialistas/disponibles?servicio=1&fecha=2024-12-10
```

```javascript
const response = await fetch("/api/especialistas/disponibles?servicio=1&fecha=2024-12-10");
const especialistas = await response.json();
```

## Respuesta Exitosa (200 OK)

```json
[
  {
    "id_especialista": 1,
    "nombre": "Juan",
    "apellidos": "Pérez García",
    "descripcion": "Especialista en cortes modernos con 10 años de experiencia",
    "foto_url": "/uploads/especialistas/juan.jpg",
    "horas_disponibles": [
      "09:00",
      "09:30",
      "10:00",
      "10:30",
      "11:00",
      "14:00",
      "14:30",
      "15:00",
      "15:30",
      "16:00"
    ]
  },
  {
    "id_especialista": 2,
    "nombre": "María",
    "apellidos": "López Sánchez",
    "descripcion": "Experta en coloración y tratamientos capilares",
    "foto_url": "/uploads/especialistas/maria.jpg",
    "horas_disponibles": ["09:00", "11:00", "11:30", "12:00", "16:00", "16:30", "17:00"]
  }
]
```

## Respuestas de Error

### 400 Bad Request - Parámetros faltantes

```json
{
  "error": "Parámetros requeridos: servicio y fecha"
}
```

### 400 Bad Request - Formato de fecha inválido

```json
{
  "error": "Formato de fecha inválido. Use Y-m-d (ejemplo: 2024-12-09)"
}
```

### 500 Internal Server Error

```json
{
  "error": "Error al obtener especialistas disponibles"
}
```

## Lógica de Disponibilidad

### Horario de Trabajo

- **Inicio**: 09:00
- **Fin**: 20:00
- **Intervalos**: 30 minutos

### Cálculo de Horas Disponibles

1. Se generan slots de tiempo cada 30 minutos desde las 09:00 hasta las 20:00
2. Para cada slot, se verifica que:
   - El servicio completo quepa antes del cierre (20:00)
   - No haya solapamiento con reservas existentes
3. Solo se incluyen slots que cumplan ambas condiciones

### Ejemplo de Cálculo

Si un servicio dura 60 minutos:

- Slot 09:00 → Servicio de 09:00 a 10:00 ✅
- Slot 19:30 → Servicio de 19:30 a 20:30 ❌ (se pasa del horario)

Si hay una reserva de 10:00 a 11:00:

- Slot 09:30 → Servicio de 09:30 a 10:30 ❌ (solapa con reserva)
- Slot 10:00 → Servicio de 10:00 a 11:00 ❌ (solapa con reserva)
- Slot 11:00 → Servicio de 11:00 a 12:00 ✅

## Notas Técnicas

- Solo se muestran especialistas que:
  - Están asignados al servicio solicitado (tabla `especialista_servicio`)
  - Tienen su cuenta de usuario activa
- Las reservas canceladas no bloquean horarios
- Los horarios se devuelven en formato 24 horas (HH:mm)
- La duración del servicio se obtiene automáticamente de la base de datos

## Uso en Frontend

Ver el archivo `/public/js/user/bookings/api/especialistas.js` para un ejemplo de implementación en JavaScript/Preact.
