/**
 * @file Bookings Store - Centralized State
 * @project app-reservas
 */

import { atom, map, computed } from "nanostores";
import {
  getServices,
  getEspecialistasDisponibles,
  getCurrentUser,
  createReserva,
  getUserBookings,
  createCheckoutSession,
} from "../api/bookingsApi.js";
import { formatearFechaISO } from "../tools/formatters.js";
import { hasWeeklyBookingForService, exceedsWeeklyHoursLimit } from "../tools/validators.js";

/**
 * Current application state (active route)
 * Possible values: "ServiceForm" | "DateForm" | "ConfirmationForm"
 */
export const $estado = atom("ServiceForm");

/**
 * List of available services
 */
export const $services = atom([]);

/**
 * List of available specialists for the selected service and date
 */
export const $especialistas = atom([]);

/**
 * Current user name
 */
export const $userName = atom("Usuario");

/**
 * Booking draft in progress
 * Groups all user selection data
 */
export const $bookingDraft = map({
  serviceId: null,
  service: null,
  dia: new Date(),
  especialista: null,
  hora: null,
});

/**
 * Pagination state for the specialists list
 */
export const $pagination = map({
  current: 1,
  pageSize: 2,
  totalPages: 0,
});

/**
 * Global UI states
 */
export const $uiState = map({
  loading: false,
  error: null,
});

/**
 * Current month displayed in the calendar
 */
export const $mes = atom(new Date());

/**
 * Total number of specialists (for pagination calculations)
 */
export const $totalEspecialistas = atom(0);

/**
 * Loads the current user from the API
 * @returns {Promise<void>}
 */
export const loadUserAction = async () => {
  const user = await getCurrentUser();
  if (user?.nombre) {
    $userName.set(user.nombre);
  }
};

/**
 * Loads available services and initializes user loading in parallel
 * @returns {Promise<void>}
 */
export const loadServicesAction = async () => {
  loadUserAction();
  const servicesData = await getServices();
  console.log("Servicios cargados:", servicesData);
  $services.set(servicesData.servicios || []);
};

/**
 * Selects a service and navigates to the date form
 * Automatically loads specialists for the current day
 * @param {Object} service - Selected service
 * @returns {Promise<void>}
 */
export const selectServiceAction = async (service) => {
  $bookingDraft.setKey("service", service);
  $bookingDraft.setKey("serviceId", service.id);

  await loadEspecialistasAction();

  $estado.set("DateForm");
};

/**
 * Updates the calendar month
 * @param {Date} fecha - New date for the month
 * @returns {void}
 */
export const setMesAction = (fecha) => {
  $mes.set(fecha);
};

/**
 * Updates the selected day and triggers specialist loading
 * @param {Date} dia - Selected day
 * @returns {Promise<void>}
 */
export const setDiaAction = async (dia) => {
  $bookingDraft.setKey("dia", dia);
  $mes.set(dia);
  $pagination.setKey("current", 1);
  await loadEspecialistasAction();
};

/**
 * Loads available specialists for the selected service and date
 * Automatically handles pagination
 * @param {number|null} [page=null] - Specific page to load (optional)
 * @returns {Promise<void>}
 */
export const loadEspecialistasAction = async (page = null) => {
  const draft = $bookingDraft.get();
  const pagination = $pagination.get();
  const currentPage = page ?? pagination.current;

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

  const response = await getEspecialistasDisponibles(
    draft.service.id,
    fechaFormateada,
    pagination.pageSize,
    offset
  );

  $especialistas.set(response.data || []);
  $totalEspecialistas.set(response.total || 0);

  const totalPages = Math.ceil((response.total || 0) / pagination.pageSize);
  $pagination.setKey("current", currentPage);
  $pagination.setKey("totalPages", totalPages);
};

/**
 * Selects a specialist and a time slot
 * @param {Object} especialista - Selected specialist
 * @param {string} hora - Selected time slot
 * @returns {void}
 */
export const selectEspecialistaAction = (especialista, hora) => {
  $bookingDraft.setKey("especialista", especialista);
  $bookingDraft.setKey("hora", hora);
  console.log("Selección guardada:", { especialista, hora });
};

/**
 * Changes the current page of specialists
 * @param {number} page - Page number
 * @returns {Promise<void>}
 */
export const changePageAction = async (page) => {
  await loadEspecialistasAction(page);
};

/**
 * Navigates to the confirmation form
 * Validates that a specialist and time slot are selected
 * @returns {void}
 */
export const goToConfirmationAction = () => {
  const draft = $bookingDraft.get();

  if (draft.especialista && draft.hora) {
    $estado.set("ConfirmationForm");
  } else {
    console.warn("Debe seleccionar un especialista y una hora antes de confirmar");
  }
};

/**
 * Confirms the current booking
 * Includes all validation logic, API call, and state handling
 *
 * Process:
 * 1. Validates that all required data is present
 * 2. Verifies that no weekly booking exists for the same service
 * 3. Creates the booking via API
 * 4. Handles loading and error states
 * 5. Redirects after success
 * 6. Clears the draft
 * @returns {Promise<void>}
 */
export const confirmReservaAction = async () => {
  const draft = $bookingDraft.get();

  if (!draft.service?.id || !draft.especialista?.id_especialista || !draft.dia || !draft.hora) {
    $uiState.setKey("error", "Faltan datos requeridos para completar la reserva");
    return;
  }

  $uiState.setKey("loading", true);
  $uiState.setKey("error", null);
  let reservaExitosa = false;

  try {
    const userBookings = await getUserBookings();
    const targetDate = formatearFechaISO(draft.dia);

    if (hasWeeklyBookingForService(userBookings, draft.service.id, targetDate)) {
      throw new Error("Ya tienes una reserva de este servicio en esta semana");
    }

    if (exceedsWeeklyHoursLimit(userBookings, targetDate, draft.service.duracion_minutos || 60)) {
      throw new Error("Ya has alcanzado el máximo de 40 horas permitidas por ley para esta semana");
    }

    const reservaData = {
      servicio_id: draft.service.id,
      especialista_id: draft.especialista.id_especialista,
      fecha: targetDate,
      hora: draft.hora,
      duracion: draft.service.duracion_minutos,
    };

    await createReserva(reservaData);
    reservaExitosa = true;
  } catch (err) {
    $uiState.setKey("error", err.message);
  } finally {
    $uiState.setKey("loading", false);
  }

  if (reservaExitosa) {
    setTimeout(() => {
      resetBookingAction();
      globalThis.location.href = "/user/reservas";
    }, 800);
  }
};

/**
 * Initiates the payment process via Stripe Checkout
 * @returns {Promise<void>}
 */
export const confirmReservaWithPaymentAction = async () => {
    const draft = $bookingDraft.get();
    
    if (!draft.service?.id || !draft.especialista?.id_especialista || !draft.dia || !draft.hora) {
        $uiState.setKey("error", "Faltan datos requeridos para completar la reserva");
        return;
    }

    $uiState.setKey("loading", true);
    $uiState.setKey("error", null);

    try {
        // 1. Validations
        const userBookings = await getUserBookings();
        const targetDate = formatearFechaISO(draft.dia);

        if (hasWeeklyBookingForService(userBookings, draft.service.id, targetDate)) {
            throw new Error("Ya tienes una reserva de este servicio en esta semana");
        }

        if (exceedsWeeklyHoursLimit(userBookings, targetDate, draft.service.duracion_minutos || 60)) {
            throw new Error("Ya has alcanzado el máximo de 40 horas permitidas por ley para esta semana");
        }

        // 2. Create Booking (Pending)
        const reservaData = {
            servicio_id: draft.service.id,
            especialista_id: draft.especialista.id_especialista,
            fecha: targetDate,
            hora: draft.hora,
            duracion: draft.service.duracion_minutos,
            estado: 'Pendiente'
        };

        const response = await createReserva(reservaData);
        
        if (!response.id_reserva) {
            throw new Error("Error: No se pudo crear la reserva inicial.");
        }

        // 3. Create Checkout Session
        const session = await createCheckoutSession(response.id_reserva);
        
        if (session.url) {
            // 4. Redirect to Stripe
            globalThis.location.href = session.url;
        } else {
            throw new Error("Error: No se recibió la URL de pago.");
        }

    } catch (err) {
        $uiState.setKey("error", "Error en el proceso de pago: " + err.message);
        $uiState.setKey("loading", false);
    }
};


/**
 * Completely clears the booking state
 * Keeps loaded services to avoid unnecessary reloads
 * @returns {void}
 */
export const resetBookingAction = () => {
  $estado.set("ServiceForm");

  $bookingDraft.set({
    serviceId: null,
    service: null,
    dia: new Date(),
    especialista: null,
    hora: null,
  });

  $mes.set(new Date());

  $especialistas.set([]);
  $totalEspecialistas.set(0);

  $pagination.set({
    current: 1,
    pageSize: 2,
    totalPages: 0,
  });

  $uiState.set({
    loading: false,
    error: null,
  });
};
