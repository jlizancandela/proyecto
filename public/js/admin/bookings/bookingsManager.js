// Handles booking modals and delete operations for admin panel.

import { fetchBooking, createBooking, updateBooking, deleteBooking } from "./api.js";

const createBookingForm = document.getElementById("createBookingForm");
const editBookingForm = document.getElementById("editBookingForm");
const editBookingModal = document.getElementById("editBookingModal");

const editBookingId = document.getElementById("editBookingId");
const editFecha = document.getElementById("editFecha");
const editHora = document.getElementById("editHora");
const editEstado = document.getElementById("editEstado");
const editObservaciones = document.getElementById("editObservaciones");
const editCliente = document.getElementById("editCliente");
const editEspecialista = document.getElementById("editEspecialista");
const editServicio = document.getElementById("editServicio");
const editDuracion = document.getElementById("editDuracion");

/**
 * Shows a temporary success message alert.
 *
 * @param {string} message - Success message to display.
 */
const showSuccess = (message) => {
  const alertDiv = document.createElement("div");
  alertDiv.className = "alert alert-success alert-dismissible fade show";

  const icon = document.createElement("i");
  icon.className = "bi bi-check-circle me-2";

  const messageText = document.createTextNode(message);

  const closeButton = document.createElement("button");
  closeButton.type = "button";
  closeButton.className = "btn-close";
  closeButton.setAttribute("data-bs-dismiss", "alert");

  alertDiv.appendChild(icon);
  alertDiv.appendChild(messageText);
  alertDiv.appendChild(closeButton);

  document.querySelector(".mb-4").prepend(alertDiv);
  setTimeout(() => alertDiv.remove(), 3000);
};

/**
 * Handles the deletion of a booking.
 *
 * @param {Event} e - Click event.
 */
const handleDeleteBooking = async (e) => {
  const bookingId = e.currentTarget.dataset.bookingId;

  if (!confirm("¿Estás seguro de que deseas eliminar esta reserva?")) {
    return;
  }

  try {
    const data = await deleteBooking(bookingId);

    if (data.success) {
      showSuccess("Reserva eliminada correctamente");
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      alert(data.error || "Error al eliminar la reserva");
    }
  } catch (error) {
    console.error("Error deleting booking:", error);
    alert("Error al conectar con el servidor");
  }
};

/**
 * Opens the edit modal and populates it with booking data.
 *
 * @param {Event} e - Click event.
 */
const handleEditBooking = async (e) => {
  const bookingId = e.currentTarget.dataset.bookingId;

  try {
    const data = await fetchBooking(bookingId);

    if (data.success) {
      const booking = data.data;

      editBookingId.value = booking.id_reserva;
      editFecha.value = booking.fecha_reserva;
      editHora.value = booking.hora_inicio;
      editEstado.value = booking.estado;
      editObservaciones.value = booking.observaciones || "";
      editCliente.value = booking.id_cliente;
      editEspecialista.value = booking.id_especialista;
      editServicio.value = booking.id_servicio;

      const inicio = new Date(`2000-01-01T${booking.hora_inicio}`);
      const fin = new Date(`2000-01-01T${booking.hora_fin}`);
      const duracion = (fin - inicio) / (1000 * 60);
      editDuracion.value = duracion;

      const modal = new bootstrap.Modal(editBookingModal);
      modal.show();
    } else {
      alert("Error al cargar los datos de la reserva");
    }
  } catch (error) {
    console.error("Error fetching booking:", error);
    alert("Error al conectar con el servidor");
  }
};

/**
 * Handles create booking form submission.
 *
 * @param {Event} e - Submit event.
 */
const handleCreateBooking = async (e) => {
  e.preventDefault();

  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());

  try {
    const result = await createBooking(data);

    if (result.success) {
      showSuccess("Reserva creada correctamente");
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      alert(result.error || "Error al crear la reserva");
    }
  } catch (error) {
    console.error("Error creating booking:", error);
    alert("Error al conectar con el servidor");
  }
};

/**
 * Handles update booking form submission.
 *
 * @param {Event} e - Submit event.
 */
const handleUpdateBooking = async (e) => {
  e.preventDefault();

  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());
  const bookingId = data.id_reserva;

  try {
    const result = await updateBooking(bookingId, data);

    if (result.success) {
      showSuccess("Reserva actualizada correctamente");
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      alert(result.error || "Error al actualizar la reserva");
    }
  } catch (error) {
    console.error("Error updating booking:", error);
    alert("Error al conectar con el servidor");
  }
};

/**
 * Attaches event listeners to booking action buttons.
 */
const attachHandlers = () => {
  document.querySelectorAll(".btn-delete-booking").forEach((btn) => {
    if (!btn.disabled) {
      btn.addEventListener("click", handleDeleteBooking);
    }
  });

  document.querySelectorAll(".btn-edit-booking").forEach((btn) => {
    btn.addEventListener("click", handleEditBooking);
  });
};

createBookingForm.addEventListener("submit", handleCreateBooking);
editBookingForm.addEventListener("submit", handleUpdateBooking);
attachHandlers();
