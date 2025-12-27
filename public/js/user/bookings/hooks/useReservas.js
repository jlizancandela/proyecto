/**
 * @file useReservas Hook - Bridge to Store
 * @project app-reservas
 *
 * Simplified hook that acts as a bridge between components
 * and the centralized store. Does NOT contain business logic.
 *
 * Responsibilities:
 * - Subscribe to atoms and maps from the store
 * - Expose data and actions to components
 * - Maintain a clean and consistent interface
 */

import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import { $bookingDraft, $uiState, confirmReservaAction } from "../context/bookingsStore.js";

/**
 * Custom hook for managing bookings
 *
 * @returns {Object} State and functions for confirming bookings
 * @returns {Object} booking - Current booking draft
 * @returns {Object} booking.service - Selected service
 * @returns {Date} booking.dia - Selected day
 * @returns {Object} booking.especialista - Selected specialist
 * @returns {string} booking.hora - Selected time
 * @returns {boolean} loading - Global loading state
 * @returns {string|null} error - Global error (if exists)
 * @returns {Function} confirmarReserva - Action to confirm the booking
 */
export const useReservas = () => {
  const booking = useStore($bookingDraft);
  const uiState = useStore($uiState);

  return {
    selectedService: booking.service,
    dia: booking.dia,
    selectedEspecialista: booking.especialista,
    selectedHora: booking.hora,
    loading: uiState.loading,
    error: uiState.error,
    confirmarReserva: confirmReservaAction,
  };
};
