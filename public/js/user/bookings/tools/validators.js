/**
 * @file Utility functions for validating booking data.
 * @project app-reservas
 */

/**
 * @file Utility functions for validating booking data.
 * @project app-reservas
 */

/**
 * Calculates the start of the week for a given date.
 * Assumes Monday as the first day of the week.
 * @param {Date} date - The date to calculate the week start from.
 * @returns {Date} A new Date object representing the start of the week.
 */
const getWeekStart = (date) => {
  const d = new Date(date);
  const day = d.getDay();
  const diff = d.getDate() - day + (day === 0 ? -6 : 1);
  return new Date(d.setDate(diff));
};

/**
 * Calculates the end of the week for a given date.
 * @param {Date} date - The date to calculate the week end from.
 * @returns {Date} A new Date object representing the end of the week.
 */
const getWeekEnd = (date) => {
  const weekStart = getWeekStart(date);
  const weekEnd = new Date(weekStart);
  weekEnd.setDate(weekStart.getDate() + 6);
  return weekEnd;
};

/**
 * Checks if two dates fall within the same calendar week.
 * @param {Date} date1 - The first date.
 * @param {Date} date2 - The second date.
 * @returns {boolean} True if both dates are in the same week, false otherwise.
 */
const isDateInSameWeek = (date1, date2) => {
  const week1Start = getWeekStart(date1);
  const week2Start = getWeekStart(date2);
  return week1Start.getTime() === week2Start.getTime();
};

/**
 * Checks if a user already has an active booking for the same service within the same week.
 *
 * @param {Array} bookings - The list of user bookings to check.
 * @param {number} serviceId - The ID of the service to check for.
 * @param {string} targetDate - The target date for the new booking (YYYY-MM-DD).
 * @returns {boolean} True if a weekly conflict exists, false otherwise.
 */
export const hasWeeklyBookingForService = (bookings, serviceId, targetDate) => {
  const activeBookings = bookings.filter((booking) => booking.estado !== "Cancelada");

  return activeBookings.some((booking) => {
    const isSameService = booking.id_servicio === serviceId;
    const isSameWeek = isDateInSameWeek(new Date(booking.fecha_reserva), new Date(targetDate));
    return isSameService && isSameWeek;
  });
};

/**
 * Calculates the total hours of active bookings for a specific week.
 *
 * @param {Array} bookings - The list of user bookings.
 * @param {string} targetDate - The date to check the week for (YYYY-MM-DD).
 * @returns {number} Total duration in hours.
 */
export const getTotalWeeklyHours = (bookings, targetDate) => {
  const activeBookings = bookings.filter((booking) => booking.estado !== "Cancelada");
  const target = new Date(targetDate);

  const totalMinutes = activeBookings.reduce((sum, booking) => {
    if (isDateInSameWeek(new Date(booking.fecha_reserva), target)) {
      return sum + (booking.duracion_minutos || 60);
    }
    return sum;
  }, 0);

  return totalMinutes / 60;
};

/**
 * Checks if a new booking would exceed the weekly hours limit.
 *
 * @param {Array} bookings - The list of user bookings.
 * @param {string} targetDate - The target date (YYYY-MM-DD).
 * @param {number} newDuration - Duration of the new booking in minutes.
 * @param {number} [limit=40] - Maximum allowed hours per week.
 * @returns {boolean} True if the limit would be exceeded.
 */
export const exceedsWeeklyHoursLimit = (bookings, targetDate, newDuration, limit = 40) => {
  const currentHours = getTotalWeeklyHours(bookings, targetDate);
  return currentHours + newDuration / 60 > limit;
};
