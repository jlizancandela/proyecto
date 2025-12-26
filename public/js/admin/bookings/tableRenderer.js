// Handles rendering of the bookings table with sorting capabilities.

import { getStatusColor, formatDate } from "./uiHelpers.js";

/**
 * Gets the sort icon HTML based on current sort state.
 *
 * @param {string} field - Field name to check.
 * @param {object} currentFilters - Current filter state including sort and order.
 * @return {string} HTML string for sort icon.
 */
const getSortIcon = (field, currentFilters) => {
  if (currentFilters.sort !== field) {
    return '<i class="bi bi-arrow-down-up text-muted"></i>';
  }
  return currentFilters.order === "asc"
    ? '<i class="bi bi-caret-up-fill"></i>'
    : '<i class="bi bi-caret-down-fill"></i>';
};

/**
 * Renders the bookings table with the provided data.
 *
 * @param {HTMLElement} container - Container element for the table.
 * @param {Array} bookings - Array of booking objects.
 * @param {object} currentFilters - Current filter state.
 * @return {string} HTML string for the table.
 */
export const renderBookingsTable = (container, bookings, currentFilters) => {
  if (bookings.length === 0) {
    container.innerHTML = `
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
          <th>
            <a href="#" class="text-decoration-none text-dark sort-link" data-sort="cliente">
              Cliente ${getSortIcon("cliente", currentFilters)}
            </a>
          </th>
          <th>
            <a href="#" class="text-decoration-none text-dark sort-link" data-sort="especialista">
              Especialista ${getSortIcon("especialista", currentFilters)}
            </a>
          </th>
          <th>Servicio</th>
          <th>
            <a href="#" class="text-decoration-none text-dark sort-link" data-sort="fecha">
              Fecha ${getSortIcon("fecha", currentFilters)}
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

  container.innerHTML = tableHTML;
};
