/**
 * This script helps managing the bookings in the admin panel.
 * It handles the table list, filters, and modals to edit or delete reservations.
 */

// DOM Elements - Filters
const filterEstado = document.getElementById("filterEstado");
const filterCliente = document.getElementById("filterCliente");
const filterEspecialista = document.getElementById("filterEspecialista");
const filterFechaDesde = document.getElementById("filterFechaDesde");
const filterFechaHasta = document.getElementById("filterFechaHasta");
const btnApplyFilters = document.getElementById("btnApplyFilters");
const btnClearFilters = document.getElementById("btnClearFilters");

// DOM Elements - Table and Pagination
const bookingsTableContainer = document.getElementById("bookingsTableContainer");
const paginationContainer = document.getElementById("paginationContainer");

// DOM Elements - Modals and Forms
const createBookingModal = document.getElementById("createBookingModal");
const editBookingModal = document.getElementById("editBookingModal");
const createBookingForm = document.getElementById("createBookingForm");
const editBookingForm = document.getElementById("editBookingForm");

// DOM Elements - Edit Form Fields
const editBookingId = document.getElementById("editBookingId");
const editFecha = document.getElementById("editFecha");
const editHora = document.getElementById("editHora");
const editEstado = document.getElementById("editEstado");
const editObservaciones = document.getElementById("editObservaciones");
const editCliente = document.getElementById("editCliente");
const editEspecialista = document.getElementById("editEspecialista");
const editServicio = document.getElementById("editServicio");
const editDuracion = document.getElementById("editDuracion");

// State
let currentPage = 1;
let currentFilters = {};

/**
 * Gets bootstrap color class based on booking status.
 *
 * @param {string} status - The booking status.
 * @return {string} Bootstrap color class.
 */
const getStatusColor = (status) => {
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
const formatDate = (dateStr) => {
  const date = new Date(dateStr + "T00:00:00");
  return date.toLocaleDateString("es-ES", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  });
};

/**
 * Updates the URL parameters without reloading the page.
 *
 * @param {object} filters - Current filter parameters.
 */
const updateUrlParams = (filters) => {
  const params = new URLSearchParams(filters);
  const newUrl = `${window.location.pathname}?${params.toString()}`;
  window.history.pushState({}, "", newUrl);
};

/**
 * Updates the PDF export link with current filter parameters.
 *
 * @param {URLSearchParams} params - Current URL parameters.
 */
const updatePdfExportLink = (params) => {
  const pdfLink = document.querySelector('a[href^="/admin/bookings/pdf"]');
  if (pdfLink) {
    pdfLink.href = `/admin/bookings/pdf?${params.toString()}`;
  }
};

/**
 * Shows an error message in the table container.
 *
 * @param {string} message - Error message to display.
 */
const showError = (message) => {
  bookingsTableContainer.innerHTML = `
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
const showSuccess = (message) => {
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
      renderBookingsTable(data.reservas);
      renderPagination(data.page, data.totalPages);
    } else {
      showError("Error al cargar las reservas");
    }
  } catch (error) {
    console.error("Error fetching bookings:", error);
    showError("Error al conectar con el servidor");
  }
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
      showError(data.error || "Error al eliminar la reserva");
    }
  } catch (error) {
    console.error("Error deleting booking:", error);
    showError("Error al conectar con el servidor");
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
      showError(result.error || "Error al crear la reserva");
    }
  } catch (error) {
    console.error("Error creating booking:", error);
    showError("Error al conectar con el servidor");
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
      showError(result.error || "Error al actualizar la reserva");
    }
  } catch (error) {
    console.error("Error updating booking:", error);
    showError("Error al conectar con el servidor");
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
 * Renders the bookings table with the provided data.
 *
 * @param {Array} bookings - Array of booking objects.
 */
const renderBookingsTable = (bookings) => {
  if (bookings.length === 0) {
    bookingsTableContainer.innerHTML = `
      <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        No se encontraron reservas con los filtros aplicados.
      </div>
    `;
    return;
  }

  const getSortIcon = (field) => {
    if (currentFilters.sort !== field) return '<i class="bi bi-arrow-down-up text-muted"></i>';
    return currentFilters.order === "asc"
      ? '<i class="bi bi-caret-up-fill"></i>'
      : '<i class="bi bi-caret-down-fill"></i>';
  };

  const tableHTML = `
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>
            <a href="#" class="text-decoration-none text-dark sort-link" data-sort="cliente">
              Cliente ${getSortIcon("cliente")}
            </a>
          </th>
          <th>
            <a href="#" class="text-decoration-none text-dark sort-link" data-sort="especialista">
              Especialista ${getSortIcon("especialista")}
            </a>
          </th>
          <th>Servicio</th>
          <th>
            <a href="#" class="text-decoration-none text-dark sort-link" data-sort="fecha">
              Fecha ${getSortIcon("fecha")}
            </a>
          </th>
          <th>Hora</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        ${bookings
          .map(
            (booking) => `
          <tr>
            <td>${booking.id_reserva}</td>
            <td>
              <div>${booking.cliente.nombre} ${booking.cliente.apellidos}</div>
              <small class="text-muted">${booking.cliente.email}</small>
            </td>
            <td>
              <div>${booking.especialista.nombre} ${booking.especialista.apellidos}</div>
            </td>
            <td>
              <div>${booking.servicio.nombre}</div>
              <small class="text-muted">${booking.servicio.duracion_minutos} min - €${
              booking.servicio.precio
            }</small>
            </td>
            <td class="text-nowrap">${formatDate(booking.fecha_reserva)}</td>
            <td class="text-nowrap">${booking.hora_inicio} - ${booking.hora_fin}</td>
            <td>
              <span class="badge bg-${getStatusColor(booking.estado)}">
                ${booking.estado}
              </span>
            </td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-sm btn-outline-primary btn-edit-booking"
                data-booking-id="${booking.id_reserva}"
                title="Editar"
              >
                <i class="bi bi-pencil"></i>
              </button>
              ${
                booking.estado !== "Pendiente"
                  ? `<span class="d-inline-block" title="Solo se pueden eliminar reservas pendientes" style="cursor: not-allowed">
                     <button type="button" class="btn btn-sm btn-outline-danger btn-delete-booking" disabled style="pointer-events: none">
                       <i class="bi bi-trash"></i>
                     </button>
                   </span>`
                  : `<button type="button" class="btn btn-sm btn-outline-danger btn-delete-booking" data-booking-id="${booking.id_reserva}" title="Eliminar">
                     <i class="bi bi-trash"></i>
                   </button>`
              }
            </td>
          </tr>
        `
          )
          .join("")}
      </tbody>
    </table>
  `;

  bookingsTableContainer.innerHTML = tableHTML;
  attachDeleteHandlers();
  attachEditHandlers();
  attachSortHandlers();
};

/**
 * Renders the pagination controls.
 *
 * @param {number} currentPage - Current active page.
 * @param {number} totalPages - Total number of pages.
 */
const renderPagination = (currentPage, totalPages) => {
  if (totalPages <= 1) {
    paginationContainer.innerHTML = "";
    return;
  }

  let paginationHTML = '<ul class="pagination justify-content-center">';

  // Previous button
  paginationHTML += `
    <li class="page-item ${currentPage <= 1 ? "disabled" : ""}">
      <a class="page-link" href="#" data-page="${currentPage - 1}">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
  `;

  // Page numbers
  for (let i = 1; i <= totalPages; i++) {
    if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
      paginationHTML += `
        <li class="page-item ${i === currentPage ? "active" : ""}">
          <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>
      `;
    } else if (i === currentPage - 3 || i === currentPage + 3) {
      paginationHTML += `
        <li class="page-item disabled">
          <span class="page-link">...</span>
        </li>
      `;
    }
  }

  // Next button
  paginationHTML += `
    <li class="page-item ${currentPage >= totalPages ? "disabled" : ""}">
      <a class="page-link" href="#" data-page="${currentPage + 1}">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  `;

  paginationHTML += "</ul>";
  paginationContainer.innerHTML = paginationHTML;

  paginationContainer.querySelectorAll("a.page-link").forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      const page = parseInt(e.currentTarget.dataset.page);
      if (page && page !== currentPage) {
        fetchBookings(page, currentFilters);
      }
    });
  });
};

// Event Listeners - Filters
btnApplyFilters.addEventListener("click", () => {
  const filters = {
    cliente: filterCliente.value,
    especialista: filterEspecialista.value,
    estado: filterEstado.value,
    fecha_desde: filterFechaDesde.value,
    fecha_hasta: filterFechaHasta.value,
  };

  Object.keys(filters).forEach((key) => {
    if (!filters[key]) delete filters[key];
  });

  if (currentFilters.sort) filters.sort = currentFilters.sort;
  if (currentFilters.order) filters.order = currentFilters.order;

  currentFilters = filters;
  fetchBookings(1, filters);
  updateUrlParams(filters);
  updatePdfExportLink(new URLSearchParams(filters));
});

btnClearFilters.addEventListener("click", () => {
  filterCliente.value = "";
  filterEspecialista.value = "";
  filterEstado.value = "";
  filterFechaDesde.value = "";
  filterFechaHasta.value = "";

  currentFilters = {};
  fetchBookings(1, {});
  window.history.pushState({}, "", window.location.pathname);
  updatePdfExportLink(new URLSearchParams());
});

// Event Listeners - Forms
createBookingForm.addEventListener("submit", handleCreateBooking);
editBookingForm.addEventListener("submit", handleUpdateBooking);

// Initialization
(() => {
  // Attach handlers to server-rendered rows
  attachDeleteHandlers();
  attachEditHandlers();

  // Load state from URL
  const urlParams = new URLSearchParams(window.location.search);
  const fields = [
    "cliente",
    "especialista",
    "estado",
    "fecha_desde",
    "fecha_hasta",
    "sort",
    "order",
  ];

  fields.forEach((field) => {
    const value = urlParams.get(field);
    if (value) currentFilters[field] = value;
  });
})();
