// Manages the bookings admin panel including table display, filters, and CRUD operations.

import { showError, showSuccess } from "./uiHelpers.js";
import {
  updateUrlParams,
  updatePdfExportLink,
  loadFiltersFromUrl,
  buildFiltersFromInputs,
  clearFilterInputs,
} from "./filterManager.js";
import { renderBookingsTable } from "./tableRenderer.js";
import { renderPagination } from "./paginationRenderer.js";

const filterEstado = document.getElementById("filterEstado");
const filterCliente = document.getElementById("filterCliente");
const filterEspecialista = document.getElementById("filterEspecialista");
const filterFechaDesde = document.getElementById("filterFechaDesde");
const filterFechaHasta = document.getElementById("filterFechaHasta");
const btnApplyFilters = document.getElementById("btnApplyFilters");
const btnClearFilters = document.getElementById("btnClearFilters");

const bookingsTableContainer = document.getElementById("bookingsTableContainer");
const paginationContainer = document.getElementById("paginationContainer");

const createBookingModal = document.getElementById("createBookingModal");
const editBookingModal = document.getElementById("editBookingModal");
const createBookingForm = document.getElementById("createBookingForm");
const editBookingForm = document.getElementById("editBookingForm");

const editBookingId = document.getElementById("editBookingId");
const editFecha = document.getElementById("editFecha");
const editHora = document.getElementById("editHora");
const editEstado = document.getElementById("editEstado");
const editObservaciones = document.getElementById("editObservaciones");
const editCliente = document.getElementById("editCliente");
const editEspecialista = document.getElementById("editEspecialista");
const editServicio = document.getElementById("editServicio");
const editDuracion = document.getElementById("editDuracion");

let currentPage = 1;
let currentFilters = {};

/**
 * Fetches bookings from the API based on page and filters.
 *
 * @param {number} page - Page number to fetch.
 * @param {object} filters - Filter criteria.
 */
const fetchBookings = async (page = 1, filters = {}) => {
  try {
    const params = new URLSearchParams({
      page: page,
      limit: 10,
      ...filters,
    });

    const response = await fetch(`/admin/api/reservas?${params}`);
    const data = await response.json();

    if (data.success) {
      renderBookingsTable(bookingsTableContainer, data.reservas, currentFilters);
      renderPagination(paginationContainer, data.page, data.totalPages, handlePageChange);
      attachDeleteHandlers();
      attachEditHandlers();
      attachSortHandlers();
    } else {
      showError(bookingsTableContainer, "Error al cargar las reservas");
    }
  } catch (error) {
    console.error("Error fetching bookings:", error);
    showError(bookingsTableContainer, "Error al conectar con el servidor");
  }
};

/**
 * Handles page change in pagination.
 *
 * @param {number} page - New page number.
 */
const handlePageChange = (page) => {
  currentPage = page;
  fetchBookings(page, currentFilters);
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
    const response = await fetch(`/admin/api/reservas/${bookingId}`, {
      method: "DELETE",
    });

    const data = await response.json();

    if (data.success) {
      showSuccess("Reserva eliminada correctamente");
      fetchBookings(currentPage, currentFilters);
    } else {
      showError(bookingsTableContainer, data.error || "Error al eliminar la reserva");
    }
  } catch (error) {
    console.error("Error deleting booking:", error);
    showError(bookingsTableContainer, "Error al conectar con el servidor");
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
    const response = await fetch(`/admin/api/reservas/${bookingId}`);
    const data = await response.json();

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
    const response = await fetch("/admin/api/reservas", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (result.success) {
      showSuccess("Reserva creada correctamente");
      bootstrap.Modal.getInstance(createBookingModal).hide();
      e.target.reset();
      fetchBookings(currentPage, currentFilters);
    } else {
      showError(bookingsTableContainer, result.error || "Error al crear la reserva");
    }
  } catch (error) {
    console.error("Error creating booking:", error);
    showError(bookingsTableContainer, "Error al conectar con el servidor");
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
    const response = await fetch(`/admin/api/reservas/${bookingId}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (result.success) {
      showSuccess("Reserva actualizada correctamente");
      bootstrap.Modal.getInstance(editBookingModal).hide();
      fetchBookings(currentPage, currentFilters);
    } else {
      showError(bookingsTableContainer, result.error || "Error al actualizar la reserva");
    }
  } catch (error) {
    console.error("Error updating booking:", error);
    showError(bookingsTableContainer, "Error al conectar con el servidor");
  }
};

/**
 * Attaches delete event listeners to buttons.
 */
const attachDeleteHandlers = () => {
  document.querySelectorAll(".btn-delete-booking").forEach((btn) => {
    btn.addEventListener("click", handleDeleteBooking);
  });
};

/**
 * Attaches edit event listeners to buttons.
 */
const attachEditHandlers = () => {
  document.querySelectorAll(".btn-edit-booking").forEach((btn) => {
    btn.addEventListener("click", handleEditBooking);
  });
};

/**
 * Attaches sort event listeners to table headers.
 */
const attachSortHandlers = () => {
  document.querySelectorAll(".sort-link").forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      const sortField = e.currentTarget.dataset.sort;

      if (currentFilters.sort === sortField) {
        currentFilters.order = currentFilters.order === "asc" ? "desc" : "asc";
      } else {
        currentFilters.sort = sortField;
        currentFilters.order = "asc";
      }

      fetchBookings(1, currentFilters);
      updateUrlParams(currentFilters);
      updatePdfExportLink(new URLSearchParams(currentFilters));
    });
  });
};

/**
 * Handles apply filters button click.
 */
const handleApplyFilters = () => {
  const filterInputs = {
    cliente: filterCliente,
    especialista: filterEspecialista,
    estado: filterEstado,
    fecha_desde: filterFechaDesde,
    fecha_hasta: filterFechaHasta,
  };

  const filters = buildFiltersFromInputs(filterInputs);

  if (currentFilters.sort) filters.sort = currentFilters.sort;
  if (currentFilters.order) filters.order = currentFilters.order;

  currentFilters = filters;
  fetchBookings(1, filters);
  updateUrlParams(filters);
  updatePdfExportLink(new URLSearchParams(filters));
};

/**
 * Handles clear filters button click.
 */
const handleClearFilters = () => {
  const filterInputs = {
    cliente: filterCliente,
    especialista: filterEspecialista,
    estado: filterEstado,
    fecha_desde: filterFechaDesde,
    fecha_hasta: filterFechaHasta,
  };

  clearFilterInputs(filterInputs);

  currentFilters = {};
  fetchBookings(1, {});
  window.history.pushState({}, "", window.location.pathname);
  updatePdfExportLink(new URLSearchParams());
};

/**
 * Initializes the bookings manager.
 */
const init = () => {
  attachDeleteHandlers();
  attachEditHandlers();

  const fields = [
    "cliente",
    "especialista",
    "estado",
    "fecha_desde",
    "fecha_hasta",
    "sort",
    "order",
  ];

  currentFilters = loadFiltersFromUrl(fields);
};

btnApplyFilters.addEventListener("click", handleApplyFilters);
btnClearFilters.addEventListener("click", handleClearFilters);
createBookingForm.addEventListener("submit", handleCreateBooking);
editBookingForm.addEventListener("submit", handleUpdateBooking);

init();
