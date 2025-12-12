import { h } from "https://esm.sh/preact@10.19.3";
import { useEffect } from "https://esm.sh/preact@10.19.3/hooks";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import htm from "https://esm.sh/htm";
import { Calendario } from "../components/Calendario.js";
import { EspecialistasList } from "../components/EspecialistasList.js";
import {
  $dia,
  $mes,
  $especialistas,
  $selectedService,
  $selectedEspecialista,
  $selectedHora,
  $estado,
  loadEspecialistasDisponibles,
  selectEspecialistaYHora,
} from "../context/bookingsContext.js";

const html = htm.bind(h);

export const DateForm = () => {
  const dia = useStore($dia);
  const mes = useStore($mes);
  const especialistas = useStore($especialistas);
  const selectedService = useStore($selectedService);
  const selectedEspecialista = useStore($selectedEspecialista);
  const selectedHora = useStore($selectedHora);

  useEffect(() => {
    if (selectedService && dia) {
      loadEspecialistasDisponibles();
    }
  }, [dia, selectedService]);

  const handleDiaChange = (nuevoDia) => {
    $dia.set(nuevoDia);
  };

  const handleMesChange = (nuevoMes) => {
    $mes.set(nuevoMes);
  };

  const handleSelectHora = (especialista, hora) => {
    selectEspecialistaYHora(especialista, hora);
  };

  const handleConfirmar = () => {
    if (selectedEspecialista && selectedHora) {
      $estado.set("ConfirmationForm");
    }
  };

  return html`
    <div class="row g-4">
      <div class="col-12 col-lg-5">
        <div>
          <div>
            <div class="d-flex justify-content-center">
              <${Calendario}
                fecha=${mes}
                diaSeleccionado=${dia}
                handleMesChange=${handleMesChange}
                handleDiaChange=${handleDiaChange}
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
              onSelectHora=${handleSelectHora}
              selectedEspecialista=${selectedEspecialista}
              selectedHora=${selectedHora}
            />
          </div>
          <div class="d-flex justify-content-center mt-4">
            <button
              class="btn btn-primary btn-lg rounded-pill px-5"
              onclick=${handleConfirmar}
              disabled=${!selectedEspecialista || !selectedHora}
            >
              Confirmar Reserva
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
};
