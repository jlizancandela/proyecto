import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import {
  $estado,
  $selectedService,
  $selectedEspecialista,
  $selectedHora,
} from "../context/bookingsContext.js";

const html = htm.bind(h);

export const BookingNavigation = () => {
  const estado = useStore($estado);
  const selectedService = useStore($selectedService);
  const selectedEspecialista = useStore($selectedEspecialista);
  const selectedHora = useStore($selectedHora);

  const getStepInfo = (currentState) => {
    switch (currentState) {
      case "ServiceForm":
        return { number: 1, title: "Servicios" };
      case "DateForm":
        return { number: 2, title: "Fecha y Especialista" };
      case "ConfirmationForm":
        return { number: 3, title: "Confirmación" };
      default:
        return { number: 0, title: "" };
    }
  };

  const handlePrev = () => {
    if (estado === "DateForm") $estado.set("ServiceForm");
    if (estado === "ConfirmationForm") $estado.set("DateForm");
  };

  const handleNext = () => {
    if (estado === "ServiceForm") $estado.set("DateForm");
    if (estado === "DateForm") $estado.set("ConfirmationForm");
  };

  const canPrev = estado !== "ServiceForm";
  const canNext =
    (estado === "ServiceForm" && selectedService) ||
    (estado === "DateForm" && selectedEspecialista && selectedHora);

  const stepInfo = getStepInfo(estado);

  return html`
    <div
      class="position-sticky bottom-0 pb-4 d-flex justify-content-center"
      style="z-index: 1000; pointer-events: none;"
    >
      <div
        class="bg-white shadow-lg rounded-pill px-4 py-2 d-flex align-items-center gap-4 border"
        style="pointer-events: auto;"
      >
        <button
          class="btn btn-light rounded-circle text-secondary"
          style="width: 40px; height: 40px;"
          onClick=${handlePrev}
          disabled=${!canPrev}
        >
          <i class="bi bi-chevron-left"></i>
        </button>

        <div class="text-center" style="min-width: 120px;">
          <div
            class="text-xs text-muted fw-bold text-uppercase"
            style="font-size: 0.7rem; letter-spacing: 1px;"
          >
            Paso ${stepInfo.number}/3
          </div>
          <div class="fw-bold fs-6">${stepInfo.title}</div>
        </div>

        ${estado !== "ConfirmationForm"
          ? html`
              <button
                class="btn btn-primary rounded-circle"
                style="width: 40px; height: 40px;"
                onClick=${handleNext}
                disabled=${!canNext}
              >
                <i class="bi bi-chevron-right"></i>
              </button>
            `
          : html`
              <div style="width: 40px;"></div>
            `}
      </div>
    </div>
  `;
};
