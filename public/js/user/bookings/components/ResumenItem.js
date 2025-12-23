/**
 * ResumenItem Component
 *
 * Displays a single item in the booking summary with icon, label, and value.
 * Used to show appointment details like service, specialist, date, and time.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

const ICON_BOX_SIZE = "48px";
const BACKGROUND_COLOR = "#fce7f3";
const ICON_COLOR = "#e83e8c";
const ICON_SIZE = "24px";
const TEXT_COLOR = "#2d3748";

/**
 * Renders a summary item with icon, label, and value.
 * @param {Object} props - Component props.
 * @param {string} props.icon - Bootstrap icon name.
 * @param {string} props.label - Item label text.
 * @param {string} props.value - Item value text.
 * @returns {Object} Preact component.
 */
export const ResumenItem = ({ icon, label, value }) => {
  return html`
    <div class="d-flex gap-3 mb-4">
      <div
        class="d-flex align-items-center justify-content-center rounded-3"
        style="width: ${ICON_BOX_SIZE}; height: ${ICON_BOX_SIZE}; background-color: ${BACKGROUND_COLOR}; flex-shrink: 0;"
      >
        <i class="bi bi-${icon}" style="font-size: ${ICON_SIZE}; color: ${ICON_COLOR};"></i>
      </div>
      <div>
        <p class="text-muted small mb-1">${label}</p>
        <p class="fw-semibold mb-0" style="color: ${TEXT_COLOR};">${value}</p>
      </div>
    </div>
  `;
};
