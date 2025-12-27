/**
 * @file Manages booking filters for client-side interactions.
 * @project app-reservas
 */

const estadoInput = document.getElementById("estadoInput");
/** @type {HTMLFormElement} */
const filterForm = document.getElementById("filterForm");

/**
 * Handles estado filter button clicks using event delegation.
 * @param {Event} e - Click event
 */
export const handleEstadoClick = (e) => {
  const button = e.target.closest("[data-estado]");
  if (!button) return;

  const estado = button.dataset.estado;
  estadoInput.value = estado;
  filterForm.submit();
};

if (filterForm) {
  filterForm.addEventListener("click", handleEstadoClick);
}
