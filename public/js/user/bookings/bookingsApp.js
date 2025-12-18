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

const BookingsApp = () => {
  const estado = useStore($estado);

  useEffect(() => {
    loadServicesAction();
  }, []);

  return html`
    <div class="d-flex flex-column position-relative" style="min-height: 75vh;">
      <h1 class="reserva-titulo mb-4">Nueva Reserva</h1>

      <!-- Content Area -->
      <div class="flex-grow-1 mb-5">${renderCurrentStep(estado)}</div>

      <${BookingNavigation} />
    </div>
  `;
};

function renderCurrentStep(estado) {
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
}

render(
  html`
    <${BookingsApp} />
  `,
  document.getElementById("bookings-app")
);
