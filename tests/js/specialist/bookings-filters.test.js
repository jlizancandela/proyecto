/**
 * @file Unit tests for specialist booking filters.
 */

import { describe, it, expect, beforeEach, vi } from "vitest";

describe("Specialist Booking Filters", () => {
  let btnApplyFilters;
  let btnClearFilters;
  let filterEstado;
  let filterCliente;
  let filterFechaDesde;
  let filterFechaHasta;

  beforeEach(() => {
    document.body.innerHTML = `
      <select id="filterEstado">
        <option value="">Todos</option>
        <option value="Pendiente">Pendiente</option>
        <option value="Confirmada">Confirmada</option>
      </select>
      <input type="text" id="filterCliente" value="" />
      <input type="date" id="filterFechaDesde" value="" />
      <input type="date" id="filterFechaHasta" value="" />
      <button id="btnApplyFilters">Aplicar</button>
      <button id="btnClearFilters">Limpiar</button>
    `;

    btnApplyFilters = document.getElementById("btnApplyFilters");
    btnClearFilters = document.getElementById("btnClearFilters");
    filterEstado = document.getElementById("filterEstado");
    filterCliente = document.getElementById("filterCliente");
    filterFechaDesde = document.getElementById("filterFechaDesde");
    filterFechaHasta = document.getElementById("filterFechaHasta");

    delete globalThis.location;
    globalThis.location = { href: "" };

    vi.resetModules();
  });

  it("should navigate with all filters when applying", async () => {
    await import("@/public/js/specialist/bookings-filters.js");

    filterEstado.value = "Pendiente";
    filterCliente.value = "Juan";
    filterFechaDesde.value = "2024-01-01";
    filterFechaHasta.value = "2024-12-31";

    btnApplyFilters.click();

    expect(globalThis.location.href).toContain("/specialist/bookings?");
    expect(globalThis.location.href).toContain("estado=Pendiente");
    expect(globalThis.location.href).toContain("cliente=Juan");
    expect(globalThis.location.href).toContain("fecha_desde=2024-01-01");
    expect(globalThis.location.href).toContain("fecha_hasta=2024-12-31");
  });

  it("should navigate with only estado filter", async () => {
    await import("@/public/js/specialist/bookings-filters.js");

    filterEstado.value = "Confirmada";

    btnApplyFilters.click();

    expect(globalThis.location.href).toBe("/specialist/bookings?estado=Confirmada");
  });

  it("should navigate with only date range", async () => {
    await import("@/public/js/specialist/bookings-filters.js");

    filterFechaDesde.value = "2024-01-01";
    filterFechaHasta.value = "2024-01-31";

    btnApplyFilters.click();

    expect(globalThis.location.href).toContain("fecha_desde=2024-01-01");
    expect(globalThis.location.href).toContain("fecha_hasta=2024-01-31");
    expect(globalThis.location.href).not.toContain("estado=");
    expect(globalThis.location.href).not.toContain("cliente=");
  });

  it("should navigate with only cliente filter", async () => {
    await import("@/public/js/specialist/bookings-filters.js");

    filterCliente.value = "Maria Garcia";

    btnApplyFilters.click();

    expect(globalThis.location.href).toBe("/specialist/bookings?cliente=Maria+Garcia");
  });

  it("should navigate without params when no filters applied", async () => {
    await import("@/public/js/specialist/bookings-filters.js");

    btnApplyFilters.click();

    expect(globalThis.location.href).toBe("/specialist/bookings?");
  });

  it("should clear all filters when clicking clear button", async () => {
    await import("@/public/js/specialist/bookings-filters.js");

    filterEstado.value = "Pendiente";
    filterCliente.value = "Juan";
    filterFechaDesde.value = "2024-01-01";

    btnClearFilters.click();

    expect(globalThis.location.href).toBe("/specialist/bookings");
  });

  it("should handle missing filter elements gracefully", async () => {
    document.body.innerHTML = `
      <button id="btnApplyFilters">Aplicar</button>
    `;

    await import("@/public/js/specialist/bookings-filters.js");

    const btn = document.getElementById("btnApplyFilters");

    expect(() => btn.click()).not.toThrow();
    expect(globalThis.location.href).toBe("/specialist/bookings?");
  });

  it("should ignore empty and whitespace-only values", async () => {
    await import("@/public/js/specialist/bookings-filters.js");

    filterEstado.value = "";
    filterCliente.value = "   ";
    filterFechaDesde.value = "";

    btnApplyFilters.click();

    expect(globalThis.location.href).toBe("/specialist/bookings?");
  });

  it("should URL encode special characters in cliente name", async () => {
    await import("@/public/js/specialist/bookings-filters.js");

    filterCliente.value = "José María & García";

    btnApplyFilters.click();

    expect(globalThis.location.href).toContain("cliente=");
    // URLSearchParams encodes: é as %C3%A9, spaces as +, & as %26
    expect(globalThis.location.href).toContain("%C3%A9"); // é encoded
    expect(globalThis.location.href).toContain("%26"); // & encoded
  });
});
