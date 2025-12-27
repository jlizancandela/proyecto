/**
 * @file Unit tests for booking filters (user bookings page).
 */

import { describe, it, expect, beforeEach, vi } from "vitest";

describe("Booking Filters", () => {
  let estadoInput;
  let filterForm;

  beforeEach(() => {
    document.body.innerHTML = `
      <form id="filterForm">
        <input type="hidden" id="estadoInput" name="estado" value="" />
        <button type="button" data-estado="">Todas</button>
        <button type="button" data-estado="Pendiente">Pendiente</button>
        <button type="button" data-estado="Confirmada">Confirmada</button>
        <button type="button" data-estado="Completada">Completada</button>
        <button type="button" data-estado="Cancelada">Cancelada</button>
      </form>
    `;

    estadoInput = document.getElementById("estadoInput");
    filterForm = document.getElementById("filterForm");

    const submitMock = vi.fn();
    filterForm.submit = submitMock;

    vi.resetModules();
  });

  it("should set estado and submit form when clicking estado button", async () => {
    await import("@/public/js/booking-filters.js");

    const pendienteBtn = document.querySelector('[data-estado="Pendiente"]');
    pendienteBtn.click();

    expect(estadoInput.value).toBe("Pendiente");
    expect(filterForm.submit).toHaveBeenCalled();
  });

  it("should clear estado when clicking 'Todas' button", async () => {
    await import("@/public/js/booking-filters.js");

    estadoInput.value = "Pendiente";

    const todasBtn = document.querySelector('[data-estado=""]');
    todasBtn.click();

    expect(estadoInput.value).toBe("");
    expect(filterForm.submit).toHaveBeenCalled();
  });

  it("should handle multiple estado changes", async () => {
    await import("@/public/js/booking-filters.js");

    const confirmadaBtn = document.querySelector('[data-estado="Confirmada"]');
    confirmadaBtn.click();
    expect(estadoInput.value).toBe("Confirmada");

    const canceladaBtn = document.querySelector('[data-estado="Cancelada"]');
    canceladaBtn.click();
    expect(estadoInput.value).toBe("Cancelada");

    expect(filterForm.submit).toHaveBeenCalledTimes(2);
  });

  it("should use event delegation (clicking icon inside button)", async () => {
    document.body.innerHTML = `
      <form id="filterForm">
        <input type="hidden" id="estadoInput" name="estado" value="" />
        <button type="button" data-estado="Pendiente">
          <i class="bi bi-clock"></i> Pendiente
        </button>
      </form>
    `;

    const estadoInput = document.getElementById("estadoInput");
    const filterForm = document.getElementById("filterForm");
    filterForm.submit = vi.fn();

    await import("@/public/js/booking-filters.js");

    const icon = document.querySelector(".bi-clock");
    icon.click();

    expect(estadoInput.value).toBe("Pendiente");
    expect(filterForm.submit).toHaveBeenCalled();
  });

  it("should not submit if clicking outside buttons", async () => {
    await import("@/public/js/booking-filters.js");

    filterForm.click();

    expect(filterForm.submit).not.toHaveBeenCalled();
  });
});
