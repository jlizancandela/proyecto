import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

export function Calendario({ fecha, diaSeleccionado, handleDiaChange, handleMesChange }) {
  if (!fecha || !handleDiaChange || !handleMesChange) return null;

  const diasSemana = ["L", "M", "X", "J", "V", "S", "D"];
  const year = fecha.getFullYear();
  const month = fecha.getMonth();
  const textMonth = fecha.toLocaleString("es-ES", { month: "long" });
  const textYear = year;
  const diasTotalesMes = new Date(year, month + 1, 0).getDate();
  const primerDiaMes = (new Date(year, month, 1).getDay() || 7) - 1;

  const calendarioCompleto = buildCalendarDays(primerDiaMes, diasTotalesMes);

  return html`
    <div
      class="card border-0 shadow-sm rounded-4 p-4 bg-white"
      style="width: 380px; margin: 0 auto;"
    >
      ${renderNavigationButtons(year, month, textMonth, textYear, handleMesChange)}
      <div class="row g-0 text-center small text-primary fw-bold mb-2">
        ${diasSemana.map(
          (d) =>
            html`
              <div class="col">${d}</div>
            `
        )}
      </div>
      ${renderWeeks(calendarioCompleto, year, month, diaSeleccionado, handleDiaChange)}
    </div>
  `;
}

function buildCalendarDays(primerDiaMes, diasTotalesMes) {
  const out = [];
  for (let i = 0; i < primerDiaMes; i++) out.push(null);
  for (let i = 1; i <= diasTotalesMes; i++) out.push(i);
  while (out.length % 7 !== 0) out.push(null);
  return out;
}

function renderNavigationButtons(year, month, textMonth, textYear, handleMesChange) {
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
}

function isPreviousMonthDisabled(year, month, today) {
  if (year < today.getFullYear()) return true;
  if (year === today.getFullYear() && month <= today.getMonth()) return true;
  return false;
}

function renderWeeks(calendarioCompleto, year, month, diaSeleccionado, handleDiaChange) {
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
}

function renderCell(dia, year, month, diaSeleccionado, handleDiaChange) {
  if (!dia)
    return html`
      <div style="width: 40px; height: 40px;"></div>
    `;
  const isSelected = isDaySelected(dia, month, year, diaSeleccionado);
  const isPast = isDayInPast(dia, month, year);
  const isSunday = new Date(year, month, dia).getDay() === 0;

  // Deshabilitar días pasados y domingos
  if (isPast || isSunday) {
    return html`
      <span
        class="d-flex align-items-center justify-content-center text-secondary"
        style="width: 40px; height: 40px;"
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
        style="width: 40px; height: 40px;"
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
      style="width: 40px; height: 40px;"
      onClick=${() => handleDiaChange(new Date(year, month, dia))}
    >
      ${dia}
    </button>
  `;
}

function isDaySelected(dia, month, year, diaSeleccionado) {
  if (!diaSeleccionado) return false;
  return (
    dia === diaSeleccionado.getDate() &&
    month === diaSeleccionado.getMonth() &&
    year === diaSeleccionado.getFullYear()
  );
}

function isDayInPast(dia, month, year) {
  const fecha = new Date(year, month, dia);
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  return fecha < hoy;
}
