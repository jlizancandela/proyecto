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

// Cache for loaded data to avoid multiple API calls
let cachedClients = null;
let cachedSpecialists = null;
let cachedServices = null;

/**
 * Loads clients into select dropdown
 *
 * @param {string} selectId - ID of the select element
 * @return {Promise<void>}
 */
const loadClients = async (selectId) => {
  const select = document.getElementById(selectId);
  const currentValue = select.value;

  // Show loading
  select.innerHTML = '<option value="">Cargando clientes...</option>';
  select.disabled = true;

  try {
    const response = await fetch("/admin/api/users?limit=1000");
    const data = await response.json();

    if (data.success) {
      select.innerHTML = '<option value="">Selecciona un cliente...</option>';

      data.users.forEach((user) => {
        const option = document.createElement("option");
        option.value = user.id;
        option.textContent = `${user.nombre} ${user.apellidos} (${user.email})`;
        select.appendChild(option);
      });

      if (currentValue) select.value = currentValue;
    }
  } catch (error) {
    console.error("Error loading clients:", error);
    select.innerHTML = '<option value="">Error al cargar</option>';
  } finally {
    select.disabled = false;
  }
};

/**
 * Loads specialists into select dropdown
 *
 * @param {string} selectId - ID of the select element
 * @return {Promise<void>}
 */
const loadSpecialists = async (selectId) => {
  const select = document.getElementById(selectId);
  const currentValue = select.value;

  select.innerHTML = '<option value="">Cargando especialistas...</option>';
  select.disabled = true;

  try {
    const response = await fetch("/admin/api/especialistas");
    const data = await response.json();

    if (data.success) {
      select.innerHTML = '<option value="">Selecciona un especialista...</option>';

      data.especialistas.forEach((especialista) => {
        const option = document.createElement("option");
        option.value = especialista.id;
        option.textContent = `${especialista.nombre} ${especialista.apellidos}`;
        select.appendChild(option);
      });

      if (currentValue) select.value = currentValue;
    }
  } catch (error) {
    console.error("Error loading specialists:", error);
    select.innerHTML = '<option value="">Error al cargar</option>';
  } finally {
    select.disabled = false;
  }
};

/**
 * Loads services into select dropdown
 *
 * @param {string} selectId - ID of the select element
 * @return {Promise<void>}
 */
const loadServices = async (selectId) => {
  const select = document.getElementById(selectId);
  const currentValue = select.value;

  select.innerHTML = '<option value="">Cargando servicios...</option>';
  select.disabled = true;

  try {
    const response = await fetch("/admin/api/services");
    const data = await response.json();

    if (data.success) {
      select.innerHTML = '<option value="">Selecciona un servicio...</option>';

      data.servicios.forEach((service) => {
        const option = document.createElement("option");
        option.value = service.id;
        option.textContent = `${service.nombre_servicio} (${service.duracion_minutos} min - €${service.precio})`;
        select.appendChild(option);
      });

      if (currentValue) select.value = currentValue;
    }
  } catch (error) {
    console.error("Error loading services:", error);
    select.innerHTML = '<option value="">Error al cargar</option>';
  } finally {
    select.disabled = false;
  }
};

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
                class="btn btn-sm btn-outline-primary btn-edit-booking"
                data-booking-id="${booking.id_reserva}"
                title="Editar"
              >
                <i class="bi bi-pencil"></i>
              </button>
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
  attachEditHandlers();
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
 * Attaches edit handlers to buttons
 *
 * @return {void}
 */
const attachEditHandlers = () => {
  document.querySelectorAll(".btn-edit-booking").forEach((btn) => {
    btn.addEventListener("click", handleEditBooking);
  });
};

/**
 * Handles edit booking button click
 *
 * @param {Event} e - Click event
 * @return {Promise<void>}
 */
const handleEditBooking = async (e) => {
  const bookingId = e.currentTarget.dataset.bookingId;

  try {
    // Fetch booking data
    const response = await fetch(`/admin/api/reservas/${bookingId}`);
    const data = await response.json();

    if (data.success) {
      const booking = data.data;

      // Load all selectors first (in parallel)
      await Promise.all([
        loadClients("editCliente"),
        loadSpecialists("editEspecialista"),
        loadServices("editServicio"),
      ]);

      // Now populate form with booking data
      document.getElementById("editBookingId").value = booking.id_reserva;
      document.getElementById("editFecha").value = booking.fecha_reserva;
      document.getElementById("editHora").value = booking.hora_inicio;
      document.getElementById("editEstado").value = booking.estado;
      document.getElementById("editObservaciones").value = booking.observaciones || "";

      // Debug: Log the booking data
      console.log("Booking data:", booking);
      console.log("Cliente ID from booking:", booking.id_cliente);
      console.log("Especialista ID from booking:", booking.id_especialista);
      console.log("Servicio ID from booking:", booking.id_servicio);

      // Set select values with a small delay to ensure DOM is ready
      setTimeout(() => {
        const clienteSelect = document.getElementById("editCliente");
        const especialistaSelect = document.getElementById("editEspecialista");
        const servicioSelect = document.getElementById("editServicio");

        console.log(
          "Cliente select options:",
          Array.from(clienteSelect.options).map((o) => ({ value: o.value, text: o.text }))
        );

        clienteSelect.value = booking.id_cliente;
        especialistaSelect.value = booking.id_especialista;
        servicioSelect.value = booking.id_servicio;

        console.log("Cliente selected value:", clienteSelect.value);
        console.log("Especialista selected value:", especialistaSelect.value);
        console.log("Servicio selected value:", servicioSelect.value);
      }, 100);

      // Calculate duration from hora_inicio and hora_fin
      const inicio = new Date(`2000-01-01T${booking.hora_inicio}`);
      const fin = new Date(`2000-01-01T${booking.hora_fin}`);
      const duracion = (fin - inicio) / (1000 * 60);
      document.getElementById("editDuracion").value = duracion;

      // Show modal after everything is loaded
      const modal = new bootstrap.Modal(document.getElementById("editBookingModal"));
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
 * Handles create booking form submission
 *
 * @param {Event} e - Submit event
 * @return {Promise<void>}
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
      bootstrap.Modal.getInstance(document.getElementById("createBookingModal")).hide();
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
 * Handles edit booking form submission
 *
 * @param {Event} e - Submit event
 * @return {Promise<void>}
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
      bootstrap.Modal.getInstance(document.getElementById("editBookingModal")).hide();
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

// Form handlers
document.getElementById("createBookingForm").addEventListener("submit", handleCreateBooking);
document.getElementById("editBookingForm").addEventListener("submit", handleUpdateBooking);

// Load data when create modal is shown
document.getElementById("createBookingModal").addEventListener("show.bs.modal", async () => {
  await Promise.all([
    loadClients("createCliente"),
    loadSpecialists("createEspecialista"),
    loadServices("createServicio"),
  ]);
});

// Attach handlers to existing table rows (server-rendered)
document.querySelectorAll(".btn-delete-booking").forEach((btn) => {
  btn.addEventListener("click", handleDeleteBooking);
});

document.querySelectorAll(".btn-edit-booking").forEach((btn) => {
  btn.addEventListener("click", handleEditBooking);
});

// Filter handlers
btnApplyFilters.addEventListener("click", () => {
  const filters = {
    cliente: filterCliente.value,
    especialista: filterEspecialista.value,
    estado: filterEstado.value,
    fecha_desde: filterFechaDesde.value,
    fecha_hasta: filterFechaHasta.value,
  };

  // Remove empty filters
  Object.keys(filters).forEach((key) => {
    if (!filters[key]) delete filters[key];
  });

  currentFilters = filters;
  fetchBookings(1, filters);

  // Update URL parameters without reloading
  const params = new URLSearchParams(filters);
  const newUrl = `${window.location.pathname}?${params.toString()}`;
  window.history.pushState({}, "", newUrl);

  // Update PDF Export Link dinamically
  updatePdfExportLink(params);
});

btnClearFilters.addEventListener("click", () => {
  filterCliente.value = "";
  filterEspecialista.value = "";
  filterEstado.value = "";
  filterFechaDesde.value = "";
  filterFechaHasta.value = "";

  currentFilters = {};
  fetchBookings(1, {});

  // Clean URL params
  window.history.pushState({}, "", window.location.pathname);

  // Reset PDF Export Link
  updatePdfExportLink(new URLSearchParams());
});

const updatePdfExportLink = (params) => {
  const pdfLink = document.querySelector('a[href^="/admin/bookings/pdf"]');
  if (pdfLink) {
    pdfLink.href = `/admin/bookings/pdf?${params.toString()}`;
  }
};

// Populate filter selectors on page load
(async () => {
  try {
    await Promise.all([loadClients("filterCliente"), loadSpecialists("filterEspecialista")]);

    // Set filter values from URL if present
    const urlParams = new URLSearchParams(window.location.search);
    const clienteParam = urlParams.get("cliente");
    const especialistaParam = urlParams.get("especialista");
    const estadoParam = urlParams.get("estado");
    const fechaDesdeParam = urlParams.get("fecha_desde");
    const fechaHastaParam = urlParams.get("fecha_hasta");
    const sortParam = urlParams.get("sort");
    const orderParam = urlParams.get("order");

    if (clienteParam) {
      document.getElementById("filterCliente").value = clienteParam;
      currentFilters.cliente = clienteParam;
    }
    if (especialistaParam) {
      document.getElementById("filterEspecialista").value = especialistaParam;
      currentFilters.especialista = especialistaParam;
    }
    if (estadoParam) {
      document.getElementById("filterEstado").value = estadoParam;
      currentFilters.estado = estadoParam;
    }
    if (fechaDesdeParam) {
      document.getElementById("filterFechaDesde").value = fechaDesdeParam;
      currentFilters.fecha_desde = fechaDesdeParam;
    }
    if (fechaHastaParam) {
      document.getElementById("filterFechaHasta").value = fechaHastaParam;
      currentFilters.fecha_hasta = fechaHastaParam;
    }

    // Persist sort/order filters if present
    if (sortParam) currentFilters.sort = sortParam;
    if (orderParam) currentFilters.order = orderParam;
  } catch (error) {
    console.error("Error loading filter selectors:", error);
  }
})();
