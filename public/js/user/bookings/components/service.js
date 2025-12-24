/**
 * Service Component
 *
 * Displays an individual service card with name and duration.
 * Handles selection callback and shows selected state visually.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

const MIN_HEIGHT = "120px";
const SHADOW_COLOR = "rgba(13, 110, 253, 0.5)";

/**
 * Handles service selection.
 * @param {Object} service - Service object to select.
 * @param {Function} onSelect - Callback for service selection.
 */
const handleServiceClick = (service, onSelect) => {
  if (onSelect) {
    onSelect(service);
  }
};

/**
 * Renders a service card with selection capability.
 * @param {Object} props - Component props.
 * @param {Object} props.service - Service data object.
 * @param {Function} props.onSelect - Callback for service selection.
 * @param {boolean} [props.isSelected=false] - Whether the service is selected.
 * @returns {Object} Preact component.
 */
export const Service = ({ service, onSelect, isSelected = false }) => {
  return html`
    <div
      class="card ${isSelected ? "border-primary border-3" : ""}"
      onclick=${() => handleServiceClick(service, onSelect)}
      style="cursor: pointer; min-height: ${MIN_HEIGHT}; ${isSelected
        ? `box-shadow: 0 0 10px ${SHADOW_COLOR};`
        : ""}"
    >
      <div class="card-body d-flex align-items-center gap-3">
        <i class="bi bi-scissors fs-1 text-primary"></i>
        <div class="flex-grow-1">
          <h5 class="card-title mb-2">${service.nombre_servicio}</h5>
          <p class="card-text text-muted mb-0">
            <i class="bi bi-clock me-1"></i>
            ${service.duracion_minutos} minutos
          </p>
        </div>
      </div>
    </div>
  `;
};
