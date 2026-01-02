/**
 * @file Date and specialist selection form component.
 */

import { h } from "preact";
import { useStore } from "@nanostores/preact";
import { Calendario } from "../components/Calendario.jsx";
import { EspecialistasList } from "../components/EspecialistasList.jsx";
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

/**
 * Renders the date and specialist selection form
 * @returns {import("preact").VNode} Date form component
 */
export const DateForm = () => {
  const booking = useStore($bookingDraft);
  const especialistas = useStore($especialistas);
  const pagination = useStore($pagination);
  const mes = useStore($mes);

  return (
    <div class="row g-4">
      <div class="col-12 col-lg-5">
        <div>
          <div>
            <div class="d-flex justify-content-center">
              <Calendario
                fecha={mes}
                diaSeleccionado={booking.dia}
                handleMesChange={setMesAction}
                handleDiaChange={setDiaAction}
              />
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-7">
        <div>
          <div>
            <EspecialistasList
              especialistas={especialistas}
              onSelectHora={selectEspecialistaAction}
              selectedEspecialista={booking.especialista}
              selectedHora={booking.hora}
              currentPage={pagination.current}
              totalPages={pagination.totalPages}
              onPageChange={changePageAction}
              diaSeleccionado={booking.dia}
            />
          </div>
        </div>
      </div>
    </div>
  );
};
