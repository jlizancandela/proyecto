/**
 * ========================================
 * DATE FORM - Componente Presentacional
 * ========================================
 *
 * Componente puramente presentacional para la selección de fecha y especialista.
 * Sin useEffect, sin cálculos de paginación, sin lógica de negocio.
 *
 * Responsabilidades:
 * - Mostrar el calendario y la lista de especialistas
 * - Conectar componentes con las acciones de la store
 * - Renderizar la UI de forma reactiva
 *
 * IMPORTANTE: La carga de especialistas se dispara automáticamente
 * desde setDiaAction en la store, NO desde un useEffect aquí.
 */

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
  goToConfirmationAction,
} from "../context/bookingsStore.js";

const html = htm.bind(h);

/**
 * Formulario de selección de fecha y especialista
 * Componente presentacional puro
 */
export const DateForm = () => {
  // Suscribirse a los stores necesarios
  const booking = useStore($bookingDraft);
  const especialistas = useStore($especialistas);
  const pagination = useStore($pagination);
  const mes = useStore($mes);

  return html`
    <div class="row g-4">
      <!-- Columna del Calendario -->
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

      <!-- Columna de Especialistas -->
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
