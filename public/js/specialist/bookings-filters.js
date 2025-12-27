/**
 * @file Manages filters for specialist bookings.
 * @project app-reservas
 */

/**
 * Specialist bookings filters management
 */

const btnApplyFilters = document.getElementById("btnApplyFilters");
const btnClearFilters = document.getElementById("btnClearFilters");
/** @type {HTMLSelectElement} */
const filterEstado = document.getElementById("filterEstado");
/** @type {HTMLInputElement} */
const filterCliente = document.getElementById("filterCliente");
/** @type {HTMLInputElement} */
const filterFechaDesde = document.getElementById("filterFechaDesde");
/** @type {HTMLInputElement} */
const filterFechaHasta = document.getElementById("filterFechaHasta");

/**
 * Applies the selected filters by constructing a URL with query parameters
 * and navigating to it.
 */
const applyFilters = () => {
  const params = new URLSearchParams();

  if (filterEstado?.value) params.set("estado", filterEstado.value);
  if (filterCliente?.value) params.set("cliente", filterCliente.value);
  if (filterFechaDesde?.value) params.set("fecha_desde", filterFechaDesde.value);
  if (filterFechaHasta?.value) params.set("fecha_hasta", filterFechaHasta.value);

  globalThis.location.href = "/specialist/bookings?" + params.toString();
};

/**
 * Clears all filters by navigating to the base specialist bookings URL.
 */
const clearFilters = () => {
  globalThis.location.href = "/specialist/bookings";
};

if (btnApplyFilters) {
  btnApplyFilters.addEventListener("click", applyFilters);
}

if (btnClearFilters) {
  btnClearFilters.addEventListener("click", clearFilters);
}
