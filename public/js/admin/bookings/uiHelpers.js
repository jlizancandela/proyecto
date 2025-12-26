// Provides helper functions for UI elements like alerts, badges, and date formatting.

/**
 * Gets bootstrap color class based on booking status.
 *
 * @param {string} status - The booking status.
 * @return {string} Bootstrap color class.
 */
export const getStatusColor = (status) => {
  const colors = {
    Pendiente: "warning",
    Confirmada: "success",
    Completada: "info",
    Cancelada: "secondary",
  };
  return colors[status] || "secondary";
};

/**
 * Formats a date string to a readable format (DD/MM/YYYY).
 *
 * @param {string} dateStr - Date string.
 * @return {string} Formatted date.
 */
export const formatDate = (dateStr) => {
  const date = new Date(dateStr + "T00:00:00");
  return date.toLocaleDateString("es-ES", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  });
};

/**
 * Shows an error message in a container.
 *
 * @param {HTMLElement} container - Container element to show error in.
 * @param {string} message - Error message to display.
 */
export const showError = (container, message) => {
  container.innerHTML = `
    <div class="alert alert-danger">
      <i class="bi bi-exclamation-triangle me-2"></i>
      ${message}
    </div>
  `;
};

/**
 * Shows a temporary success message alert.
 *
 * @param {string} message - Success message to display.
 */
export const showSuccess = (message) => {
  const alertDiv = document.createElement("div");
  alertDiv.className = "alert alert-success alert-dismissible fade show";
  alertDiv.innerHTML = `
    <i class="bi bi-check-circle me-2"></i>
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;
  document.querySelector(".mb-4").prepend(alertDiv);

  setTimeout(() => alertDiv.remove(), 3000);
};
