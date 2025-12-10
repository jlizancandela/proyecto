/**
 * Formatea una fecha en español con formato largo
 * @param {Date} fecha - Fecha a formatear
 * @param {boolean} capitalizar - Si debe capitalizar la primera letra
 * @returns {string} Fecha formateada
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
 * Formatea una fecha en formato ISO (YYYY-MM-DD)
 * @param {Date} fecha - Fecha a formatear
 * @returns {string} Fecha en formato ISO
 */
export const formatearFechaISO = (fecha) => {
  return fecha.toISOString().split("T")[0];
};

/**
 * Capitaliza la primera letra de un string
 * @param {string} texto - Texto a capitalizar
 * @returns {string} Texto capitalizado
 */
export const capitalizarPrimeraLetra = (texto) => {
  if (!texto) return "";
  return texto.charAt(0).toUpperCase() + texto.slice(1);
};
