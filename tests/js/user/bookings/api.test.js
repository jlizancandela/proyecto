import { describe, it, expect, beforeEach, vi } from "vitest";
import {
  getServices,
  getEspecialistasDisponibles,
  getUserBookings,
  createReserva,
  getCurrentUser,
} from "@/public/js/user/bookings/api/bookingsApi.js";

describe("User Bookings API", () => {
  beforeEach(() => {
    fetch.mockClear();
  });

  describe("getServices", () => {
    it("should fetch all services", async () => {
      const mockServices = [
        { id: 1, nombre: "Corte", precio: 15 },
        { id: 2, nombre: "Tinte", precio: 35 },
      ];

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockServices,
      });

      const result = await getServices();

      expect(fetch).toHaveBeenCalledWith("/api/services");
      expect(result).toEqual(mockServices);
    });

    it("should return empty array on error", async () => {
      fetch.mockRejectedValueOnce(new Error("Network error"));

      const result = await getServices();

      expect(result).toEqual([]);
    });
  });

  describe("getEspecialistasDisponibles", () => {
    it("should fetch available specialists with pagination", async () => {
      const mockData = {
        data: [{ id: 1, nombre: "Juan" }],
        total: 1,
      };

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockData,
      });

      const result = await getEspecialistasDisponibles("1", "2024-01-15", 10, 0);

      expect(fetch).toHaveBeenCalledWith(
        "/api/especialistas/disponibles?servicio=1&fecha=2024-01-15&limit=10&offset=0"
      );
      expect(result).toEqual(mockData);
    });

    it("should return empty data if no service provided", async () => {
      const result = await getEspecialistasDisponibles(null, "2024-01-15");

      expect(result).toEqual({ data: [], total: 0 });
      expect(fetch).not.toHaveBeenCalled();
    });

    it("should return empty data if no date provided", async () => {
      const result = await getEspecialistasDisponibles("1", null);

      expect(result).toEqual({ data: [], total: 0 });
      expect(fetch).not.toHaveBeenCalled();
    });
  });

  describe("getUserBookings", () => {
    it("should fetch user bookings", async () => {
      const mockBookings = [{ id: 1, servicio: "Corte", fecha: "2024-01-15" }];

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ reservas: mockBookings }),
      });

      const result = await getUserBookings();

      expect(fetch).toHaveBeenCalledWith("/api/reservas");
      expect(result).toEqual(mockBookings);
    });

    it("should return empty array on error", async () => {
      fetch.mockRejectedValueOnce(new Error("Network error"));

      const result = await getUserBookings();

      expect(result).toEqual([]);
    });
  });

  describe("createReserva", () => {
    const validReserva = {
      servicio_id: 1,
      especialista_id: 2,
      fecha: "2024-01-15",
      hora: "10:00",
    };

    it("should create booking with valid data", async () => {
      const mockResponse = { success: true, id: 123 };

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const result = await createReserva(validReserva);

      expect(fetch).toHaveBeenCalledWith("/api/reservas", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(validReserva),
      });
      expect(result).toEqual(mockResponse);
    });

    it("should throw error if servicio_id is missing", async () => {
      const invalidData = { ...validReserva, servicio_id: null };

      await expect(createReserva(invalidData)).rejects.toThrow("Debes seleccionar un servicio");
    });

    it("should throw error if especialista_id is missing", async () => {
      const invalidData = { ...validReserva, especialista_id: null };

      await expect(createReserva(invalidData)).rejects.toThrow("Debes seleccionar un especialista");
    });

    it("should throw error if fecha is missing", async () => {
      const invalidData = { ...validReserva, fecha: null };

      await expect(createReserva(invalidData)).rejects.toThrow("Debes seleccionar una fecha");
    });

    it("should throw error if hora is missing", async () => {
      const invalidData = { ...validReserva, hora: null };

      await expect(createReserva(invalidData)).rejects.toThrow("Debes seleccionar una hora");
    });
  });

  describe("getCurrentUser", () => {
    it("should fetch current user", async () => {
      const mockUser = { id: 1, nombre: "Juan", email: "juan@example.com" };

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ success: true, data: mockUser }),
      });

      const result = await getCurrentUser();

      expect(fetch).toHaveBeenCalledWith("/api/me");
      expect(result).toEqual(mockUser);
    });

    it("should return null if not authenticated", async () => {
      fetch.mockResolvedValueOnce({
        ok: false,
      });

      const result = await getCurrentUser();

      expect(result).toBeNull();
    });

    it("should return null on error", async () => {
      fetch.mockRejectedValueOnce(new Error("Network error"));

      const result = await getCurrentUser();

      expect(result).toBeNull();
    });
  });
});
