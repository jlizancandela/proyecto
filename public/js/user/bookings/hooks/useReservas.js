import { useState } from "https://esm.sh/preact@10.19.3/hooks";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import {
  $selectedService,
  $dia,
  $selectedEspecialista,
  $selectedHora,
  resetBooking,
} from "../context/bookingsContext.js";
import { createReserva, getUserBookings } from "../api/bookingsApi.js";
import { formatearFechaISO } from "../tools/formatters.js";
import { hasWeeklyBookingForService } from "../tools/validators.js";

/**
 * Hook personalizado para manejar la lógica de confirmación de reservas
 * @returns {Object} Estado y funciones para confirmar reservas
 */
export const useReservas = () => {
  const selectedService = useStore($selectedService);
  const dia = useStore($dia);
  const selectedEspecialista = useStore($selectedEspecialista);
  const selectedHora = useStore($selectedHora);

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const confirmarReserva = async () => {
    if (!selectedService?.id || !selectedEspecialista?.id_especialista || !dia || !selectedHora) {
      setError("Faltan datos requeridos para completar la reserva");
      return;
    }

    setLoading(true);
    setError(null);
    let reservaExitosa = false;

    try {
      const userBookings = await getUserBookings();
      const targetDate = formatearFechaISO(dia);

      if (hasWeeklyBookingForService(userBookings, selectedService.id, targetDate)) {
        throw new Error("Ya tienes una reserva de este servicio en esta semana");
      }

      const reservaData = {
        servicio_id: selectedService.id,
        especialista_id: selectedEspecialista.id_especialista,
        fecha: targetDate,
        hora: selectedHora,
        duracion: selectedService.duracion,
      };

      await createReserva(reservaData);
      reservaExitosa = true;
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }

    if (reservaExitosa) {
      setTimeout(() => {
        resetBooking();
        window.location.href = "/user/reservas";
      }, 800);
    }
  };

  return {
    selectedService,
    dia,
    selectedEspecialista,
    selectedHora,
    loading,
    error,
    confirmarReserva,
  };
};
