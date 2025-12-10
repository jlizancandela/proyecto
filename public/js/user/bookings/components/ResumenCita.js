import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { ResumenItem } from "./ResumenItem.js";
import { formatearFechaLarga } from "../tools/formatters.js";

const html = htm.bind(h);

/**
 * Componente que muestra el resumen completo de la cita
 */
export const ResumenCita = ({ selectedService, selectedEspecialista, dia, selectedHora }) => {
  const fechaCapitalizada = formatearFechaLarga(dia);

  return html`
    <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color: #f8f9fa;">
      <h5 class="fw-bold mb-4" style="color: #2d3748;">Resumen de la Cita</h5>

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
