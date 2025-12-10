import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

/**
 * Componente con los botones de acción de confirmación
 */
export const ConfirmationActions = ({ loading, onModificar, onConfirmar }) => {
  return html`
    <div class="d-flex gap-3 justify-content-end flex-wrap">
      <button
        class="btn btn-outline-secondary rounded-pill px-4"
        onclick=${onModificar}
        disabled=${loading}
        style="border-color: #cbd5e0; color: #4a5568;"
      >
        Modificar selección
      </button>
      <button
        class="btn rounded-pill px-5"
        onclick=${onConfirmar}
        disabled=${loading}
        style="background-color: #e83e8c; border: none; color: white; font-weight: 600;"
      >
        ${loading
          ? html`
              <span class="spinner-border spinner-border-sm me-2" role="status"></span>
              Confirmando...
            `
          : "Confirmar Reserva"}
      </button>
    </div>
  `;
};
