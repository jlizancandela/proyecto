import { atom } from "https://esm.sh/nanostores@0.9.5";
import { getServices, getEspecialistasDisponibles } from "../api/bookingsApi.js";
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
export const $pageSize = atom(3);

export const loadServices = async () => {
  const servicesData = await getServices();
  console.log("Servicios cargados:", servicesData);
  $services.set(servicesData);
};

export const selectService = (service) => {
  $selectedService.set(service);
  $estado.set("DateForm");
};

export const loadEspecialistasDisponibles = async (page = null) => {
  const selectedService = $selectedService.get();
  const dia = $dia.get();
  const pageSize = $pageSize.get();
  const currentPage = page !== null ? page : $currentPage.get();

  // Guard clauses mejoradas
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

  console.log("Cargando especialistas para:", {
    servicio: selectedService.id,
    fecha: fechaFormateada,
    page: currentPage,
    limit: pageSize,
    offset: offset,
  });

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

export const selectEspecialistaYHora = (especialista, hora) => {
  $selectedEspecialista.set(especialista);
  $selectedHora.set(hora);
  console.log("Selección guardada:", { especialista, hora });
};

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
