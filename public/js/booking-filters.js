const estadoInput = document.getElementById("estadoInput");
const filterForm = document.getElementById("filterForm");
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
