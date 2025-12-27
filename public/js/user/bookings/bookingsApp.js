/**
 * @file Main application file for user bookings.
 * @project app-reservas
 */

import { h, render } from "https://esm.sh/preact@10.19.3";
import { useEffect } from "https://esm.sh/preact@10.19.3/hooks";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { $estado, loadServicesAction } from "./context/bookingsStore.js";
import { DateForm } from "./routes/dateForm.js";
import { ServiceForm } from "./routes/serviceForm.js";
import { ConfirmationForm } from "./routes/confirmationForm.js";
import { BookingNavigation } from "./components/BookingNavigation.js";

const html = htm.bind(h);

/**
 * Main application component for user bookings.
 * Manages the overall booking flow and displays different forms based on the current state.
 */
const BookingsApp = () => {
  const estado = useStore($estado);

  useEffect(() => {
    loadServicesAction();
  }, []);

  return html`
    <div class="d-flex flex-column position-relative" style="min-height: 75vh;">
      <h1 class="h2 text-dark mb-5">Nueva Reserva</h1>

      <!-- Content Area -->
      <div class="flex-grow-1 mb-5">${renderCurrentStep(estado)}</div>

      <${BookingNavigation} />
    </div>
  `;
};

/**
 * Renders the appropriate booking form component based on the current state.
 * @param {string} estado - The current state of the booking process (e.g., "ServiceForm", "DateForm", "ConfirmationForm").
 * @returns {Object} Preact component for the current step.
 */
const renderCurrentStep = (estado) => {
  if (!estado) {
    return html`
      <div class="alert alert-warning">Cargando...</div>
    `;
  }

  if (estado === "ConfirmationForm") {
    return html`
      <${ConfirmationForm} />
    `;
  }

  if (estado === "DateForm") {
    return html`
      <${DateForm} />
    `;
  }

  return html`
    <${ServiceForm} />
  `;
};

render(
  html`
    <${BookingsApp} />
  `,
  document.getElementById("bookings-app")
);
