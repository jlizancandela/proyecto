import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

/**
 * Componente para mostrar un ítem del resumen con icono
 */
export const ResumenItem = ({ icon, label, value }) => {
  return html`
    <div class="d-flex gap-3 mb-4">
      <div
        class="d-flex align-items-center justify-content-center rounded-3"
        style="width: 48px; height: 48px; background-color: #fce7f3; flex-shrink: 0;"
      >
        <i class="bi bi-${icon}" style="font-size: 24px; color: #e83e8c;"></i>
      </div>
      <div>
        <p class="text-muted small mb-1">${label}</p>
        <p class="fw-semibold mb-0" style="color: #2d3748;">${value}</p>
      </div>
    </div>
  `;
};
