/**
 * Admin bookings management
 */

// DOM elements
const filterEstado = document.getElementById("filterEstado");
const filterCliente = document.getElementById("filterCliente");
const filterEspecialista = document.getElementById("filterEspecialista");
const filterFechaDesde = document.getElementById("filterFechaDesde");
const filterFechaHasta = document.getElementById("filterFechaHasta");
const btnApplyFilters = document.getElementById("btnApplyFilters");
const btnClearFilters = document.getElementById("btnClearFilters");
const bookingsTableContainer = document.getElementById("bookingsTableContainer");
const paginationContainer = document.getElementById("paginationContainer");

let currentPage = 1;
let currentFilters = {};

/**
 * Fetches bookings from API
 *
 * @param {number} page - Page number
 * @param {object} filters - Filter parameters
 * @return {Promise<void>}
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
      renderPagination(data.page, data.totalPages, filters);
    } else {
      showError("Error al cargar las reservas");
    }
  } catch (error) {
    console.error("Error fetching bookings:", error);
    showError("Error al conectar con el servidor");
  }
};

/**
 * Renders bookings table
 *
 * @param {Array} bookings - Array of bookings
 * @return {void}
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

  const tableHTML = `
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Especialista</th>
          <th>Servicio</th>
          <th>Fecha</th>
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
                class="btn btn-sm btn-outline-danger btn-delete-booking"
                data-booking-id="${booking.id_reserva}"
                title="Eliminar"
              >
                <i class="bi bi-trash"></i>
              </button>
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
};

/**
 * Renders pagination
 *
 * @param {number} currentPage - Current page number
 * @param {number} totalPages - Total pages
 * @param {object} filters - Current filters
 * @return {void}
 */
const renderPagination = (currentPage, totalPages, filters) => {
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

  // Attach pagination handlers
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

/**
 * Attaches delete handlers to buttons
 *
 * @return {void}
 */
const attachDeleteHandlers = () => {
  document.querySelectorAll(".btn-delete-booking").forEach((btn) => {
    btn.addEventListener("click", handleDeleteBooking);
  });
};

/**
 * Handles booking deletion
 *
 * @param {Event} e - Click event
 * @return {Promise<void>}
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
 * Gets bootstrap color for status
 *
 * @param {string} status - Booking status
 * @return {string} Bootstrap color class
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
 * Formats date to readable format
 *
 * @param {string} dateStr - Date string
 * @return {string} Formatted date
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
 * Shows error message
 *
 * @param {string} message - Error message
 * @return {void}
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
 * Shows success message
 *
 * @param {string} message - Success message
 * @return {void}
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

// Event handlers
btnApplyFilters.addEventListener("click", () => {
  currentFilters = {};

  if (filterEstado.value) currentFilters.estado = filterEstado.value;
  if (filterCliente.value) currentFilters.cliente = filterCliente.value;
  if (filterEspecialista.value) currentFilters.especialista = filterEspecialista.value;
  if (filterFechaDesde.value) currentFilters.fecha_desde = filterFechaDesde.value;
  if (filterFechaHasta.value) currentFilters.fecha_hasta = filterFechaHasta.value;

  currentPage = 1;
  fetchBookings(currentPage, currentFilters);
});

btnClearFilters.addEventListener("click", () => {
  filterEstado.value = "";
  filterCliente.value = "";
  filterEspecialista.value = "";
  filterFechaDesde.value = "";
  filterFechaHasta.value = "";

  currentFilters = {};
  currentPage = 1;
  fetchBookings(currentPage, currentFilters);
});

// Initial load
fetchBookings(currentPage, currentFilters);
