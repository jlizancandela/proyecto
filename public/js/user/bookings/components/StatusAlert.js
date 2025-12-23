/**
 * StatusAlert Component
 *
 * Displays status messages for the booking process.
 * Shows loading indicator during confirmation and error messages if issues occur.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

/**
 * Renders a loading alert with spinner.
 * @returns {Object} Preact component.
 */
const renderLoadingAlert = () => html`
  <div class="alert alert-info d-flex align-items-center" role="alert">
    <div class="spinner-border spinner-border-sm me-2" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div>Confirming your booking...</div>
  </div>
`;

/**
 * Renders an error alert.
 * @param {string} error - Error message to display.
 * @returns {Object} Preact component.
 */
const renderErrorAlert = (error) => html`
  <div class="alert alert-danger" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    ${error}
  </div>
`;

/**
 * Renders status alert based on loading or error state.
 * @param {Object} props - Component props.
 * @param {boolean} props.loading - Whether request is in progress.
 * @param {string} props.error - Error message if any.
 * @returns {Object|null} Preact component or null if no status.
 */
export const StatusAlert = ({ loading, error }) => {
  if (loading) {
    return renderLoadingAlert();
  }

  if (error) {
    return renderErrorAlert(error);
  }

  return null;
};
