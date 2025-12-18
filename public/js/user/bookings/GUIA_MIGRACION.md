# Guía de Migración - De bookingsContext.js a bookingsStore.js

## 📚 Introducción

Esta guía te ayudará a entender cómo migrar código que usa la antigua `bookingsContext.js` a la nueva `bookingsStore.js`.

---

## 🔄 Tabla de Equivalencias

### Imports

#### ANTES (bookingsContext.js)

```javascript
import {
  $estado,
  $services,
  $selectedService,
  $dia,
  $mes,
  $especialistas,
  $selectedEspecialista,
  $selectedHora,
  $currentPage,
  $totalEspecialistas,
  $pageSize,
  $userName,
  loadServices,
  selectService,
  loadEspecialistasDisponibles,
  selectEspecialistaYHora,
  resetBooking,
} from "./context/bookingsContext.js";
```

#### DESPUÉS (bookingsStore.js)

```javascript
import {
  // Átomos que se mantienen
  $estado,
  $services,
  $especialistas,
  $userName,
  $totalEspecialistas,

  // Nuevos maps agrupados
  $bookingDraft, // Reemplaza: $selectedService, $dia, $selectedEspecialista, $selectedHora
  $pagination, // Reemplaza: $currentPage, $pageSize, $totalEspecialistas
  $uiState, // Nuevo: loading, error

  // Computed store
  $mes, // Ahora es computed, se actualiza automáticamente

  // Acciones (nombres actualizados)
  loadServicesAction, // Antes: loadServices
  selectServiceAction, // Antes: selectService
  setDiaAction, // Nuevo
  loadEspecialistasAction, // Antes: loadEspecialistasDisponibles
  selectEspecialistaAction, // Antes: selectEspecialistaYHora
  changePageAction, // Nuevo
  goToConfirmationAction, // Nuevo
  confirmReservaAction, // Nuevo (antes estaba en useReservas)
  resetBookingAction, // Antes: resetBooking
} from "./context/bookingsStore.js";
```

---

## 🗺️ Migración de Átomos a Maps

### 1. Datos de Reserva → $bookingDraft

#### ANTES

```javascript
const selectedService = useStore($selectedService);
const dia = useStore($dia);
const selectedEspecialista = useStore($selectedEspecialista);
const selectedHora = useStore($selectedHora);

// Acceder a valores
console.log(selectedService.id);
console.log(dia);
console.log(selectedEspecialista.nombre);
console.log(selectedHora);
```

#### DESPUÉS

```javascript
const booking = useStore($bookingDraft);

// Acceder a valores
console.log(booking.service.id); // Nota: service, no serviceId
console.log(booking.dia);
console.log(booking.especialista.nombre);
console.log(booking.hora);
```

#### Tabla de Mapeo

| Antes                   | Después                      |
| ----------------------- | ---------------------------- |
| `$selectedService`      | `$bookingDraft.service`      |
| N/A                     | `$bookingDraft.serviceId`    |
| `$dia`                  | `$bookingDraft.dia`          |
| `$selectedEspecialista` | `$bookingDraft.especialista` |
| `$selectedHora`         | `$bookingDraft.hora`         |

### 2. Paginación → $pagination

#### ANTES

```javascript
const currentPage = useStore($currentPage);
const pageSize = useStore($pageSize);
const totalEspecialistas = useStore($totalEspecialistas);

// Calcular totalPages
const totalPages = Math.ceil(totalEspecialistas / pageSize);
```

#### DESPUÉS

```javascript
const pagination = useStore($pagination);

// Acceder a valores (totalPages ya calculado)
console.log(pagination.current);
console.log(pagination.pageSize);
console.log(pagination.totalPages); // ✨ Ya calculado en la store
```

#### Tabla de Mapeo

| Antes                | Después                  |
| -------------------- | ------------------------ |
| `$currentPage`       | `$pagination.current`    |
| `$pageSize`          | `$pagination.pageSize`   |
| Calculado localmente | `$pagination.totalPages` |

### 3. Estados de UI → $uiState (NUEVO)

#### ANTES (en useReservas.js)

```javascript
const [loading, setLoading] = useState(false);
const [error, setError] = useState(null);
```

#### DESPUÉS (en bookingsStore.js)

```javascript
const uiState = useStore($uiState);

console.log(uiState.loading);
console.log(uiState.error);
```

---

## 🎬 Migración de Funciones a Acciones

### 1. Cargar Servicios

#### ANTES

```javascript
import { loadServices } from "./context/bookingsContext.js";

useEffect(() => {
  loadServices();
}, []);
```

#### DESPUÉS

```javascript
import { loadServicesAction } from "./context/bookingsStore.js";

useEffect(() => {
  loadServicesAction();
}, []);
```

### 2. Seleccionar Servicio

#### ANTES

```javascript
import { selectService } from "./context/bookingsContext.js";

const handleSelect = (service) => {
  selectService(service);
};
```

#### DESPUÉS

```javascript
import { selectServiceAction } from "./context/bookingsStore.js";

const handleSelect = (service) => {
  selectServiceAction(service);
};
```

### 3. Cambiar Día

#### ANTES

```javascript
const handleDiaChange = (nuevoDia) => {
  $dia.set(nuevoDia);
};

// Luego en un useEffect separado
useEffect(() => {
  if (selectedService && dia) {
    loadEspecialistasDisponibles();
  }
}, [dia, selectedService]);
```

#### DESPUÉS

```javascript
import { setDiaAction } from "./context/bookingsStore.js";

const handleDiaChange = (nuevoDia) => {
  setDiaAction(nuevoDia); // ✨ Carga especialistas automáticamente
};

// ✅ No necesitas useEffect
```

### 4. Cambiar Mes

#### ANTES

```javascript
const handleMesChange = (nuevoMes) => {
  $mes.set(nuevoMes);
};
```

#### DESPUÉS

```javascript
import { setMesAction } from "./context/bookingsStore.js";

const handleMesChange = (nuevoMes) => {
  setMesAction(nuevoMes);
};

// ⚠️ Nota: $mes ahora es computed, se actualiza automáticamente
// Esta acción solo existe para compatibilidad con el componente Calendario
```

### 5. Cargar Especialistas

#### ANTES

```javascript
import { loadEspecialistasDisponibles } from "./context/bookingsContext.js";

const handlePageChange = (page) => {
  loadEspecialistasDisponibles(page);
};
```

#### DESPUÉS

```javascript
import { changePageAction } from "./context/bookingsStore.js";

const handlePageChange = (page) => {
  changePageAction(page); // ✨ Llama a loadEspecialistasAction internamente
};
```

### 6. Seleccionar Especialista

#### ANTES

```javascript
import { selectEspecialistaYHora } from "./context/bookingsContext.js";

const handleSelectHora = (especialista, hora) => {
  selectEspecialistaYHora(especialista, hora);
};
```

#### DESPUÉS

```javascript
import { selectEspecialistaAction } from "./context/bookingsStore.js";

const handleSelectHora = (especialista, hora) => {
  selectEspecialistaAction(especialista, hora);
};
```

### 7. Confirmar Reserva

#### ANTES (en useReservas.js)

```javascript
const confirmarReserva = async () => {
  if (!selectedService?.id || !selectedEspecialista?.id_especialista || !dia || !selectedHora) {
    setError("Faltan datos requeridos");
    return;
  }

  setLoading(true);
  setError(null);

  try {
    const userBookings = await getUserBookings();
    const targetDate = formatearFechaISO(dia);

    if (hasWeeklyBookingForService(userBookings, selectedService.id, targetDate)) {
      throw new Error("Ya tienes una reserva de este servicio en esta semana");
    }

    const reservaData = {
      servicio_id: selectedService.id,
      especialista_id: selectedEspecialista.id_especialista,
      fecha: targetDate,
      hora: selectedHora,
      duracion: selectedService.duracion,
    };

    await createReserva(reservaData);
    reservaExitosa = true;
  } catch (err) {
    setError(err.message);
  } finally {
    setLoading(false);
  }

  if (reservaExitosa) {
    setTimeout(() => {
      resetBooking();
      window.location.href = "/user/reservas";
    }, 800);
  }
};
```

#### DESPUÉS (en bookingsStore.js)

```javascript
import { confirmReservaAction } from "./context/bookingsStore.js";

// En el componente
const { confirmarReserva } = useReservas();

// O directamente
<button onclick=${confirmReservaAction}>Confirmar</button>
```

### 8. Reset de Reserva

#### ANTES

```javascript
import { resetBooking } from "./context/bookingsContext.js";

const handleReset = () => {
  resetBooking();
};
```

#### DESPUÉS

```javascript
import { resetBookingAction } from "./context/bookingsStore.js";

const handleReset = () => {
  resetBookingAction();
};
```

---

## 🧩 Migración de Componentes

### Componente con Lógica → Componente Presentacional

#### ANTES: DateForm.js

```javascript
export const DateForm = () => {
  const dia = useStore($dia);
  const mes = useStore($mes);
  const especialistas = useStore($especialistas);
  const selectedService = useStore($selectedService);
  const selectedEspecialista = useStore($selectedEspecialista);
  const selectedHora = useStore($selectedHora);
  const currentPage = useStore($currentPage);
  const totalEspecialistas = useStore($totalEspecialistas);
  const pageSize = useStore($pageSize);

  const totalPages = Math.ceil(totalEspecialistas / pageSize); // ❌ Cálculo local

  useEffect(() => {
    // ❌ useEffect para cargar datos
    if (selectedService && dia) {
      loadEspecialistasDisponibles();
    }
  }, [dia, selectedService]);

  const handleDiaChange = (nuevoDia) => {
    $dia.set(nuevoDia); // ❌ Acceso directo a átomo
  };

  const handleMesChange = (nuevoMes) => {
    $mes.set(nuevoMes); // ❌ Acceso directo a átomo
  };

  const handleSelectHora = (especialista, hora) => {
    selectEspecialistaYHora(especialista, hora);
  };

  const handlePageChange = (page) => {
    loadEspecialistasDisponibles(page);
  };

  const handleConfirmar = () => {
    if (selectedEspecialista && selectedHora) {
      $estado.set("ConfirmationForm"); // ❌ Acceso directo a átomo
    }
  };

  return html`
    ...
  `;
};
```

#### DESPUÉS: DateForm.js

```javascript
export const DateForm = () => {
  // ✅ Suscripciones simplificadas
  const booking = useStore($bookingDraft);
  const especialistas = useStore($especialistas);
  const pagination = useStore($pagination);
  const mes = useStore($mes);

  // ✅ Sin useEffect
  // ✅ Sin cálculos locales
  // ✅ Sin handlers complejos

  return html`
    <div class="row g-4">
      <div class="col-12 col-lg-5">
        <${Calendario} fecha=${mes} diaSeleccionado=${booking.dia} handleMesChange=${setMesAction} //
        ✅ Acción directa handleDiaChange=${setDiaAction} // ✅ Acción directa />
      </div>

      <div class="col-12 col-lg-7">
        <${EspecialistasList} especialistas=${especialistas} onSelectHora=${selectEspecialistaAction}
        // ✅ Acción directa selectedEspecialista=${booking.especialista} selectedHora=${booking.hora}
        currentPage=${pagination.current} totalPages=${pagination.totalPages} // ✅ Ya calculado
        onPageChange=${changePageAction} // ✅ Acción directa />
      </div>
    </div>
  `;
};
```

---

## 🔍 Casos de Uso Comunes

### Caso 1: Verificar si hay un servicio seleccionado

#### ANTES

```javascript
const selectedService = useStore($selectedService);

if (selectedService) {
  console.log("Servicio seleccionado:", selectedService.nombre);
}
```

#### DESPUÉS

```javascript
const booking = useStore($bookingDraft);

if (booking.service) {
  console.log("Servicio seleccionado:", booking.service.nombre);
}
```

### Caso 2: Deshabilitar botón si falta selección

#### ANTES

```javascript
const selectedEspecialista = useStore($selectedEspecialista);
const selectedHora = useStore($selectedHora);

<button disabled=${!selectedEspecialista || !selectedHora}>
  Confirmar
</button>
```

#### DESPUÉS

```javascript
const booking = useStore($bookingDraft);

<button disabled=${!booking.especialista || !booking.hora}>
  Confirmar
</button>
```

### Caso 3: Mostrar loading durante confirmación

#### ANTES (en componente)

```javascript
const [loading, setLoading] = useState(false);

<button disabled=${loading}>
  {loading ? "Procesando..." : "Confirmar"}
</button>
```

#### DESPUÉS (desde store global)

```javascript
const uiState = useStore($uiState);

<button disabled=${uiState.loading}>
  {uiState.loading ? "Procesando..." : "Confirmar"}
</button>
```

### Caso 4: Navegar entre pasos

#### ANTES

```javascript
import { $estado } from "./context/bookingsContext.js";

const handleNext = () => {
  if (selectedService) {
    $estado.set("DateForm");
  }
};
```

#### DESPUÉS

```javascript
import { selectServiceAction } from "./context/bookingsStore.js";

// La navegación ocurre automáticamente en selectServiceAction
const handleSelect = (service) => {
  selectServiceAction(service); // ✨ Navega automáticamente
};
```

---

## ⚠️ Errores Comunes y Soluciones

### Error 1: "Cannot read property 'id' of null"

#### Causa

```javascript
const booking = useStore($bookingDraft);
console.log(booking.service.id); // ❌ service puede ser null
```

#### Solución

```javascript
const booking = useStore($bookingDraft);
console.log(booking.service?.id); // ✅ Optional chaining

// O con validación
if (booking.service) {
  console.log(booking.service.id);
}
```

### Error 2: "setKey is not a function"

#### Causa

```javascript
const booking = useStore($bookingDraft);
booking.service = newService; // ❌ No puedes mutar directamente
```

#### Solución

```javascript
// Usa la acción correspondiente
selectServiceAction(newService); // ✅

// O si necesitas actualizar directamente
$bookingDraft.setKey("service", newService); // ✅
```

### Error 3: "$mes.set is not a function"

#### Causa

```javascript
$mes.set(new Date()); // ❌ $mes ahora es computed, no se puede setear
```

#### Solución

```javascript
// $mes se actualiza automáticamente cuando cambias $bookingDraft.dia
setDiaAction(new Date()); // ✅ $mes se actualiza solo
```

### Error 4: "totalPages is undefined"

#### Causa

```javascript
const currentPage = useStore($currentPage);
const pageSize = useStore($pageSize);
const totalEspecialistas = useStore($totalEspecialistas);
const totalPages = Math.ceil(totalEspecialistas / pageSize); // ❌ Cálculo duplicado
```

#### Solución

```javascript
const pagination = useStore($pagination);
const totalPages = pagination.totalPages; // ✅ Ya calculado en la store
```

---

## 📋 Checklist de Migración

Usa este checklist para migrar un componente:

- [ ] Actualizar imports de `bookingsContext.js` a `bookingsStore.js`
- [ ] Reemplazar átomos individuales por maps (`$bookingDraft`, `$pagination`, `$uiState`)
- [ ] Eliminar `useEffect` que cargan datos (ahora se disparan desde acciones)
- [ ] Eliminar `useState` para loading/error (usar `$uiState`)
- [ ] Eliminar cálculos locales (usar computed stores o valores de maps)
- [ ] Reemplazar funciones por acciones (agregar sufijo "Action")
- [ ] Actualizar accesos a propiedades:
  - [ ] `selectedService` → `booking.service`
  - [ ] `dia` → `booking.dia`
  - [ ] `selectedEspecialista` → `booking.especialista`
  - [ ] `selectedHora` → `booking.hora`
  - [ ] `currentPage` → `pagination.current`
  - [ ] `pageSize` → `pagination.pageSize`
  - [ ] `totalPages` → `pagination.totalPages`
- [ ] Verificar que no hay accesos directos a átomos (`.set()`)
- [ ] Probar el componente en el navegador

---

## 🎓 Mejores Prácticas

1. **Usa acciones, no setters directos**

   ```javascript
   // ❌ Evitar
   $bookingDraft.setKey("service", service);

   // ✅ Preferir
   selectServiceAction(service);
   ```

2. **Confía en los computed stores**

   ```javascript
   // ❌ Evitar sincronización manual
   $dia.set(nuevoDia);
   $mes.set(nuevoDia);

   // ✅ Preferir
   setDiaAction(nuevoDia); // $mes se actualiza solo
   ```

3. **Usa maps para datos relacionados**

   ```javascript
   // ❌ Evitar múltiples suscripciones
   const service = useStore($selectedService);
   const dia = useStore($dia);
   const especialista = useStore($selectedEspecialista);

   // ✅ Preferir una sola suscripción
   const booking = useStore($bookingDraft);
   ```

4. **Deja que las acciones manejen la lógica**

   ```javascript
   // ❌ Evitar lógica en componentes
   const handleDiaChange = (dia) => {
     $dia.set(dia);
     if (selectedService) {
       loadEspecialistas();
     }
   };

   // ✅ Preferir acciones que encapsulan lógica
   const handleDiaChange = setDiaAction; // Ya maneja todo
   ```

---

**¡Migración completada!** 🎉

Si tienes dudas, consulta los archivos de ejemplo o la documentación completa en `REFACTORIZACION_RESUMEN.md`.
