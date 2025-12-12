// Bookings Page JavaScript

let currentBookingId = null;
let currentAction = null;

// Event listeners cuando el DOM esté cargado
document.addEventListener("DOMContentLoaded", function () {
  // Event listeners para botones de modificar
  const modifyButtons = document.querySelectorAll(".btn-modify");
  modifyButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const bookingId = this.getAttribute("data-booking-id");
      modifyBooking(bookingId);
    });
  });

  // Event listeners para botones de cancelar
  const cancelButtons = document.querySelectorAll(".btn-cancel");
  cancelButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const bookingId = this.getAttribute("data-booking-id");
      cancelBooking(bookingId);
    });
  });

  // Event listener para el botón de confirmación del modal
  const confirmBtn = document.getElementById("confirmActionBtn");
  if (confirmBtn) {
    confirmBtn.addEventListener("click", confirmAction);
  }
});

function modifyBooking(bookingId) {
  currentBookingId = bookingId;
  currentAction = "modify";

  // Actualizar contenido del modal
  updateModal(
    "Modificar Reserva",
    "¿Deseas modificar esta reserva? Esto cancelará la reserva actual y te llevará a crear una nueva.",
    "Esta acción no se puede deshacer.",
    "No, mantener",
    "Sí, modificar"
  );

  const modal = new bootstrap.Modal(document.getElementById("actionModal"));
  modal.show();
}

function cancelBooking(bookingId) {
  currentBookingId = bookingId;
  currentAction = "cancel";

  // Actualizar contenido del modal
  updateModal(
    "Cancelar Reserva",
    "¿Estás seguro de que deseas cancelar esta reserva?",
    "Esta acción no se puede deshacer.",
    "No, mantener",
    "Sí, cancelar"
  );

  const modal = new bootstrap.Modal(document.getElementById("actionModal"));
  modal.show();
}

function updateModal(title, message, subMessage, cancelText, confirmText) {
  document.getElementById("modalTitle").textContent = title;
  document.getElementById("modalMessage").textContent = message;
  document.getElementById("modalSubMessage").textContent = subMessage;
  document.getElementById("modalCancelBtn").textContent = cancelText;
  document.getElementById("confirmActionBtn").textContent = confirmText;
}

function confirmAction() {
  if (currentBookingId && currentAction) {
    if (currentAction === "cancel") {
      // Crear formulario POST para cancelar
      const form = document.createElement("form");
      form.method = "POST";
      form.action = `/user/reservas/cancel/${currentBookingId}`;
      document.body.appendChild(form);
      form.submit();
    } else if (currentAction === "modify") {
      // Redirigir para modificar
      window.location.href = `/user/reservas/modify/${currentBookingId}`;
    }
  }
}
