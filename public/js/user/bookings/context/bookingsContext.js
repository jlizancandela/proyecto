/**
 * @file Context for managing booking state.
 * @project app-reservas
 */

import { atom } from "https://esm.sh/nanostores@0.9.5";
import { getServices, getEspecialistasDisponibles, getCurrentUser } from "../api/bookingsApi.js";
import { formatearFechaISO } from "../tools/formatters.js";

export const $estado = atom("ServiceForm");
export const $services = atom([]);
export const $selectedService = atom(null);
export const $dia = atom(new Date());
export const $mes = atom(new Date());
export const $especialistas = atom([]);
export const $selectedEspecialista = atom(null);
export const $selectedHora = atom(null);
export const $currentPage = atom(1);
export const $totalEspecialistas = atom(0);
export const $pageSize = atom(2);
export const $userName = atom("Usuario");

/**
 * Loads the current user and updates the user name store.
 * @returns {Promise<void>}
 */
export const loadUser = async () => {
  const user = await getCurrentUser();
  if (user?.nombre) {
    $userName.set(user.nombre);
  }
};

/**
 * Loads the current user and available services in parallel.
 * Updates the services store with the fetched data.
 * @returns {Promise<void>}
 */
export const loadServices = async () => {
  loadUser();
  const servicesData = await getServices();
  console.log("Servicios cargados:", servicesData);
  $services.set(servicesData);
};

/**
 * Sets the selected service and transitions the state to 'DateForm'.
 * @param {Object} service - The service object to select.
 * @returns {void}
 */
export const selectService = (service) => {
  $selectedService.set(service);
  $estado.set("DateForm");
};

/**
 * Loads available specialists based on the selected service, date, and pagination.
 * Updates the specialists, total count, and current page stores.
 * @param {number|null} [page=null] - The page number to load. If null, uses the current page from the store.
 * @returns {Promise<void>}
 */
export const loadEspecialistasDisponibles = async (page = null) => {
  const selectedService = $selectedService.get();
  const dia = $dia.get();
  const pageSize = $pageSize.get();
  const currentPage = page ?? $currentPage.get();

  if (!selectedService) {
    console.log("No hay servicio seleccionado");
    return;
  }

  if (!selectedService.id) {
    console.warn("El servicio seleccionado no tiene ID");
    return;
  }

  if (!dia) {
    console.warn("No hay fecha seleccionada");
    return;
  }

  const fechaFormateada = formatearFechaISO(dia);
  const offset = (currentPage - 1) * pageSize;

  const response = await getEspecialistasDisponibles(
    selectedService.id,
    fechaFormateada,
    pageSize,
    offset
  );

  $especialistas.set(response.data || []);
  $totalEspecialistas.set(response.total || 0);
  $currentPage.set(currentPage);
};

/**
 * Sets the selected specialist and time slot.
 * @param {Object} especialista - The selected specialist object.
 * @param {string} hora - The selected time slot (e.g., "10:00").
 * @returns {void}
 */
export const selectEspecialistaYHora = (especialista, hora) => {
  $selectedEspecialista.set(especialista);
  $selectedHora.set(hora);
  console.log("Selección guardada:", { especialista, hora });
};

/**
 * Resets the booking process to its initial state.
 * Clears selections but preserves the loaded services.
 * @returns {void}
 */
export const resetBooking = () => {
  const services = $services.get();
  $estado.set("ServiceForm");
  $selectedService.set(null);
  $dia.set(new Date());
  $mes.set(new Date());
  $especialistas.set([]);
  $selectedEspecialista.set(null);
  $selectedHora.set(null);
  $services.set(services);
  $currentPage.set(1);
  $totalEspecialistas.set(0);
};
