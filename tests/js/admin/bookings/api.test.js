import { describe, it, expect, beforeEach, vi } from "vitest";
import {
  fetchBooking,
  createBooking,
  updateBooking,
  deleteBooking,
} from "@/public/js/admin/bookings/api.js";

describe("Bookings API", () => {
  beforeEach(() => {
    fetch.mockClear();
  });

  describe("fetchBooking", () => {
    it("should fetch booking by id", async () => {
      const mockBooking = {
        id_reserva: 1,
        fecha_reserva: "2024-01-15",
        hora_inicio: "10:00",
        hora_fin: "11:00",
        estado: "confirmada",
      };

      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true, data: mockBooking }),
      });

      const result = await fetchBooking(1);

      expect(fetch).toHaveBeenCalledWith("/admin/api/reservas/1");
      expect(result.success).toBe(true);
      expect(result.data).toEqual(mockBooking);
    });

    it("should handle network errors", async () => {
      fetch.mockRejectedValueOnce(new Error("Network error"));

      await expect(fetchBooking(1)).rejects.toThrow("Network error");
    });
  });

  describe("createBooking", () => {
    it("should create booking with correct payload", async () => {
      const bookingData = {
        fecha_reserva: "2024-01-15",
        hora_inicio: "10:00",
        id_cliente: 1,
        id_especialista: 2,
        id_servicio: 3,
      };

      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true, data: { id: 123 } }),
      });

      const result = await createBooking(bookingData);

      expect(fetch).toHaveBeenCalledWith("/admin/api/reservas", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(bookingData),
      });
      expect(result.success).toBe(true);
    });
  });

  describe("updateBooking", () => {
    it("should update booking with correct payload", async () => {
      const bookingId = 1;
      const bookingData = {
        fecha_reserva: "2024-01-16",
        estado: "cancelada",
      };

      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true }),
      });

      await updateBooking(bookingId, bookingData);

      expect(fetch).toHaveBeenCalledWith("/admin/api/reservas/1", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(bookingData),
      });
    });
  });

  describe("deleteBooking", () => {
    it("should delete booking by id", async () => {
      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true }),
      });

      const result = await deleteBooking(1);

      expect(fetch).toHaveBeenCalledWith("/admin/api/reservas/1", {
        method: "DELETE",
      });
      expect(result.success).toBe(true);
    });
  });
});
