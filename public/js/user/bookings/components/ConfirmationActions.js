/**
 * @file ConfirmationActions Component
 * @project app-reservas
 *
 * Displays action buttons for confirming or modifying a booking.
 * Shows loading state with spinner during booking confirmation.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

const BUTTON_PADDING = "px-4";
const BUTTON_PADDING_CONFIRM = "px-5";
const BORDER_COLOR = "#cbd5e0";
const TEXT_COLOR = "#4a5568";
const PRIMARY_COLOR = "#e83e8c";

/**
 * Renders confirmation and modification buttons.
 * @param {Object} props - Component props.
 * @param {boolean} props.loading - Whether a request is in progress.
 * @param {Function} props.onModificar - Callback for modify button click.
 * @param {Function} props.onConfirmar - Callback for confirm button click.
 * @returns {Object} Preact component.
 */
export const ConfirmationActions = ({ loading, onModificar, onConfirmar }) => {
  return html`
    <div class="d-flex gap-3 justify-content-end flex-wrap">
      <button
        class="btn btn-outline-secondary rounded-pill ${BUTTON_PADDING}"
        onclick=${onModificar}
        disabled=${loading}
        style="border-color: ${BORDER_COLOR}; color: ${TEXT_COLOR};"
      >
        Modificar selección
      </button>
      <button
        class="btn rounded-pill ${BUTTON_PADDING_CONFIRM}"
        onclick=${onConfirmar}
        disabled=${loading}
        style="background-color: ${PRIMARY_COLOR}; border: none; color: white; font-weight: 600;"
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
