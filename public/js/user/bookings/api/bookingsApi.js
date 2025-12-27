/**
 * @file API utilities for user booking operations.
 * @project app-reservas
 */

const SERVICES_API_URL = "/api/services";
const AVAILABLE_SPECIALISTS_API_URL = "/api/especialistas/disponibles";
const USER_BOOKINGS_API_URL = "/api/reservas";
const CURRENT_USER_API_URL = "/api/me";

/**
 * Fetches all available services.
 * @returns {Promise<Array>} Array of services or empty array on error.
 */
export const getServices = async () => {
  try {
    const response = await fetch(SERVICES_API_URL);

    if (!response.ok) {
      throw new Error("Error al obtener servicios");
    }

    const data = await response.json();
    return data;
  } catch (error) {
    console.error("Error al cargar servicios:", error);
    return [];
  }
};

/**
 * Fetches available specialists for a service and date.
 * @param {string} idServicio - The service ID.
 * @param {string} fecha - The date in format YYYY-MM-DD.
 * @param {number} [limit=null] - Maximum number of results.
 * @param {number} [offset=null] - Number of results to skip.
 * @returns {Promise<Object>} Object with data array and total count.
 */
export const getEspecialistasDisponibles = async (
  idServicio,
  fecha,
  limit = null,
  offset = null
) => {
  if (!idServicio) {
    return { data: [], total: 0 };
  }

  if (!fecha) {
    return { data: [], total: 0 };
  }

  try {
    let url = `${AVAILABLE_SPECIALISTS_API_URL}?servicio=${idServicio}&fecha=${fecha}`;

    if (limit !== null) {
      url += `&limit=${limit}`;
    }

    if (offset !== null) {
      url += `&offset=${offset}`;
    }

    const response = await fetch(url);

    if (!response.ok) {
      throw new Error("Error al obtener especialistas disponibles");
    }

    const data = await response.json();
    return data;
  } catch (error) {
    console.error("Error al cargar especialistas:", error);
    return { data: [], total: 0 };
  }
};

/**
 * Fetches all bookings for the current user.
 * @returns {Promise<Array>} Array of user bookings or empty array on error.
 */
export const getUserBookings = async () => {
  try {
    const response = await fetch(USER_BOOKINGS_API_URL);

    if (!response.ok) {
      throw new Error("Error al obtener reservas");
    }

    const data = await response.json();
    return data.reservas || [];
  } catch (error) {
    console.error("Error al cargar reservas:", error);
    return [];
  }
};

/**
 * Creates a new booking with the provided data.
 * @param {Object} reservaData - The booking data containing servicio_id, especialista_id, fecha, and hora.
 * @returns {Promise<Object>} The created booking data.
 * @throws {Error} If validation fails or API request fails.
 */
export const createReserva = async (reservaData) => {
  if (!reservaData || typeof reservaData !== "object") {
    throw new Error("Los datos de la reserva son obligatorios");
  }

  const { servicio_id, especialista_id, fecha, hora } = reservaData;

  if (!servicio_id) {
    throw new Error("Debes seleccionar un servicio");
  }

  if (!especialista_id) {
    throw new Error("Debes seleccionar un especialista");
  }

  if (!fecha) {
    throw new Error("Debes seleccionar una fecha");
  }

  if (!hora) {
    throw new Error("Debes seleccionar una hora");
  }

  try {
    const response = await fetch(USER_BOOKINGS_API_URL, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(reservaData),
    });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error || "Error al crear la reserva");
    }

    return await response.json();
  } catch (error) {
    console.error("Error al crear reserva:", error);
    throw error;
  }
};

/**
 * Fetches the current authenticated user.
 * @returns {Promise<Object|null>} User object or null if not authenticated.
 */
export const getCurrentUser = async () => {
  try {
    const response = await fetch(CURRENT_USER_API_URL);
    if (!response.ok) return null;
    const data = await response.json();
    return data.success ? data.data : null;
  } catch (error) {
    console.error("Error al obtener usuario:", error);
    return null;
  }
};
