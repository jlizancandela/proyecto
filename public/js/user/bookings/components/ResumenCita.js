/**
 * ResumenCita Component
 *
 * Displays a summary of the booking appointment including service,
 * specialist, date, time, and duration information.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { ResumenItem } from "./ResumenItem.js";
import { formatearFechaLarga } from "../tools/formatters.js";

const html = htm.bind(h);

const BACKGROUND_COLOR = "#f8f9fa";
const TEXT_COLOR = "#2d3748";

/**
 * Renders appointment summary with service, specialist, date, and time details.
 * @param {Object} props - Component props.
 * @param {Object} props.selectedService - Selected service object.
 * @param {Object} props.selectedEspecialista - Selected specialist object.
 * @param {Date} props.dia - Selected appointment date.
 * @param {string} props.selectedHora - Selected appointment time.
 * @returns {Object} Preact component.
 */
export const ResumenCita = ({ selectedService, selectedEspecialista, dia, selectedHora }) => {
  const fechaCapitalizada = formatearFechaLarga(dia);

  return html`
    <div
      class="card border-0 shadow-sm rounded-4 p-4"
      style="background-color: ${BACKGROUND_COLOR};"
    >
      <h5 class="fw-bold mb-4" style="color: ${TEXT_COLOR};">Resumen de la Cita</h5>

      <${ResumenItem} icon="scissors" label="Servicio" value=${selectedService?.nombre} />

      <${ResumenItem}
        icon="person"
        label="Especialista"
        value="${selectedEspecialista?.nombre} ${selectedEspecialista?.apellidos}"
      />

      <${ResumenItem}
        icon="calendar-event"
        label="Fecha y Hora"
        value="${fechaCapitalizada} a las ${selectedHora}"
      />

      <div class="d-flex gap-3">
        <${ResumenItem}
          icon="clock"
          label="Duración"
          value="Aprox. ${selectedService?.duracion} min"
        />
      </div>
    </div>
  `;
};
