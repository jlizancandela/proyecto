/**
 * @file Manages booking filters for client-side interactions.
 * @project app-reservas
 */

const estadoInput = document.getElementById("estadoInput");
/** @type {HTMLFormElement} */
const filterForm = document.getElementById("filterForm");
/** @type {HTMLInputElement} */
const fechaInput = document.getElementById("fecha");

/**
 * Sets the booking status filter and submits the form.
 * @param {string} estado - The status to filter by.
 */
const selectEstado = (estado) => {
  estadoInput.value = estado;
  filterForm.submit();
};

/**
 * Clears the date filter and submits the form.
 */
const clearFecha = () => {
  fechaInput.value = "";
  filterForm.submit();
};

globalThis.selectEstado = selectEstado;
globalThis.clearFecha = clearFecha;
