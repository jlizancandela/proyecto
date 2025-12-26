import { describe, it, expect, beforeEach, vi } from "vitest";

describe("Bookings JS Logic", () => {
  let modalMock;

  beforeEach(() => {
    // Reset DOM
    document.body.innerHTML = `
      <div id="actionModal">
        <h5 id="modalTitle"></h5>
        <p id="modalMessage"></p>
        <p id="modalSubMessage"></p>
        <button id="modalCancelBtn"></button>
        <button id="confirmActionBtn"></button>
      </div>
      <button class="btn-modify" data-booking-id="1">Modify</button>
      <button class="btn-cancel" data-booking-id="2">Cancel</button>
    `;

    // Mock Bootstrap Modal
    modalMock = {
      show: vi.fn(),
      hide: vi.fn(),
    };
    global.bootstrap = {
      Modal: vi.fn(() => modalMock),
    };

    // Reload the script to re-initialize constants and listeners
    vi.resetModules();
    // We need to re-import the file to trigger execution and constant assignment
    // relative path needs to be correct based on alias
    import("@/public/js/bookings.js").then(() => {
      // Manually trigger init if needed, but the script calls it on DOMContentLoaded
      // Since we are already past DOMContentLoaded in JSDOM usually, we might need to call init manually
      // But the script adds listener.
      // We can call window.initializeBookingListeners() because we exposed it.
      window.initializeBookingListeners();
    });
  });

  it("should open modal for modifying booking", async () => {
    await import("@/public/js/bookings.js");
    window.initializeBookingListeners(); // Re-bind since DOM changed

    const modifyBtn = document.querySelector(".btn-modify");
    modifyBtn.click();

    expect(global.bootstrap.Modal).toHaveBeenCalled();
    expect(modalMock.show).toHaveBeenCalled();
    expect(document.getElementById("modalTitle").textContent).toBe("Modificar Reserva");
  });

  it("should open modal for canceling booking", async () => {
    await import("@/public/js/bookings.js");
    window.initializeBookingListeners();

    const cancelBtn = document.querySelector(".btn-cancel");
    cancelBtn.click();

    expect(global.bootstrap.Modal).toHaveBeenCalled();
    expect(modalMock.show).toHaveBeenCalled();
    expect(document.getElementById("modalTitle").textContent).toBe("Cancelar Reserva");
  });

  it("should submit form when canceling confirmation is clicked", async () => {
    await import("@/public/js/bookings.js");

    // Setup cancel state
    window.cancelBooking("123");

    // Mock form submit
    const submitMock = vi.fn();
    HTMLFormElement.prototype.submit = submitMock;

    window.confirmAction();

    // Check if form was created and submitted
    // confirmAction creates a form and appends it to body
    const form = document.querySelector("form");
    expect(form).not.toBeNull();
    expect(form.action).toContain("/user/reservas/cancel/123");
    expect(submitMock).toHaveBeenCalled();
  });

  it("should redirect when modifying confirmation is clicked", async () => {
    await import("@/public/js/bookings.js");

    // Setup modify state
    window.modifyBooking("456");

    // Mock window location
    delete window.location;
    window.location = { href: "" };

    window.confirmAction();

    expect(window.location.href).toContain("/user/reservas/modify/456");
  });
});
