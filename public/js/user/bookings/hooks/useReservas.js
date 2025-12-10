import { useState } from "https://esm.sh/preact@10.19.3/hooks";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import {
  $selectedService,
  $dia,
  $selectedEspecialista,
  $selectedHora,
  resetBooking,
} from "../context/bookingsContext.js";
import { createReserva } from "../api/bookingsApi.js";
import { formatearFechaISO } from "../tools/formatters.js";

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
    setLoading(true);
    setError(null);

    try {
      const reservaData = {
        servicio_id: selectedService?.id,
        especialista_id: selectedEspecialista?.id_especialista,
        fecha: formatearFechaISO(dia),
        hora: selectedHora,
        duracion: selectedService?.duracion,
      };

      await createReserva(reservaData);

      // Éxito - resetear y volver al inicio después de 1.5s
      setTimeout(() => {
        resetBooking();
      }, 1500);
    } catch (err) {
      setError(err.message);
      setLoading(false);
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
