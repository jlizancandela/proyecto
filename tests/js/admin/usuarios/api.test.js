import { describe, it, expect, beforeEach, vi } from "vitest";
import {
  fetchUser,
  createUser,
  updateUser,
  toggleUserStatus,
} from "@/public/js/admin/usuarios/api.js";

describe("Users API", () => {
  beforeEach(() => {
    fetch.mockClear();
  });

  describe("fetchUser", () => {
    it("should fetch user by id", async () => {
      const mockUser = {
        id: 1,
        nombre: "Juan",
        apellidos: "Pérez",
        email: "juan@example.com",
        telefono: "123456789",
        rol: "Cliente",
        activo: true,
      };

      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true, data: mockUser }),
      });

      const result = await fetchUser(1);

      expect(fetch).toHaveBeenCalledWith("/admin/api/users/1");
      expect(result.success).toBe(true);
      expect(result.data).toEqual(mockUser);
    });

    it("should handle network errors", async () => {
      fetch.mockRejectedValueOnce(new Error("Network error"));

      await expect(fetchUser(1)).rejects.toThrow("Network error");
    });
  });

  describe("createUser", () => {
    it("should create user with FormData", async () => {
      const formData = new FormData();
      formData.append("nombre", "Test");
      formData.append("email", "test@example.com");

      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true, data: { id: 5 } }),
      });

      const result = await createUser(formData);

      expect(fetch).toHaveBeenCalledWith("/admin/api/users", {
        method: "POST",
        body: formData,
      });
      expect(result.success).toBe(true);
    });
  });

  describe("updateUser", () => {
    it("should update user with FormData", async () => {
      const userId = 1;
      const formData = new FormData();
      formData.append("nombre", "Updated Name");

      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true }),
      });

      await updateUser(userId, formData);

      expect(fetch).toHaveBeenCalledWith("/admin/api/users/1", {
        method: "POST",
        body: formData,
      });
    });
  });

  describe("toggleUserStatus", () => {
    it("should toggle user status to inactive", async () => {
      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true }),
      });

      const result = await toggleUserStatus(1, "0");

      expect(fetch).toHaveBeenCalledWith("/admin/api/users/1", {
        method: "POST",
        body: expect.any(FormData),
      });
      expect(result.success).toBe(true);
    });

    it("should toggle user status to active", async () => {
      fetch.mockResolvedValueOnce({
        json: async () => ({ success: true }),
      });

      await toggleUserStatus(1, "1");

      const call = fetch.mock.calls[0];
      const formData = call[1].body;

      expect(formData.get("activo")).toBe("1");
    });
  });
});
