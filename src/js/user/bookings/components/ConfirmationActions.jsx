/**
 * @file ConfirmationActions Component
 * @project app-reservas
 *
 * Displays action buttons for confirming or modifying a booking.
 * Shows loading state with spinner during booking confirmation.
 */

import { h } from "preact";

const BUTTON_PADDING = "px-4";
const BUTTON_PADDING_CONFIRM = "px-5";

/**
 * Renders confirmation and modification buttons.
 * @param {Object} props - Component props.
 * @param {boolean} props.loading - Whether a request is in progress.
 * @param {Function} props.onModificar - Callback for modify button click.
 * @param {Function} props.onConfirmar - Callback for confirm button click.
 * @param {Function} [props.onPagar] - Callback for pay button click (optional).
 * @returns {Object} Preact component.
 */
export const ConfirmationActions = ({ loading, onModificar, onConfirmar, onPagar }) => {
  return (
    <div class="d-flex gap-3 justify-content-end flex-wrap">
      <button
        class={`btn btn-outline-secondary rounded-pill ${BUTTON_PADDING}`}
        onClick={onModificar}
        disabled={loading}
      >
        Modificar selección
      </button>
      
      {onPagar && (
        <button
          class={`btn btn-primary rounded-pill ${BUTTON_PADDING_CONFIRM}`}
          onClick={onPagar}
          disabled={loading}
          style="font-weight: 600;"
        >
          {loading ? "Procesando..." : "Pagar ahora"}
        </button>
      )}

      <button
        class={`btn btn-outline-primary rounded-pill ${BUTTON_PADDING_CONFIRM}`}
        onClick={onConfirmar}
        disabled={loading}
        style="font-weight: 600;"
      >
        {loading ? (
          <span>
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Confirmando...
          </span>
        ) : (
          "Pagar en el local"
        )}
      </button>
    </div>
  );
};
