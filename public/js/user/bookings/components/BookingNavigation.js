/**
 * BookingNavigation Component
 *
 * Displays a sticky navigation bar with step indicator and navigation buttons
 * for multi-step booking form. Shows current step progress and allows users
 * to move between different booking stages.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { useStore } from "https://esm.sh/@nanostores/preact@0.5.1?deps=preact@10.19.3";
import { $estado, $bookingDraft } from "../context/bookingsStore.js";

const html = htm.bind(h);

const BUTTON_SIZE = "40px";
const SPACER_SIZE = "120px";
const Z_INDEX = 1000;

/**
 * Gets step information for the current booking state.
 * @param {string} currentState - The current booking form state.
 * @returns {Object} Object containing step number and title.
 */
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

/**
 * Handles navigation to the previous step.
 * @param {string} estado - Current booking state.
 */
const handlePrevStep = (estado) => {
  if (estado === "DateForm") $estado.set("ServiceForm");
  if (estado === "ConfirmationForm") $estado.set("DateForm");
};

/**
 * Handles navigation to the next step.
 * @param {string} estado - Current booking state.
 */
const handleNextStep = (estado) => {
  if (estado === "ServiceForm") $estado.set("DateForm");
  if (estado === "DateForm") $estado.set("ConfirmationForm");
};

/**
 * Determines if the next button should be enabled.
 * @param {string} estado - Current booking state.
 * @param {Object} booking - Current booking draft data.
 * @returns {boolean} True if next button should be enabled.
 */
const canNavigateNext = (estado, booking) => {
  return (
    (estado === "ServiceForm" && booking.service) ||
    (estado === "DateForm" && booking.especialista && booking.hora)
  );
};

/**
 * Renders the previous navigation button.
 * @param {boolean} canPrev - Whether previous navigation is allowed.
 * @param {Function} handlePrev - Callback for previous button click.
 * @returns {Object} Preact component.
 */
const renderPrevButton = (canPrev, handlePrev) => html`
  <button
    class="btn btn-light rounded-circle text-secondary"
    style="width: ${BUTTON_SIZE}; height: ${BUTTON_SIZE};"
    onClick=${handlePrev}
    disabled=${!canPrev}
  >
    <i class="bi bi-chevron-left"></i>
  </button>
`;

/**
 * Renders the step information display.
 * @param {Object} stepInfo - Step information object.
 * @returns {Object} Preact component.
 */
const renderStepInfo = (stepInfo) => html`
  <div class="text-center" style="min-width: ${SPACER_SIZE};">
    <div
      class="text-xs text-muted fw-bold text-uppercase"
      style="font-size: 0.7rem; letter-spacing: 1px;"
    >
      Paso ${stepInfo.number}/3
    </div>
    <div class="fw-bold fs-6">${stepInfo.title}</div>
  </div>
`;

/**
 * Renders the next navigation button or spacer.
 * @param {string} estado - Current booking state.
 * @param {boolean} canNext - Whether next navigation is allowed.
 * @param {Function} handleNext - Callback for next button click.
 * @returns {Object} Preact component.
 */
const renderNextButtonOrSpacer = (estado, canNext, handleNext) => {
  if (estado !== "ConfirmationForm") {
    return html`
      <button
        class="btn btn-primary rounded-circle"
        style="width: ${BUTTON_SIZE}; height: ${BUTTON_SIZE};"
        onClick=${handleNext}
        disabled=${!canNext}
      >
        <i class="bi bi-chevron-right"></i>
      </button>
    `;
  }

  return html`
    <div style="width: ${BUTTON_SIZE};"></div>
  `;
};

/**
 * Renders the booking navigation component with step indicator and navigation buttons.
 * @returns {Object} Preact component.
 */
export const BookingNavigation = () => {
  const estado = useStore($estado);
  const booking = useStore($bookingDraft);

  const canPrev = estado !== "ServiceForm";
  const canNext = canNavigateNext(estado, booking);
  const stepInfo = getStepInfo(estado);

  return html`
    <div
      class="position-sticky bottom-0 pb-4 d-flex justify-content-center"
      style="z-index: ${Z_INDEX}; pointer-events: none;"
    >
      <div
        class="bg-white shadow-lg rounded-pill px-4 py-2 d-flex align-items-center gap-4 border"
        style="pointer-events: auto;"
      >
        ${renderPrevButton(canPrev, () => handlePrevStep(estado))} ${renderStepInfo(stepInfo)}
        ${renderNextButtonOrSpacer(estado, canNext, () => handleNextStep(estado))}
      </div>
    </div>
  `;
};
