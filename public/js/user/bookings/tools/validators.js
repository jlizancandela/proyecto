const getWeekStart = (date) => {
  const d = new Date(date);
  const day = d.getDay();
  const diff = d.getDate() - day + (day === 0 ? -6 : 1);
  return new Date(d.setDate(diff));
};

const getWeekEnd = (date) => {
  const weekStart = getWeekStart(date);
  const weekEnd = new Date(weekStart);
  weekEnd.setDate(weekStart.getDate() + 6);
  return weekEnd;
};

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
