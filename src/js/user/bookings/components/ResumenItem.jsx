/**
 * @file ResumenItem Component
 * @project app-reservas
 *
 * Displays a single item in the booking summary with icon, label, and value.
 * Used to show appointment details like service, specialist, date, and time.
 */

import { h } from "preact";

const ICON_BOX_SIZE = "48px";
const ICON_SIZE = "24px";

/**
 * Renders a summary item with icon, label, and value.
 * @param {Object} props - Component props.
 * @param {string} props.icon - Bootstrap icon name.
 * @param {string} props.label - Item label text.
 * @param {string} props.value - Item value text.
 * @returns {Object} Preact component.
 */
export const ResumenItem = ({ icon, label, value }) => {
  return (
    <div class="d-flex gap-3 mb-4">
      <div
        class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle flex-shrink-0"
        style={`width: ${ICON_BOX_SIZE}; height: ${ICON_BOX_SIZE};`}
      >
        <i class={`bi bi-${icon} text-primary`} style={`font-size: ${ICON_SIZE};`}></i>
      </div>
      <div>
        <p class="text-muted small mb-1">{label}</p>
        <p class="fw-semibold mb-0 text-dark">
          {value}
        </p>
      </div>
    </div>
  );
};
