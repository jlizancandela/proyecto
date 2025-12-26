/**
 * Manages booking actions (modify, cancel) in the user interface.
 * Handles modal updates and form submissions for booking management.
 */

let currentBookingId = null;
let currentAction = null;

const modifyButtons = document.querySelectorAll(".btn-modify");
const cancelButtons = document.querySelectorAll(".btn-cancel");
const confirmBtn = document.getElementById("confirmActionBtn");
const actionModal = document.getElementById("actionModal");
const modalTitle = document.getElementById("modalTitle");
const modalMessage = document.getElementById("modalMessage");
const modalSubMessage = document.getElementById("modalSubMessage");
const modalCancelBtn = document.getElementById("modalCancelBtn");

/**
 * Initializes event listeners for booking action buttons.
 */
const initializeBookingListeners = () => {
  modifyButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const bookingId = button.getAttribute("data-booking-id");
      modifyBooking(bookingId);
    });
  });

  cancelButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const bookingId = button.getAttribute("data-booking-id");
      cancelBooking(bookingId);
    });
  });

  if (confirmBtn) {
    confirmBtn.addEventListener("click", confirmAction);
  }
};

/**
 * Opens modify booking confirmation modal.
 * @param {string} bookingId - The booking ID to modify.
 */
const modifyBooking = (bookingId) => {
  currentBookingId = bookingId;
  currentAction = "modify";

  updateModal(
    "Modificar Reserva",
    "¿Deseas modificar esta reserva? Esto cancelará la reserva actual y te llevará a crear una nueva.",
    "Esta acción no se puede deshacer.",
    "No, mantener",
    "Sí, modificar"
  );

  const modal = new bootstrap.Modal(actionModal);
  modal.show();
};

/**
 * Opens cancel booking confirmation modal.
 * @param {string} bookingId - The booking ID to cancel.
 */
const cancelBooking = (bookingId) => {
  currentBookingId = bookingId;
  currentAction = "cancel";

  updateModal(
    "Cancelar Reserva",
    "¿Estás seguro de que deseas cancelar esta reserva?",
    "Esta acción no se puede deshacer.",
    "No, mantener",
    "Sí, cancelar"
  );

  const modal = new bootstrap.Modal(actionModal);
  modal.show();
};

/**
 * Updates modal content with provided text.
 * @param {string} title - Modal title.
 * @param {string} message - Modal main message.
 * @param {string} subMessage - Modal sub message.
 * @param {string} cancelText - Cancel button text.
 * @param {string} confirmText - Confirm button text.
 */
const updateModal = (title, message, subMessage, cancelText, confirmText) => {
  modalTitle.textContent = title;
  modalMessage.textContent = message;
  modalSubMessage.textContent = subMessage;
  modalCancelBtn.textContent = cancelText;
  confirmBtn.textContent = confirmText;
};

/**
 * Executes the current booking action (modify or cancel).
 */
const confirmAction = () => {
  if (currentBookingId && currentAction) {
    if (currentAction === "cancel") {
      const form = document.createElement("form");
      form.method = "POST";
      form.action = `/user/reservas/cancel/${currentBookingId}`;
      document.body.appendChild(form);
      form.submit();
    } else if (currentAction === "modify") {
      window.location.href = `/user/reservas/modify/${currentBookingId}`;
    }
  }
};

document.addEventListener("DOMContentLoaded", initializeBookingListeners);

// Expose functions for testing
if (typeof window !== "undefined") {
  window.modifyBooking = modifyBooking;
  window.cancelBooking = cancelBooking;
  window.updateModal = updateModal;
  window.confirmAction = confirmAction;
  window.initializeBookingListeners = initializeBookingListeners;
}
