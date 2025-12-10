import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

export function Calendario({
  fecha = new Date(),
  diaSeleccionado = new Date(),
  handleDiaChange,
  handleMesChange,
}) {
  const diasSemana = ["L", "M", "X", "J", "V", "S", "D"];

  const year = fecha.getFullYear();
  const month = fecha.getMonth();

  const textMonth = fecha.toLocaleString("es-ES", { month: "long" });
  const textYear = year;

  const diasTotalesMes = new Date(year, month + 1, 0).getDate();
  const primerDiaMes = (new Date(year, month, 1).getDay() || 7) - 1;

  const calendarioCompleto = [];

  // Espacios vacíos antes del primer día (Lunes = 0, Domingo = 6)
  for (let i = 0; i < primerDiaMes; i++) {
    calendarioCompleto.push(null);
  }

  // Días del mes
  for (let i = 1; i <= diasTotalesMes; i++) {
    calendarioCompleto.push(i);
  }

  // Función para verificar si un día ya pasó
  const esDiaPasado = (dia) => {
    if (!dia) return false;
    const fechaDia = new Date(year, month, dia);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    return fechaDia < hoy;
  };

  return html`
    <div class="card border-0 shadow rounded-4 p-4" style="width: 380px;">
      <h5 class="fw-bold mb-4">Selecciona una fecha</h5>

      <!-- Navegación de mes -->
      <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <button
          class="btn btn-sm border-0"
          disabled=${year < new Date().getFullYear() ||
          (year === new Date().getFullYear() && month <= new Date().getMonth())}
          onClick=${() => handleMesChange(new Date(year, month - 1))}
        >
          <i class="bi bi-chevron-left fw-bold"></i>
        </button>
        <span class="fw-bold text-capitalize">${textMonth} ${textYear}</span>
        <button
          class="btn btn-sm border-0"
          onClick=${() => handleMesChange(new Date(year, month + 1))}
        >
          <i class="bi bi-chevron-right fw-bold"></i>
        </button>
      </div>

      <!-- Grid del calendario -->
      <div class="d-grid gap-2 text-center" style="grid-template-columns: repeat(7, 1fr);">
        <!-- Encabezados de días -->
        ${diasSemana.map(
          (dia, index) => html`
            <div class="${index === 5 || index === 6 ? "text-danger" : ""} small fw-normal">
              ${dia}
            </div>
          `
        )}

        <!-- Días del mes -->
        ${calendarioCompleto.map((dia) => {
          if (!dia) {
            return html`
              <div></div>
            `;
          }

          const esDiaSeleccionado =
            dia === diaSeleccionado.getDate() &&
            month === diaSeleccionado.getMonth() &&
            year === diaSeleccionado.getFullYear();

          const pasado = esDiaPasado(dia);

          if (esDiaSeleccionado) {
            return html`
              <div class="d-flex justify-content-center align-items-center">
                <div
                  class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center shadow-sm"
                  style="width: 36px; height: 36px; cursor: pointer;"
                  onClick=${() => handleDiaChange(new Date(year, month, dia))}
                >
                  ${dia}
                </div>
              </div>
            `;
          }

          return html`
            <div
              class="${pasado ? "text-secondary opacity-50" : ""} py-2"
              style="${pasado ? "cursor: not-allowed;" : "cursor: pointer;"}"
              onClick=${() => !pasado && handleDiaChange(new Date(year, month, dia))}
            >
              ${dia}
            </div>
          `;
        })}
      </div>
    </div>
  `;
}
