import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

/**
 * Componente que muestra alertas de estado (loading/error)
 */
export const StatusAlert = ({ loading, error }) => {
  if (loading) {
    return html`
      <div class="alert alert-info d-flex align-items-center" role="alert">
        <div class="spinner-border spinner-border-sm me-2" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <div>Confirmando tu reserva...</div>
      </div>
    `;
  }

  if (error) {
    return html`
      <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        ${error}
      </div>
    `;
  }

  return null;
};
