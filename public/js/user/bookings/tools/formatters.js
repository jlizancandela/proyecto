/**
 * @file Utility functions for formatting dates and times.
 * @project app-reservas
 */

/**
 * Formats a date in long Spanish format
 * @param {Date} fecha - Date to format
 * @param {boolean} capitalizar - Whether to capitalize the first letter
 * @returns {string} Formatted date string
 */
export const formatearFechaLarga = (fecha, capitalizar = true) => {
  const fechaFormateada = fecha.toLocaleDateString("es-ES", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });

  return capitalizar
    ? fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1)
    : fechaFormateada;
};

/**
 * Formats a date in ISO format (YYYY-MM-DD)
 * Uses local components to avoid timezone issues
 * @param {Date} fecha - Date to format
 * @returns {string} Date in ISO format
 */
export const formatearFechaISO = (fecha) => {
  const year = fecha.getFullYear();
  const month = String(fecha.getMonth() + 1).padStart(2, "0");
  const day = String(fecha.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
};

/**
 * Capitalizes the first letter of a string
 * @param {string} texto - Text to capitalize
 * @returns {string} Capitalized text
 */
export const capitalizarPrimeraLetra = (texto) => {
  if (!texto) return "";
  return texto.charAt(0).toUpperCase() + texto.slice(1);
};

/**
 * Checks if a date is today
 * @param {Date} fecha - Date to check
 * @returns {boolean} True if date is today
 */
export const esHoy = (fecha) => {
  const hoy = new Date();
  return (
    fecha.getDate() === hoy.getDate() &&
    fecha.getMonth() === hoy.getMonth() &&
    fecha.getFullYear() === hoy.getFullYear()
  );
};

/**
 * Checks if a time has already passed today
 * If the date is not today, returns false (all times are valid)
 * @param {Date} fecha - Selected date
 * @param {string} hora - Time in "HH:MM" format
 * @returns {boolean} True if time has passed
 */
export const isPastTime = (fecha, hora) => {
  if (!esHoy(fecha)) {
    return false;
  }

  const [horas, minutos] = hora.split(":").map(Number);
  const horaSeleccionada = new Date();
  horaSeleccionada.setHours(horas, minutos, 0, 0);

  const ahora = new Date();
  return horaSeleccionada <= ahora;
};
