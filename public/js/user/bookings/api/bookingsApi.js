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
