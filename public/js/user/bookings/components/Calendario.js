/**
 * Calendario Component
 *
 * Interactive calendar component for date selection in booking.
 * Displays current month with navigation to previous/next months.
 * Disables past dates and Sundays (non-working days).
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

const DAYS_OF_WEEK = ["L", "M", "X", "J", "V", "S", "D"];
const CALENDAR_WIDTH = "380px";
const CELL_WIDTH = "40px";
const CELL_HEIGHT = "40px";
const FIRST_DAY_OF_WEEK = 7;

/**
 * Renders a calendar component for date selection.
 * @param {Object} props - Component props.
 * @param {Date} props.fecha - Current date object.
 * @param {Date} props.diaSeleccionado - Selected day date.
 * @param {Function} props.handleDiaChange - Callback for day selection.
 * @param {Function} props.handleMesChange - Callback for month change.
 * @returns {Object} Preact component.
 */
export const Calendario = ({ fecha, diaSeleccionado, handleDiaChange, handleMesChange }) => {
  if (!fecha || !handleDiaChange || !handleMesChange) return null;

  const year = fecha.getFullYear();
  const month = fecha.getMonth();
  const textMonth = fecha.toLocaleString("es-ES", { month: "long" });
  const textYear = year;
  const diasTotalesMes = new Date(year, month + 1, 0).getDate();
  const primerDiaMes = (new Date(year, month, 1).getDay() || FIRST_DAY_OF_WEEK) - 1;

  const calendarioCompleto = buildCalendarDays(primerDiaMes, diasTotalesMes);

  return html`
    <div
      class="card border-0 shadow-sm rounded-4 p-4 bg-white"
      style="width: ${CALENDAR_WIDTH}; margin: 0 auto;"
    >
      ${renderNavigationButtons(year, month, textMonth, textYear, handleMesChange)}
      <div class="row g-0 text-center small text-primary fw-bold mb-2">
        ${DAYS_OF_WEEK.map(
          (d) =>
            html`
              <div class="col">${d}</div>
            `
        )}
      </div>
      ${renderWeeks(calendarioCompleto, year, month, diaSeleccionado, handleDiaChange)}
    </div>
  `;
};

/**
 * Builds calendar days array with null values for empty cells.
 * @param {number} primerDiaMes - First day of month position.
 * @param {number} diasTotalesMes - Total days in month.
 * @returns {Array} Calendar days array.
 */
const buildCalendarDays = (primerDiaMes, diasTotalesMes) => {
  const out = [];
  for (let i = 0; i < primerDiaMes; i++) out.push(null);
  for (let i = 1; i <= diasTotalesMes; i++) out.push(i);
  while (out.length % 7 !== 0) out.push(null);
  return out;
};

/**
 * Renders calendar navigation buttons.
 * @param {number} year - Current year.
 * @param {number} month - Current month.
 * @param {string} textMonth - Month name in Spanish.
 * @param {number} textYear - Year value.
 * @param {Function} handleMesChange - Callback for month change.
 * @returns {Object} Preact component.
 */
const renderNavigationButtons = (year, month, textMonth, textYear, handleMesChange) => {
  const today = new Date();
  const isPreviousDisabled = isPreviousMonthDisabled(year, month, today);
  const prev = () => handleMesChange(new Date(year, month - 1));
  const next = () => handleMesChange(new Date(year, month + 1));

  return html`
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
      <button
        class="btn btn-sm btn-link text-decoration-none text-dark p-0"
        disabled=${isPreviousDisabled}
        onClick=${prev}
      >
        <i class="bi bi-chevron-left"></i>
      </button>
      <div class="fw-bold text-capitalize fs-5">${textMonth} ${textYear}</div>
      <button class="btn btn-sm btn-link text-decoration-none text-dark p-0" onClick=${next}>
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  `;
};

/**
 * Checks if previous month navigation should be disabled.
 * @param {number} year - Current year.
 * @param {number} month - Current month.
 * @param {Date} today - Today's date.
 * @returns {boolean} True if previous month should be disabled.
 */
const isPreviousMonthDisabled = (year, month, today) => {
  if (year < today.getFullYear()) return true;
  if (year === today.getFullYear() && month <= today.getMonth()) return true;
  return false;
};

/**
 * Renders calendar weeks.
 * @param {Array} calendarioCompleto - Complete calendar days array.
 * @param {number} year - Current year.
 * @param {number} month - Current month.
 * @param {Date} diaSeleccionado - Selected day date.
 * @param {Function} handleDiaChange - Callback for day selection.
 * @returns {Object} Preact component.
 */
const renderWeeks = (calendarioCompleto, year, month, diaSeleccionado, handleDiaChange) => {
  const weeks = [];
  for (let i = 0; i < calendarioCompleto.length; i += 7) {
    weeks.push(calendarioCompleto.slice(i, i + 7));
  }

  return weeks.map(
    (week) => html`
      <div class="row g-0 text-center mb-2">
        ${week.map(
          (d) =>
            html`
              <div class="col d-flex justify-content-center">
                ${renderCell(d, year, month, diaSeleccionado, handleDiaChange)}
              </div>
            `
        )}
      </div>
    `
  );
};

/**
 * Renders a single calendar cell day.
 * @param {number} dia - Day number.
 * @param {number} year - Current year.
 * @param {number} month - Current month.
 * @param {Date} diaSeleccionado - Selected day date.
 * @param {Function} handleDiaChange - Callback for day selection.
 * @returns {Object} Preact component.
 */
const renderCell = (dia, year, month, diaSeleccionado, handleDiaChange) => {
  if (!dia)
    return html`
      <div style="width: ${CELL_WIDTH}; height: ${CELL_HEIGHT};"></div>
    `;

  const isSelected = isDaySelected(dia, month, year, diaSeleccionado);
  const isPast = isDayInPast(dia, month, year);
  const isSunday = new Date(year, month, dia).getDay() === 0;

  if (isPast || isSunday) {
    return html`
      <span
        class="d-flex align-items-center justify-content-center text-secondary"
        style="width: ${CELL_WIDTH}; height: ${CELL_HEIGHT};"
      >
        ${dia}
      </span>
    `;
  }

  if (isSelected) {
    return html`
      <button
        type="button"
        class="btn rounded-circle d-flex align-items-center justify-content-center bg-primary text-white border-0"
        style="width: ${CELL_WIDTH}; height: ${CELL_HEIGHT};"
        onClick=${() => handleDiaChange(new Date(year, month, dia))}
      >
        ${dia}
      </button>
    `;
  }

  return html`
    <button
      type="button"
      class="btn rounded-circle d-flex align-items-center justify-content-center text-black border-0"
      style="width: ${CELL_WIDTH}; height: ${CELL_HEIGHT};"
      onClick=${() => handleDiaChange(new Date(year, month, dia))}
    >
      ${dia}
    </button>
  `;
};

/**
 * Checks if a specific day is selected.
 * @param {number} dia - Day number.
 * @param {number} month - Month number.
 * @param {number} year - Year number.
 * @param {Date} diaSeleccionado - Selected day date.
 * @returns {boolean} True if day is selected.
 */
const isDaySelected = (dia, month, year, diaSeleccionado) => {
  if (!diaSeleccionado) return false;
  return (
    dia === diaSeleccionado.getDate() &&
    month === diaSeleccionado.getMonth() &&
    year === diaSeleccionado.getFullYear()
  );
};

/**
 * Checks if a specific day is in the past.
 * @param {number} dia - Day number.
 * @param {number} month - Month number.
 * @param {number} year - Year number.
 * @returns {boolean} True if day is in the past.
 */
const isDayInPast = (dia, month, year) => {
  const fecha = new Date(year, month, dia);
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  return fecha < hoy;
};
