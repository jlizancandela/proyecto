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

export const getEspecialistasDisponibles = async (idServicio, fecha) => {
  // Validación simple de parámetros requeridos
  if (!idServicio) {
    console.warn("getEspecialistasDisponibles: Se necesita un ID de servicio");
    return [];
  }

  if (!fecha) {
    console.warn("getEspecialistasDisponibles: Se necesita una fecha");
    return [];
  }

  try {
    const response = await fetch(
      `/api/especialistas/disponibles?servicio=${idServicio}&fecha=${fecha}`
    );

    if (!response.ok) {
      throw new Error("Error al obtener especialistas disponibles");
    }

    const data = await response.json();
    return data;
  } catch (error) {
    console.error("Error al cargar especialistas:", error);
    return [];
  }
};

export const createReserva = async (reservaData) => {
  // Validar que tengamos datos de reserva
  if (!reservaData || typeof reservaData !== "object") {
    throw new Error("Los datos de la reserva son obligatorios");
  }

  // Extraer los campos que necesitamos
  const { servicio_id, especialista_id, fecha, hora } = reservaData;

  // Verificar que todos los campos estén presentes
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
