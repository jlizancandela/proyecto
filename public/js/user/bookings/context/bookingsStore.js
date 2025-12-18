/**
 * ========================================
 * BOOKINGS STORE - Estado Centralizado
 * ========================================
 *
 * Store principal para el sistema de reservas usando Nano Stores.
 * Implementa el patrón de Estado Atómico y Acciones para eliminar
 * prop drilling y centralizar toda la lógica de negocio.
 *
 * Arquitectura:
 * - Maps agrupados para datos relacionados
 * - Computed stores para valores derivados
 * - Acciones centralizadas para toda la lógica de negocio
 * - Estados de UI globales (loading, error)
 */

import { atom, map, computed } from "https://esm.sh/nanostores@0.9.5";
import {
  getServices,
  getEspecialistasDisponibles,
  getCurrentUser,
  createReserva,
  getUserBookings,
} from "../api/bookingsApi.js";
import { formatearFechaISO } from "../tools/formatters.js";
import { hasWeeklyBookingForService } from "../tools/validators.js";

// ========================================
// ÁTOMOS DE ESTADO
// ========================================

/**
 * Estado actual de la aplicación (ruta activa)
 * Valores posibles: "ServiceForm" | "DateForm" | "ConfirmationForm"
 */
export const $estado = atom("ServiceForm");

/**
 * Lista de servicios disponibles
 */
export const $services = atom([]);

/**
 * Lista de especialistas disponibles para el servicio y fecha seleccionados
 */
export const $especialistas = atom([]);

/**
 * Nombre del usuario actual
 */
export const $userName = atom("Usuario");

// ========================================
// MAPS AGRUPADOS
// ========================================

/**
 * Draft de la reserva en progreso
 * Agrupa todos los datos de selección del usuario
 */
export const $bookingDraft = map({
  serviceId: null,
  service: null,
  dia: new Date(),
  especialista: null,
  hora: null,
});

/**
 * Estado de paginación para la lista de especialistas
 */
export const $pagination = map({
  current: 1,
  pageSize: 2,
  totalPages: 0,
});

/**
 * Estados de UI globales
 */
export const $uiState = map({
  loading: false,
  error: null,
});

// ========================================
// COMPUTED STORES (Valores Derivados)
// ========================================

/**
 * Mes actual calculado automáticamente desde el día seleccionado
 * Se actualiza reactivamente cuando $bookingDraft.dia cambia
 */
export const $mes = computed($bookingDraft, (draft) => {
  return draft.dia || new Date();
});

/**
 * Total de especialistas (para cálculos de paginación)
 */
export const $totalEspecialistas = atom(0);

// ========================================
// ACCIONES DE INICIALIZACIÓN
// ========================================

/**
 * Carga el usuario actual desde la API
 */
export const loadUserAction = async () => {
  const user = await getCurrentUser();
  if (user && user.nombre) {
    $userName.set(user.nombre);
  }
};

/**
 * Carga los servicios disponibles
 * También inicializa la carga del usuario en paralelo
 */
export const loadServicesAction = async () => {
  // Cargar usuario y servicios en paralelo
  loadUserAction();
  const servicesData = await getServices();
  console.log("Servicios cargados:", servicesData);
  $services.set(servicesData);
};

// ========================================
// ACCIONES DE SELECCIÓN DE SERVICIO
// ========================================

/**
 * Selecciona un servicio y navega al formulario de fecha
 * Carga automáticamente los especialistas para el día actual
 * @param {Object} service - Servicio seleccionado
 */
export const selectServiceAction = async (service) => {
  $bookingDraft.setKey("service", service);
  $bookingDraft.setKey("serviceId", service.id);

  // Cargar especialistas para el día actual antes de navegar
  await loadEspecialistasAction();

  $estado.set("DateForm");
};

// ========================================
// ACCIONES DE FECHA
// ========================================

/**
 * Actualiza el mes del calendario
 * @param {Date} fecha - Nueva fecha para el mes
 */
export const setMesAction = (fecha) => {
  // El mes se calcula automáticamente desde $bookingDraft.dia
  // Esta acción solo se mantiene para compatibilidad con el componente Calendario
  // pero el valor real se deriva del computed store $mes
  console.log("Mes actualizado (computed):", fecha);
};

/**
 * Actualiza el día seleccionado y dispara la carga de especialistas
 * @param {Date} dia - Día seleccionado
 */
export const setDiaAction = async (dia) => {
  $bookingDraft.setKey("dia", dia);

  // Resetear página a 1 cuando cambia el día
  $pagination.setKey("current", 1);

  // Disparar automáticamente la carga de especialistas
  await loadEspecialistasAction();
};

// ========================================
// ACCIONES DE ESPECIALISTAS
// ========================================

/**
 * Carga los especialistas disponibles para el servicio y fecha seleccionados
 * Maneja automáticamente la paginación
 * @param {number|null} page - Página específica a cargar (opcional)
 */
export const loadEspecialistasAction = async (page = null) => {
  const draft = $bookingDraft.get();
  const pagination = $pagination.get();
  const currentPage = page !== null ? page : pagination.current;

  // Guard clauses
  if (!draft.service) {
    console.log("No hay servicio seleccionado");
    return;
  }

  if (!draft.service.id) {
    console.warn("El servicio seleccionado no tiene ID");
    return;
  }

  if (!draft.dia) {
    console.warn("No hay fecha seleccionada");
    return;
  }

  const fechaFormateada = formatearFechaISO(draft.dia);
  const offset = (currentPage - 1) * pagination.pageSize;

  console.log("Cargando especialistas para:", {
    servicio: draft.service.id,
    fecha: fechaFormateada,
    page: currentPage,
    limit: pagination.pageSize,
    offset: offset,
  });

  const response = await getEspecialistasDisponibles(
    draft.service.id,
    fechaFormateada,
    pagination.pageSize,
    offset
  );

  $especialistas.set(response.data || []);
  $totalEspecialistas.set(response.total || 0);

  // Actualizar paginación
  const totalPages = Math.ceil((response.total || 0) / pagination.pageSize);
  $pagination.setKey("current", currentPage);
  $pagination.setKey("totalPages", totalPages);
};

/**
 * Selecciona un especialista y una hora
 * @param {Object} especialista - Especialista seleccionado
 * @param {string} hora - Hora seleccionada
 */
export const selectEspecialistaAction = (especialista, hora) => {
  $bookingDraft.setKey("especialista", especialista);
  $bookingDraft.setKey("hora", hora);
  console.log("Selección guardada:", { especialista, hora });
};

// ========================================
// ACCIONES DE PAGINACIÓN
// ========================================

/**
 * Cambia la página actual de especialistas
 * @param {number} page - Número de página
 */
export const changePageAction = async (page) => {
  await loadEspecialistasAction(page);
};

// ========================================
// ACCIONES DE NAVEGACIÓN
// ========================================

/**
 * Navega al formulario de confirmación
 * Valida que haya especialista y hora seleccionados
 */
export const goToConfirmationAction = () => {
  const draft = $bookingDraft.get();

  if (draft.especialista && draft.hora) {
    $estado.set("ConfirmationForm");
  } else {
    console.warn("Debe seleccionar un especialista y una hora antes de confirmar");
  }
};

// ========================================
// ACCIÓN DE CONFIRMACIÓN
// ========================================

/**
 * Confirma la reserva actual
 * Incluye toda la lógica de validación, llamada a API y manejo de estados
 *
 * Proceso:
 * 1. Valida que todos los datos requeridos estén presentes
 * 2. Verifica que no exista una reserva semanal del mismo servicio
 * 3. Crea la reserva en la API
 * 4. Maneja estados de loading y error
 * 5. Redirige tras éxito
 * 6. Limpia el draft
 */
export const confirmReservaAction = async () => {
  const draft = $bookingDraft.get();

  // Validación de datos requeridos
  if (!draft.service?.id || !draft.especialista?.id_especialista || !draft.dia || !draft.hora) {
    $uiState.setKey("error", "Faltan datos requeridos para completar la reserva");
    return;
  }

  // Iniciar loading
  $uiState.setKey("loading", true);
  $uiState.setKey("error", null);
  let reservaExitosa = false;

  try {
    // Obtener reservas del usuario
    const userBookings = await getUserBookings();
    const targetDate = formatearFechaISO(draft.dia);

    // Validar límite semanal de reservas por servicio
    if (hasWeeklyBookingForService(userBookings, draft.service.id, targetDate)) {
      throw new Error("Ya tienes una reserva de este servicio en esta semana");
    }

    // Preparar datos de la reserva
    const reservaData = {
      servicio_id: draft.service.id,
      especialista_id: draft.especialista.id_especialista,
      fecha: targetDate,
      hora: draft.hora,
      duracion: draft.service.duracion,
    };

    // Crear la reserva
    await createReserva(reservaData);
    reservaExitosa = true;
  } catch (err) {
    $uiState.setKey("error", err.message);
  } finally {
    $uiState.setKey("loading", false);
  }

  // Si la reserva fue exitosa, redirigir y limpiar
  if (reservaExitosa) {
    setTimeout(() => {
      resetBookingAction();
      window.location.href = "/user/reservas";
    }, 800);
  }
};

// ========================================
// ACCIÓN DE RESET
// ========================================

/**
 * Limpia completamente el estado de la reserva
 * Mantiene los servicios cargados para evitar recargas innecesarias
 */
export const resetBookingAction = () => {
  // Resetear estado de navegación
  $estado.set("ServiceForm");

  // Limpiar draft de reserva
  $bookingDraft.set({
    serviceId: null,
    service: null,
    dia: new Date(),
    especialista: null,
    hora: null,
  });

  // Limpiar especialistas
  $especialistas.set([]);
  $totalEspecialistas.set(0);

  // Resetear paginación
  $pagination.set({
    current: 1,
    pageSize: 2,
    totalPages: 0,
  });

  // Limpiar estados de UI
  $uiState.set({
    loading: false,
    error: null,
  });
};
