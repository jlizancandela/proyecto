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
 * Usa componentes locales para evitar problemas de zona horaria
 * @param {Date} fecha - Fecha a formatear
 * @returns {string} Fecha en formato ISO
 */
export const formatearFechaISO = (fecha) => {
  const year = fecha.getFullYear();
  const month = String(fecha.getMonth() + 1).padStart(2, "0");
  const day = String(fecha.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
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

/**
 * Verifica si una fecha es hoy
 * @param {Date} fecha - Fecha a verificar
 * @returns {boolean} True si la fecha es hoy
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
 * Verifica si una hora ya ha pasado en el día actual
 * Si la fecha no es hoy, retorna false (todas las horas son válidas)
 * @param {Date} fecha - Fecha seleccionada
 * @param {string} hora - Hora en formato "HH:MM"
 * @returns {boolean} True si la hora ya pasó
 */
export const horaYaPaso = (fecha, hora) => {
  // Si no es hoy, todas las horas son válidas
  if (!esHoy(fecha)) {
    return false;
  }

  // Parsear la hora
  const [horas, minutos] = hora.split(":").map(Number);

  // Crear fecha con la hora especificada
  const horaSeleccionada = new Date();
  horaSeleccionada.setHours(horas, minutos, 0, 0);

  // Comparar con la hora actual
  const ahora = new Date();

  return horaSeleccionada <= ahora;
};
