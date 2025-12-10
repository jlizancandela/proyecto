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
    <h1 class="m-5">Nueva Reserva</h1>
    ${estado === "ConfirmationForm"
      ? html`
          <${ConfirmationForm} />
        `
      : estado === "DateForm"
      ? html`
          <${DateForm} />
        `
      : html`
          <${ServiceForm} />
        `}
  `;
};

render(
  html`
    <${BookingsApp} />
  `,
  document.getElementById("bookings-app")
);
