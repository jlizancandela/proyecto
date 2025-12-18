# Ejemplos de Consumo Directo de Store

## Componentes que Pueden Consumir Directamente de la Store

Este documento muestra ejemplos de cómo componentes como `ResumenCita` o `StatusAlert` podrían consumir directamente de la store si fuera necesario, eliminando aún más el prop drilling.

---

## Ejemplo 1: ResumenCita con Acceso Directo a la Store

### Versión Actual (Con Props)

```javascript
// En ConfirmationForm.js
<${ResumenCita}
  selectedService=${selectedService}
  selectedEspecialista=${selectedEspecialista}
  dia=${dia}
  selectedHora=${selectedHora}
/>
```

### Versión Alternativa (Acceso Directo a Store)

```javascript
/**
 * ResumenCita.js - Versión con acceso directo a la store
 */
import { h } from "https://esm.sh/preact@10.19.3";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { $bookingDraft } from "../context/bookingsStore.js";
import { ResumenItem } from "./ResumenItem.js";
import { formatearFecha } from "../tools/formatters.js";

const html = htm.bind(h);

export const ResumenCita = () => {
  // Consumir directamente desde la store
  const booking = useStore($bookingDraft);

  if (!booking.service || !booking.especialista) {
    return html`
      <div class="alert alert-warning">No hay datos de reserva disponibles</div>
    `;
  }

  return html`
    <div class="card border-0 shadow-sm rounded-4 p-4">
      <h5 class="fw-bold mb-4" style="color: #2d3748;">Resumen de tu Cita</h5>

      <${ResumenItem} icon="scissors" label="Servicio" value=${booking.service.nombre} />

      <${ResumenItem}
        icon="person-fill"
        label="Especialista"
        value=${booking.especialista.nombre_completo}
      />

      <${ResumenItem} icon="calendar-event" label="Fecha" value=${formatearFecha(booking.dia)} />

      <${ResumenItem} icon="clock" label="Hora" value=${booking.hora} />

      <${ResumenItem}
        icon="hourglass-split"
        label="Duración"
        value="${booking.service.duracion} minutos"
      />
    </div>
  `;
};
```

**Ventajas:**

- ✅ Elimina completamente el prop drilling
- ✅ El componente se auto-actualiza cuando cambia la store
- ✅ Menos código en el componente padre

**Desventajas:**

- ⚠️ El componente está acoplado a la estructura de la store
- ⚠️ Más difícil de testear en aislamiento
- ⚠️ Menos reutilizable en otros contextos

---

## Ejemplo 2: StatusAlert con Acceso Directo a la Store

### Versión Actual (Con Props)

```javascript
// En ConfirmationForm.js
<${StatusAlert} loading=${loading} error=${error} />
```

### Versión Alternativa (Acceso Directo a Store)

```javascript
/**
 * StatusAlert.js - Versión con acceso directo a la store
 */
import { h } from "https://esm.sh/preact@10.19.3";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { $uiState } from "../context/bookingsStore.js";

const html = htm.bind(h);

export const StatusAlert = () => {
  // Consumir directamente desde la store
  const uiState = useStore($uiState);

  if (uiState.loading) {
    return html`
      <div class="alert alert-info d-flex align-items-center" role="alert">
        <div class="spinner-border spinner-border-sm me-2" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <div>Procesando tu reserva...</div>
      </div>
    `;
  }

  if (uiState.error) {
    return html`
      <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        ${uiState.error}
      </div>
    `;
  }

  return null;
};
```

**Ventajas:**

- ✅ Componente completamente autónomo
- ✅ Se actualiza automáticamente con los estados globales
- ✅ Puede usarse en cualquier parte de la app sin pasar props

**Desventajas:**

- ⚠️ Solo funciona con estados globales de UI
- ⚠️ No puede reutilizarse para mostrar estados locales

---

## Ejemplo 3: Componente Híbrido (Recomendado)

La mejor práctica es crear componentes que puedan funcionar de ambas formas:

```javascript
/**
 * StatusAlert.js - Versión híbrida (recomendada)
 */
import { h } from "https://esm.sh/preact@10.19.3";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { $uiState } from "../context/bookingsStore.js";

const html = htm.bind(h);

/**
 * StatusAlert puede recibir props o consumir de la store
 * Si se pasan props, los usa. Si no, consume de la store.
 */
export const StatusAlert = ({ loading: propLoading, error: propError } = {}) => {
  const uiState = useStore($uiState);

  // Usar props si están disponibles, sino usar store
  const loading = propLoading !== undefined ? propLoading : uiState.loading;
  const error = propError !== undefined ? propError : uiState.error;

  if (loading) {
    return html`
      <div class="alert alert-info d-flex align-items-center" role="alert">
        <div class="spinner-border spinner-border-sm me-2" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <div>Procesando tu reserva...</div>
      </div>
    `;
  }

  if (error) {
    return html`
      <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        ${error}
      </div>
    `;
  }

  return null;
};
```

**Uso:**

```javascript
// Con props (más testeable)
<${StatusAlert} loading=${true} error=${null} />

// Sin props (consume de la store automáticamente)
<${StatusAlert} />
```

---

## Recomendaciones

### Cuándo Consumir Directamente de la Store:

1. **Estados Globales de UI**: Loading, error, notificaciones
2. **Datos de Sesión**: Usuario actual, permisos
3. **Configuración Global**: Tema, idioma, preferencias

### Cuándo Usar Props:

1. **Componentes Reutilizables**: Que se usan en múltiples contextos
2. **Componentes de UI Genéricos**: Botones, modales, cards
3. **Testing**: Cuando necesitas testear el componente en aislamiento

### Patrón Híbrido (Mejor de Ambos Mundos):

- Permite flexibilidad máxima
- Componentes testeables pero también convenientes
- Facilita la migración gradual
