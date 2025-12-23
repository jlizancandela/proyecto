/**
 * EspecialistasList Component
 *
 * Displays a paginated list of available specialists for a selected service and date.
 * Shows specialist photos, descriptions, and available time slots with past times disabled.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { Pagination } from "./Pagination.js";
import { isPastTime } from "../tools/formatters.js";

const html = htm.bind(h);

const MIN_PHOTO_HEIGHT = "150px";
const PHOTO_OBJECT_FIT = "cover";
const PHOTO_OBJECT_POSITION = "top center";

/**
 * Renders the empty state when no specialists are available.
 * @returns {Object} Preact component.
 */
const renderEmptyState = () =>
  html`
    <div class="text-center py-5">
      <i class="bi bi-calendar-x fs-1 text-muted"></i>
      <p class="text-muted mt-2">No hay especialistas disponibles para esta fecha</p>
    </div>
  `;

/**
 * Renders specialist photo or placeholder.
 * @param {string} fotoUrl - Specialist photo URL.
 * @param {string} nombre - Specialist name.
 * @returns {Object} Preact component.
 */
const renderSpecialistPhoto = (fotoUrl, nombre) => {
  if (fotoUrl) {
    return html`
      <img
        src="${fotoUrl}"
        alt="${nombre}"
        class="img-fluid w-100 h-100"
        style="object-fit: ${PHOTO_OBJECT_FIT}; object-position: ${PHOTO_OBJECT_POSITION}; min-height: ${MIN_PHOTO_HEIGHT};"
      />
    `;
  }

  return html`
    <div
      class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-secondary"
      style="min-height: ${MIN_PHOTO_HEIGHT};"
    >
      <i class="bi bi-person-fill fs-1"></i>
    </div>
  `;
};

/**
 * Renders a time slot button.
 * @param {string} hora - Time slot.
 * @param {boolean} isSelected - Whether the time is selected.
 * @param {boolean} timeHasPassed - Whether the time has passed.
 * @param {Function} onSelectHora - Callback for time selection.
 * @param {Object} especialista - Specialist object.
 * @returns {Object} Preact component.
 */
const renderTimeButton = (hora, isSelected, timeHasPassed, onSelectHora, especialista) => {
  const buttonClass = isSelected
    ? "btn btn-primary btn-sm px-3"
    : timeHasPassed
    ? "btn btn-outline-secondary btn-sm px-3 text-muted"
    : "btn btn-outline-primary btn-sm px-3";

  const buttonStyle = timeHasPassed ? "cursor: not-allowed; opacity: 0.5;" : "";

  return html`
    <button
      class="${buttonClass}"
      onClick=${() => !timeHasPassed && onSelectHora(especialista, hora)}
      disabled=${timeHasPassed}
      title=${timeHasPassed ? "Esta hora ya ha pasado" : ""}
      style="${buttonStyle}"
    >
      ${hora}
      ${timeHasPassed
        ? html`
            <i class="bi bi-lock-fill ms-1 small"></i>
          `
        : ""}
    </button>
  `;
};

/**
 * Renders available time slots for a specialist.
 * @param {Array} horasDisponibles - Available times.
 * @param {Object} selectedEspecialista - Selected specialist.
 * @param {string} selectedHora - Selected time.
 * @param {Date} diaSeleccionado - Selected date.
 * @param {Function} onSelectHora - Callback for time selection.
 * @param {Object} especialista - Specialist object.
 * @returns {Object} Preact component.
 */
const renderAvailableTimes = (
  horasDisponibles,
  selectedEspecialista,
  selectedHora,
  diaSeleccionado,
  onSelectHora,
  especialista
) => {
  return html`
    <div>
      <strong class="d-block small text-secondary mb-2">Horarios disponibles:</strong>
      <div class="d-flex flex-wrap gap-2">
        ${horasDisponibles.map((hora) => {
          const isSelected =
            selectedEspecialista &&
            selectedHora &&
            selectedEspecialista.id_especialista === especialista.id_especialista &&
            selectedHora === hora;

          const timeHasPassed = diaSeleccionado && isPastTime(diaSeleccionado, hora);

          return renderTimeButton(hora, isSelected, timeHasPassed, onSelectHora, especialista);
        })}
      </div>
    </div>
  `;
};

/**
 * Renders a single specialist card.
 * @param {Object} especialista - Specialist data.
 * @param {Object} selectedEspecialista - Selected specialist.
 * @param {string} selectedHora - Selected time.
 * @param {Date} diaSeleccionado - Selected date.
 * @param {Function} onSelectHora - Callback for time selection.
 * @returns {Object} Preact component.
 */
const renderSpecialistCard = (
  especialista,
  selectedEspecialista,
  selectedHora,
  diaSeleccionado,
  onSelectHora
) => {
  return html`
    <div class="card border border-0 shadow-sm overflow-hidden">
      <div class="row g-0">
        <div class="col-4 col-sm-3 col-md-2 p-0 position-relative">
          ${renderSpecialistPhoto(especialista.foto_url, especialista.nombre)}
        </div>
        <div class="col-8 col-sm-9 col-md-10">
          <div class="card-body">
            <h5 class="card-title fw-bold">${especialista.nombre} ${especialista.apellidos}</h5>
            <p class="card-text text-muted small mb-3">${especialista.descripcion}</p>
            ${renderAvailableTimes(
              especialista.horas_disponibles,
              selectedEspecialista,
              selectedHora,
              diaSeleccionado,
              onSelectHora,
              especialista
            )}
          </div>
        </div>
      </div>
    </div>
  `;
};

/**
 * Renders the specialists list component.
 * @param {Object} props - Component props.
 * @param {Array} props.especialistas - List of specialists.
 * @param {Function} props.onSelectHora - Callback for time selection.
 * @param {Object} props.selectedEspecialista - Currently selected specialist.
 * @param {string} props.selectedHora - Currently selected time.
 * @param {number} props.currentPage - Current page number.
 * @param {number} props.totalPages - Total number of pages.
 * @param {Function} props.onPageChange - Callback for page change.
 * @param {Date} props.diaSeleccionado - Selected date.
 * @returns {Object} Preact component.
 */
export const EspecialistasList = ({
  especialistas,
  onSelectHora,
  selectedEspecialista,
  selectedHora,
  currentPage,
  totalPages,
  onPageChange,
  diaSeleccionado,
}) => {
  if (especialistas.length === 0) {
    return renderEmptyState();
  }

  return html`
    <div>
      <h5 class="card-title mb-3">Especialistas disponibles</h5>
      <div class="d-flex flex-column gap-3 mb-4">
        ${especialistas.map((especialista) =>
          renderSpecialistCard(
            especialista,
            selectedEspecialista,
            selectedHora,
            diaSeleccionado,
            onSelectHora
          )
        )}
      </div>

      <${Pagination}
        currentPage=${currentPage}
        totalPages=${totalPages}
        onPageChange=${onPageChange}
      />
    </div>
  `;
};
