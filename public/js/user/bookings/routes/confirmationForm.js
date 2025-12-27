/**
 * @file Booking confirmation form component.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { useStore } from "https://esm.sh/@nanostores/preact?deps=preact@10.19.3";
import { $estado, $userName } from "../context/bookingsStore.js";
import { useReservas } from "../hooks/useReservas.js";
import { ResumenCita } from "../components/ResumenCita.js";
import { StatusAlert } from "../components/StatusAlert.js";
import { ConfirmationActions } from "../components/ConfirmationActions.js";

const html = htm.bind(h);

/**
 * Renders the booking confirmation form with summary and action buttons
 * @returns {import("preact").VNode} Confirmation form component
 */
export const ConfirmationForm = () => {
  const userName = useStore($userName);

  const {
    selectedService,
    dia,
    selectedEspecialista,
    selectedHora,
    loading,
    error,
    confirmarReserva,
  } = useReservas();

  const handleModificar = () => {
    $estado.set("DateForm");
  };

  return html`
    <div class="container py-4">
      <div class="mb-4">
        <h2 class="fw-bold mb-2" style="color: #2d3748;">Confirma tu Cita</h2>
        <p class="text-muted">Revisa los detalles de tu reserva antes de confirmar.</p>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-5">
          <${ResumenCita}
            selectedService=${selectedService}
            selectedEspecialista=${selectedEspecialista}
            dia=${dia}
            selectedHora=${selectedHora}
          />
        </div>

        <div class="col-12 col-lg-7">
          <div
            class="card border-0 shadow-sm rounded-4 p-4 h-100 d-flex flex-column justify-content-between"
          >
            <div>
              <h5 class="fw-bold mb-3" style="color: #2d3748;">Todo listo, ${userName}</h5>
              <p class="text-muted mb-4">
                Solo falta un paso para confirmar tu cita. Tus datos se rellenarán automáticamente.
              </p>

              <${StatusAlert} loading=${loading} error=${error} />
            </div>

            <${ConfirmationActions}
              loading=${loading}
              onModificar=${handleModificar}
              onConfirmar=${confirmarReserva}
            />
          </div>
        </div>
      </div>
    </div>
  `;
};
