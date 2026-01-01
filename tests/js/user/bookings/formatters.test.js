/**
 * @file Unit tests for booking UI formatters.
 * @project app-reservas
 */

import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import {
  formatearFechaLarga,
  formatearFechaISO,
  capitalizarPrimeraLetra,
  esHoy,
  isPastTime,
} from "@/src/js/user/bookings/tools/formatters.js";

describe("Formatters", () => {
  describe("formatearFechaLarga", () => {
    it("should format date in long Spanish format with capitalization", () => {
      const fecha = new Date("2024-01-15");
      const result = formatearFechaLarga(fecha);

      expect(result).toMatch(/^[A-Z]/); // Starts with capital letter
      expect(result).toContain("15");
      expect(result).toContain("2024");
    });

    it("should format date without capitalization when specified", () => {
      const fecha = new Date("2024-01-15");
      const result = formatearFechaLarga(fecha, false);

      expect(result).toMatch(/^[a-z]/); // Starts with lowercase
    });
  });

  describe("formatearFechaISO", () => {
    it("should format date in ISO format (YYYY-MM-DD)", () => {
      const fecha = new Date("2024-01-15");
      const result = formatearFechaISO(fecha);

      expect(result).toBe("2024-01-15");
    });

    it("should pad single digit months and days", () => {
      const fecha = new Date("2024-03-05");
      const result = formatearFechaISO(fecha);

      expect(result).toBe("2024-03-05");
    });
  });

  describe("capitalizarPrimeraLetra", () => {
    it("should capitalize first letter", () => {
      expect(capitalizarPrimeraLetra("hello")).toBe("Hello");
    });

    it("should handle already capitalized text", () => {
      expect(capitalizarPrimeraLetra("Hello")).toBe("Hello");
    });

    it("should return empty string for empty input", () => {
      expect(capitalizarPrimeraLetra("")).toBe("");
    });

    it("should handle null/undefined", () => {
      expect(capitalizarPrimeraLetra(null)).toBe("");
      expect(capitalizarPrimeraLetra(undefined)).toBe("");
    });
  });

  describe("esHoy", () => {
    it("should return true for today", () => {
      const hoy = new Date();
      expect(esHoy(hoy)).toBe(true);
    });

    it("should return false for yesterday", () => {
      const ayer = new Date();
      ayer.setDate(ayer.getDate() - 1);
      expect(esHoy(ayer)).toBe(false);
    });

    it("should return false for tomorrow", () => {
      const manana = new Date();
      manana.setDate(manana.getDate() + 1);
      expect(esHoy(manana)).toBe(false);
    });
  });

  describe("isPastTime", () => {
    beforeEach(() => {
      // Set fixed date: Jan 15, 2024 at 12:00:00
      vi.useFakeTimers();
      vi.setSystemTime(new Date("2024-01-15T12:00:00"));
    });

    afterEach(() => {
      vi.useRealTimers();
    });

    it("should return false for future dates", () => {
      const manana = new Date();
      manana.setDate(manana.getDate() + 1);

      expect(isPastTime(manana, "10:00")).toBe(false);
    });

    it("should return true for past time today", () => {
      const hoy = new Date(); // Will be 2024-01-15 12:00:00
      // 10:00 is past relative to 12:00
      expect(isPastTime(hoy, "10:00")).toBe(true);
    });

    it("should return false for future time today", () => {
      const hoy = new Date(); // Will be 2024-01-15 12:00:00
      // 14:00 is future relative to 12:00
      expect(isPastTime(hoy, "14:00")).toBe(false);
    });
  });
});
