// Presentational component for date and specialist selection, calendar and list display.

import { h } from "https://esm.sh/preact@10.19.3";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { Calendario } from "../components/Calendario.js";
import { EspecialistasList } from "../components/EspecialistasList.js";
import {
  $bookingDraft,
  $especialistas,
  $pagination,
  $mes,
  setMesAction,
  setDiaAction,
  selectEspecialistaAction,
  changePageAction,
} from "../context/bookingsStore.js";

const html = htm.bind(h);

/**
 * Renders the date and specialist selection form
 * @returns {import("preact").VNode} Date form component
 */
export const DateForm = () => {
  const booking = useStore($bookingDraft);
  const especialistas = useStore($especialistas);
  const pagination = useStore($pagination);
  const mes = useStore($mes);

  return html`
    <div class="row g-4">
      <div class="col-12 col-lg-5">
        <div>
          <div>
            <div class="d-flex justify-content-center">
              <${Calendario}
                fecha=${mes}
                diaSeleccionado=${booking.dia}
                handleMesChange=${setMesAction}
                handleDiaChange=${setDiaAction}
              />
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-7">
        <div>
          <div>
            <${EspecialistasList}
              especialistas=${especialistas}
              onSelectHora=${selectEspecialistaAction}
              selectedEspecialista=${booking.especialista}
              selectedHora=${booking.hora}
              currentPage=${pagination.current}
              totalPages=${pagination.totalPages}
              onPageChange=${changePageAction}
              diaSeleccionado=${booking.dia}
            />
          </div>
        </div>
      </div>
    </div>
  `;
};
