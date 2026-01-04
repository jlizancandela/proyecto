/**
 * @file ResumenCita Component
 * @project app-reservas
 *
 * Displays a summary of the booking appointment including service,
 * specialist, date, time, and duration information.
 */

import { h } from "preact";
import { ResumenItem } from "./ResumenItem.jsx";
import { formatearFechaLarga } from "../tools/formatters.js";

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

  return (
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-light">
      <h5 class="fw-bold mb-4 text-dark">
        Resumen de la Cita
      </h5>

      <ResumenItem
        icon="scissors"
        label="Servicio"
        value={selectedService?.nombre || selectedService?.nombre_servicio}
      />

      <ResumenItem
        icon="person"
        label="Especialista"
        value={`${selectedEspecialista?.nombre} ${selectedEspecialista?.apellidos}`}
      />

      <ResumenItem
        icon="calendar-event"
        label="Fecha y Hora"
        value={`${fechaCapitalizada} a las ${selectedHora || "..."}`}
      />

      <div class="d-flex gap-3">
        <ResumenItem
          icon="clock"
          label="Duración"
          value={`Aprox. ${selectedService?.duracion_minutos || 0} min`}
        />
      </div>
    </div>
  );
};
