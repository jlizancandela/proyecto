/**
 * @file Handles booking modals and delete operations for the admin panel.
 * @project app-reservas
 */

import { fetchBooking, createBooking, updateBooking, deleteBooking } from "./api.js";
import { notification } from "../../shared/components/toast.js";

const createBookingForm = document.getElementById("createBookingForm");
const editBookingForm = document.getElementById("editBookingForm");
const editBookingModal = document.getElementById("editBookingModal");

const deleteButtons = document.querySelectorAll(".btn-delete-booking");
const editButtons = document.querySelectorAll(".btn-edit-booking");

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
      notification("Reserva eliminada correctamente", "success");
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      notification(data.error || "Error al eliminar la reserva", "error");
    }
  } catch (error) {
    console.error("Error deleting booking:", error);
    notification("Error al conectar con el servidor", "error");
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
      const form = editBookingForm.elements;

      form.editBookingId.value = booking.id_reserva;
      form.editFecha.value = booking.fecha_reserva;
      form.editHora.value = booking.hora_inicio;
      form.editEstado.value = booking.estado;
      form.editObservaciones.value = booking.observaciones || "";
      form.editCliente.value = booking.id_cliente;
      form.editEspecialista.value = booking.id_especialista;
      form.editServicio.value = booking.id_servicio;

      const inicio = new Date(`2000-01-01T${booking.hora_inicio}`);
      const fin = new Date(`2000-01-01T${booking.hora_fin}`);
      const duracion = (fin - inicio) / (1000 * 60);
      form.editDuracion.value = duracion;

      const modal = new bootstrap.Modal(editBookingModal);
      modal.show();
    } else {
      notification("Error al cargar los datos de la reserva", "error");
    }
  } catch (error) {
    console.error("Error fetching booking:", error);
    notification("Error al conectar con el servidor", "error");
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
      notification("Reserva creada correctamente", "success");
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      notification(result.error || "Error al crear la reserva", "error");
    }
  } catch (error) {
    console.error("Error creating booking:", error);
    notification("Error al conectar con el servidor", "error");
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
      notification("Reserva actualizada correctamente", "success");
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      notification(result.error || "Error al actualizar la reserva", "error");
    }
  } catch (error) {
    console.error("Error updating booking:", error);
    notification("Error al conectar con el servidor", "error");
  }
};

deleteButtons.forEach((btn) => {
  if (!btn.disabled) {
    btn.addEventListener("click", handleDeleteBooking);
  }
});

editButtons.forEach((btn) => {
  btn.addEventListener("click", handleEditBooking);
});

if (createBookingForm) {
  createBookingForm.addEventListener("submit", handleCreateBooking);
}

if (editBookingForm) {
  editBookingForm.addEventListener("submit", handleUpdateBooking);
}
