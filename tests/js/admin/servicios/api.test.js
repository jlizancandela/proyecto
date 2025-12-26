import { describe, it, expect, beforeEach, vi } from "vitest";
import {
  fetchService,
  createService,
  updateService,
  activateService,
  deactivateService,
} from "@/public/js/admin/servicios/api.js";

describe("Services API", () => {
  beforeEach(() => {
    fetch.mockClear();
  });

  describe("fetchService", () => {
    it("should fetch service by id", async () => {
      const mockService = {
        id: 1,
        nombre_servicio: "Corte de pelo",
        descripcion: "Corte básico",
        duracion_minutos: 30,
        precio: 15.0,
        activo: true,
      };

      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true, data: mockService }),
      });

      const result = await fetchService(1);

      expect(fetch).toHaveBeenCalledWith("/admin/api/services/1");
      expect(result.success).toBe(true);
      expect(result.data).toEqual(mockService);
    });
  });

  describe("createService", () => {
    it("should create service with correct payload", async () => {
      const serviceData = {
        nombre_servicio: "Tinte",
        descripcion: "Tinte completo",
        duracion_minutos: 60,
        precio: 35.0,
      };

      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true, data: { id: 5 } }),
      });

      const result = await createService(serviceData);

      expect(fetch).toHaveBeenCalledWith("/admin/api/services", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(serviceData),
      });
      expect(result.success).toBe(true);
    });
  });

  describe("updateService", () => {
    it("should update service with correct payload", async () => {
      const serviceId = 1;
      const serviceData = {
        nombre_servicio: "Corte premium",
        precio: 20.0,
      };

      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true }),
      });

      await updateService(serviceId, serviceData);

      expect(fetch).toHaveBeenCalledWith("/admin/api/services/1", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(serviceData),
      });
    });
  });

  describe("activateService", () => {
    it("should activate service", async () => {
      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true }),
      });

      const result = await activateService(1);

      expect(fetch).toHaveBeenCalledWith("/admin/api/services/1/activate", {
        method: "POST",
      });
      expect(result.success).toBe(true);
    });
  });

  describe("deactivateService", () => {
    it("should deactivate service", async () => {
      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true }),
      });

      const result = await deactivateService(1);

      expect(fetch).toHaveBeenCalledWith("/admin/api/services/1/deactivate", {
        method: "POST",
      });
      expect(result.success).toBe(true);
    });
  });
});
