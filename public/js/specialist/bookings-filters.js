/**
 * @file Manages filters for specialist bookings.
 * @project app-reservas
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
export const applyFilters = () => {
  const params = new URLSearchParams();

  const estado = filterEstado?.value?.trim();
  const cliente = filterCliente?.value?.trim();
  const fechaDesde = filterFechaDesde?.value?.trim();
  const fechaHasta = filterFechaHasta?.value?.trim();

  if (estado) params.set("estado", estado);
  if (cliente) params.set("cliente", cliente);
  if (fechaDesde) params.set("fecha_desde", fechaDesde);
  if (fechaHasta) params.set("fecha_hasta", fechaHasta);

  globalThis.location.href = "/specialist/bookings?" + params.toString();
};

/**
 * Clears all filters by navigating to the base specialist bookings URL.
 */
export const clearFilters = () => {
  globalThis.location.href = "/specialist/bookings";
};

if (btnApplyFilters) {
  btnApplyFilters.addEventListener("click", applyFilters);
}

if (btnClearFilters) {
  btnClearFilters.addEventListener("click", clearFilters);
}
