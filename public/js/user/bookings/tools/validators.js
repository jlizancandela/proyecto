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
  const diff = d.getDate() - day + (day === 0 ? -6 : 1); // Adjust for Sunday (0) to be last day
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

export const hasWeeklyBookingForService = (bookings, serviceId, targetDate) => {
  const activeBookings = bookings.filter((booking) => booking.estado !== "Cancelada");

  return activeBookings.some((booking) => {
    const isSameService = booking.id_servicio === serviceId;
    const isSameWeek = isDateInSameWeek(new Date(booking.fecha_reserva), new Date(targetDate));
    return isSameService && isSameWeek;
  });
};
