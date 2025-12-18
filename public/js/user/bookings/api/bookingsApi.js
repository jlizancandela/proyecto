export const getServices = async () => {
  try {
    const response = await fetch("/api/services");

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

export const getEspecialistasDisponibles = async (
  idServicio,
  fecha,
  limit = null,
  offset = null
) => {
  // Validación simple de parámetros requeridos
  if (!idServicio) {
    console.warn("getEspecialistasDisponibles: Se necesita un ID de servicio");
    return { data: [], total: 0 };
  }

  if (!fecha) {
    console.warn("getEspecialistasDisponibles: Se necesita una fecha");
    return { data: [], total: 0 };
  }

  try {
    let url = `/api/especialistas/disponibles?servicio=${idServicio}&fecha=${fecha}`;

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

export const getUserBookings = async () => {
  try {
    const response = await fetch("/api/reservas");

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
    const response = await fetch("/api/reservas", {
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

export const getCurrentUser = async () => {
  try {
    const response = await fetch("/api/me");
    if (!response.ok) return null;
    const data = await response.json();
    return data.success ? data.data : null;
  } catch (error) {
    console.error("Error al obtener usuario:", error);
    return null;
  }
};
