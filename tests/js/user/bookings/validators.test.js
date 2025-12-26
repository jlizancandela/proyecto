import { describe, it, expect } from "vitest";
import { hasWeeklyBookingForService } from "@/public/js/user/bookings/tools/validators.js";

describe("Validators", () => {
  describe("hasWeeklyBookingForService", () => {
    const mockBookings = [
      {
        id: 1,
        id_servicio: 1,
        fecha_reserva: "2024-01-15", // Monday
        estado: "Confirmada",
      },
      {
        id: 4,
        id_servicio: 1,
        fecha_reserva: "2024-01-17", // Wednesday (same week as first)
        estado: "Cancelada",
      },
    ];

    it("should return true if booking exists for same service in same week", () => {
      const result = hasWeeklyBookingForService(mockBookings, 1, "2024-01-17");
      expect(result).toBe(true);
    });

    it("should ignore cancelled bookings", () => {
      const result = hasWeeklyBookingForService(
        [mockBookings[1]], // Only cancelled booking
        1,
        "2024-01-17"
      );
      expect(result).toBe(false);
    });

    it("should return false for empty bookings array", () => {
      const result = hasWeeklyBookingForService([], 1, "2024-01-15");
      expect(result).toBe(false);
    });

    it("should handle bookings at week boundaries correctly", () => {
      const sundayBooking = {
        id: 5,
        id_servicio: 1,
        fecha_reserva: "2024-01-21", // Sunday
        estado: "Confirmada",
      };

      const result = hasWeeklyBookingForService(
        [sundayBooking],
        1,
        "2024-01-15" // Monday of same week
      );
      expect(result).toBe(true);
    });
  });
});
