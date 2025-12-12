import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { Pagination } from "./Pagination.js";

const html = htm.bind(h);

export const EspecialistasList = ({
  especialistas,
  onSelectHora,
  selectedEspecialista,
  selectedHora,
  currentPage,
  totalPages,
  onPageChange,
}) => {
  if (especialistas.length === 0) {
    return html`
      <div class="text-center py-5">
        <i class="bi bi-calendar-x fs-1 text-muted"></i>
        <p class="text-muted mt-2">No hay especialistas disponibles para esta fecha</p>
      </div>
    `;
  }

  return html`
    <div>
      <h5 class="card-title mb-3">Especialistas disponibles</h5>
      ${especialistas.map(
        (especialista) => html`
          <div class="card mb-3 border">
            <div class="card-body">
              <h6 class="mb-1">${especialista.nombre} ${especialista.apellidos}</h6>
              <p class="text-muted small mb-3">${especialista.descripcion}</p>
              <div>
                <strong class="small">Horas disponibles:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                  ${especialista.horas_disponibles.map((hora) => {
                    const isSelected =
                      selectedEspecialista &&
                      selectedHora &&
                      selectedEspecialista.id_especialista === especialista.id_especialista &&
                      selectedHora === hora;

                    return html`
                      <button
                        class="${isSelected
                          ? "btn btn-primary btn-sm"
                          : "btn btn-outline-primary btn-sm"}"
                        onClick=${() => onSelectHora(especialista, hora)}
                      >
                        <i class="bi bi-clock me-1"></i>
                        ${hora}
                      </button>
                    `;
                  })}
                </div>
              </div>
            </div>
          </div>
        `
      )}

      <${Pagination}
        currentPage=${currentPage}
        totalPages=${totalPages}
        onPageChange=${onPageChange}
      />
    </div>
  `;
};
