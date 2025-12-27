import { describe, it, expect, beforeEach, vi, afterEach } from "vitest";

describe("Bookings JS Logic", () => {
  let modalMock;
  let initializeBookingListeners;
  let modifyBooking;
  let cancelBooking;
  let confirmAction;

  beforeEach(async () => {
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

    modalMock = {
      show: vi.fn(),
      hide: vi.fn(),
    };
    globalThis.bootstrap = {
      Modal: vi.fn(() => modalMock),
    };

    vi.resetModules();

    // Import the module and get the exported functions
    const module = await import("@/public/js/bookings.js");
    initializeBookingListeners = module.initializeBookingListeners;
    modifyBooking = module.modifyBooking;
    cancelBooking = module.cancelBooking;
    confirmAction = module.confirmAction;
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  it("should open modal for modifying booking", () => {
    initializeBookingListeners();

    const modifyBtn = document.querySelector(".btn-modify");
    modifyBtn.click();

    expect(globalThis.bootstrap.Modal).toHaveBeenCalled();
    expect(modalMock.show).toHaveBeenCalled();
    expect(document.getElementById("modalTitle").textContent).toBe("Modificar Reserva");
  });

  it("should open modal for canceling booking", () => {
    initializeBookingListeners();

    const cancelBtn = document.querySelector(".btn-cancel");
    cancelBtn.click();

    expect(globalThis.bootstrap.Modal).toHaveBeenCalled();
    expect(modalMock.show).toHaveBeenCalled();
    expect(document.getElementById("modalTitle").textContent).toBe("Cancelar Reserva");
  });

  it("should submit form when canceling confirmation is clicked", () => {
    cancelBooking("123");

    const submitMock = vi.fn();
    HTMLFormElement.prototype.submit = submitMock;

    confirmAction();

    const form = document.querySelector("form");
    expect(form).not.toBeNull();
    expect(form.action).toContain("/user/reservas/cancel/123");
    expect(submitMock).toHaveBeenCalled();
  });

  it("should redirect when modifying confirmation is clicked", () => {
    modifyBooking("456");

    delete globalThis.location;
    globalThis.location = { href: "" };

    confirmAction();

    expect(globalThis.location.href).toContain("/user/reservas/modify/456");
  });
});
