// API utilities for booking operations.

/**
 * Fetches a single booking by ID.
 *
 * @param {number} bookingId - The booking ID.
 * @return {Promise<object>} The booking data.
 */
const fetchBooking = async (bookingId) => {
  const response = await fetch(`/admin/api/reservas/${bookingId}`);
  return response.json();
};

/**
 * Creates a new booking.
 *
 * @param {object} bookingData - The booking data to create.
 * @return {Promise<object>} The API response.
 */
const createBooking = async (bookingData) => {
  const response = await fetch("/admin/api/reservas", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(bookingData),
  });
  return response.json();
};

/**
 * Updates an existing booking.
 *
 * @param {number} bookingId - The booking ID.
 * @param {object} bookingData - The booking data to update.
 * @return {Promise<object>} The API response.
 */
const updateBooking = async (bookingId, bookingData) => {
  const response = await fetch(`/admin/api/reservas/${bookingId}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(bookingData),
  });
  return response.json();
};

/**
 * Deletes a booking.
 *
 * @param {number} bookingId - The booking ID.
 * @return {Promise<object>} The API response.
 */
const deleteBooking = async (bookingId) => {
  const response = await fetch(`/admin/api/reservas/${bookingId}`, {
    method: "DELETE",
  });
  return response.json();
};
