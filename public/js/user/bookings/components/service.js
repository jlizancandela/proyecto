import { h } from "https://esm.sh/preact@10.19.3";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { $selectedService } from "../context/bookingsContext.js";

const html = htm.bind(h);

export const Service = ({ service }) => {
  const selectedService = useStore($selectedService);

  const isSelected = selectedService?.id === service.id;

  return html`
    <div
      class="card ${isSelected ? "border-primary border-3" : ""}"
      onclick=${() => $selectedService.set(service)}
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
