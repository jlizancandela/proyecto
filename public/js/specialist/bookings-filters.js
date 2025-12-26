/**
 * Specialist bookings filters management
 */

const btnApplyFilters = document.getElementById("btnApplyFilters");
const btnClearFilters = document.getElementById("btnClearFilters");
const filterEstado = document.getElementById("filterEstado");
const filterCliente = document.getElementById("filterCliente");
const filterFechaDesde = document.getElementById("filterFechaDesde");
const filterFechaHasta = document.getElementById("filterFechaHasta");

/**
 * Applies the selected filters
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
 * Clears all filters
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
