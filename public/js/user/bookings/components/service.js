/**
 * ========================================
 * SERVICE COMPONENT - Componente Presentacional
 * ========================================
 *
 * Componente para mostrar un servicio individual.
 * Acepta un callback para manejar la selección.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

/**
 * Componente de servicio
 * @param {Object} props
 * @param {Object} props.service - Datos del servicio
 * @param {Function} props.onSelect - Callback cuando se selecciona el servicio
 * @param {boolean} props.isSelected - Si el servicio está seleccionado
 */
export const Service = ({ service, onSelect, isSelected = false }) => {
  const handleClick = () => {
    if (onSelect) {
      onSelect(service);
    }
  };

  return html`
    <div
      class="card ${isSelected ? "border-primary border-3" : ""}"
      onclick=${handleClick}
      style="cursor: pointer; min-height: 120px; ${isSelected
        ? "box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);"
        : ""}"
    >
      <div class="card-body d-flex align-items-center gap-3">
        <i class="bi bi-scissors fs-1 text-primary"></i>
        <div class="flex-grow-1">
          <h5 class="card-title mb-2">${service.nombre}</h5>
          <p class="card-text text-muted mb-0">
            <i class="bi bi-clock me-1"></i>
            ${service.duracion} minutos
          </p>
        </div>
      </div>
    </div>
  `;
};
