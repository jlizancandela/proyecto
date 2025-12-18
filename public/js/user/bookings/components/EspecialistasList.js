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
      <div class="d-flex flex-column gap-3 mb-4">
        ${especialistas.map(
          (especialista) => html`
            <div class="card border border-0 shadow-sm overflow-hidden">
              <div class="row g-0">
                <div class="col-4 col-sm-3 col-md-2 p-0 position-relative">
                  ${especialista.foto_url
                    ? html`
                        <img
                          src="${especialista.foto_url}"
                          alt="${especialista.nombre}"
                          class="img-fluid w-100 h-100"
                          style="object-fit: cover; object-position: top center; min-height: 150px;"
                        />
                      `
                    : html`
                        <div
                          class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-secondary"
                          style="min-height: 150px;"
                        >
                          <i class="bi bi-person-fill fs-1"></i>
                        </div>
                      `}
                </div>
                <div class="col-8 col-sm-9 col-md-10">
                  <div class="card-body">
                    <h5 class="card-title fw-bold">
                      ${especialista.nombre} ${especialista.apellidos}
                    </h5>
                    <p class="card-text text-muted small mb-3">${especialista.descripcion}</p>

                    <div>
                      <strong class="d-block small text-secondary mb-2">
                        Horarios disponibles:
                      </strong>
                      <div class="d-flex flex-wrap gap-2">
                        ${especialista.horas_disponibles.map((hora) => {
                          const isSelected =
                            selectedEspecialista &&
                            selectedHora &&
                            selectedEspecialista.id_especialista === especialista.id_especialista &&
                            selectedHora === hora;

                          return html`
                            <button
                              class="${isSelected
                                ? "btn btn-primary btn-sm px-3"
                                : "btn btn-outline-primary btn-sm px-3"}"
                              onClick=${() => onSelectHora(especialista, hora)}
                            >
                              ${hora}
                            </button>
                          `;
                        })}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `
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
