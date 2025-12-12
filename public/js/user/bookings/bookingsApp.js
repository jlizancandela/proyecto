import { h, render } from "https://esm.sh/preact@10.19.3";
import { useEffect } from "https://esm.sh/preact@10.19.3/hooks";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { $estado, loadServices } from "./context/bookingsContext.js";
import { DateForm } from "./routes/dateForm.js";
import { ServiceForm } from "./routes/serviceForm.js";
import { ConfirmationForm } from "./routes/confirmationForm.js";

const html = htm.bind(h);

const BookingsApp = () => {
  const estado = useStore($estado);

  useEffect(() => {
    loadServices();
  }, []);

  return html`
    <h1 class="reserva-titulo">Nueva Reserva</h1>
    ${renderCurrentStep(estado)}
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
