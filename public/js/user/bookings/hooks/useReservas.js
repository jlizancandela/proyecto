/**
 * ========================================
 * USE RESERVAS HOOK - Puente a la Store
 * ========================================
 *
 * Hook simplificado que actúa como puente entre los componentes
 * y la store centralizada. NO contiene lógica de negocio.
 *
 * Responsabilidades:
 * - Suscribirse a los átomos y maps de la store
 * - Exponer datos y acciones a los componentes
 * - Mantener una interfaz limpia y consistente
 */

import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import { $bookingDraft, $uiState, confirmReservaAction } from "../context/bookingsStore.js";

/**
 * Hook personalizado para manejar reservas
 *
 * @returns {Object} Estado y funciones para confirmar reservas
 * @returns {Object} booking - Draft de la reserva actual
 * @returns {Object} booking.service - Servicio seleccionado
 * @returns {Date} booking.dia - Día seleccionado
 * @returns {Object} booking.especialista - Especialista seleccionado
 * @returns {string} booking.hora - Hora seleccionada
 * @returns {boolean} loading - Estado de carga global
 * @returns {string|null} error - Error global (si existe)
 * @returns {Function} confirmarReserva - Acción para confirmar la reserva
 */
export const useReservas = () => {
  // Suscribirse a los stores
  const booking = useStore($bookingDraft);
  const uiState = useStore($uiState);

  // Exponer interfaz simplificada
  return {
    // Datos de la reserva (desde el draft)
    selectedService: booking.service,
    dia: booking.dia,
    selectedEspecialista: booking.especialista,
    selectedHora: booking.hora,

    // Estados de UI
    loading: uiState.loading,
    error: uiState.error,

    // Acción de confirmación (importada desde la store)
    confirmarReserva: confirmReservaAction,
  };
};
