/**
 * @file Unit tests for ToastNotification component.
 */

import { describe, it, expect, beforeEach, vi } from "vitest";
import "@/public/js/shared/components/toast.js";
import { notification } from "@/public/js/shared/components/toast.js";

describe("ToastNotification Web Component", () => {
  let toastElement;
  let bootstrapToastMock;

  beforeEach(() => {
    // Mock Bootstrap Toast
    bootstrapToastMock = {
      show: vi.fn(),
      hide: vi.fn(),
    };

    global.bootstrap = {
      ...global.bootstrap,
      Toast: vi.fn(() => bootstrapToastMock),
    };

    // Clean up DOM
    document.body.innerHTML = "";
    toastElement = document.createElement("toast-notification");
    document.body.appendChild(toastElement);
  });

  it("should be defined as a custom element", () => {
    expect(customElements.get("toast-notification")).toBeDefined();
  });

  it("should render the toast structure", () => {
    const toastContainer = toastElement.querySelector(".toast-container");
    const toastBody = toastElement.querySelector(".toast-body");
    const closeBtn = toastElement.querySelector(".btn-close");

    expect(toastContainer).not.toBeNull();
    expect(toastBody).not.toBeNull();
    expect(closeBtn).not.toBeNull();
  });

  it("should initialize Bootstrap Toast", () => {
    expect(global.bootstrap.Toast).toHaveBeenCalledWith(toastElement.querySelector(".toast"));
  });

  it("should show success toast with correct classes and message", () => {
    const message = "Operation successful";
    toastElement.show(message, "success");

    const toastEl = toastElement.querySelector(".toast");
    const toastBody = toastElement.querySelector(".toast-body");
    const closeBtn = toastElement.querySelector(".btn-close");

    expect(toastEl.classList.contains("bg-success")).toBe(true);
    expect(toastEl.classList.contains("text-white")).toBe(true);
    // Should verify btn-close-white is present for success (white text)
    expect(closeBtn.classList.contains("btn-close-white")).toBe(true);
    expect(toastBody.textContent).toBe(message);
    expect(bootstrapToastMock.show).toHaveBeenCalled();
  });

  it("should show warning toast with correct classes", () => {
    toastElement.show("Warning message", "warning");

    const toastEl = toastElement.querySelector(".toast");
    const closeBtn = toastElement.querySelector(".btn-close");

    expect(toastEl.classList.contains("bg-warning")).toBe(true);
    expect(toastEl.classList.contains("text-dark")).toBe(true);
    // Should NOT have btn-close-white for warning (dark text)
    expect(closeBtn.classList.contains("btn-close-white")).toBe(false);
  });

  describe("notification helper", () => {
    it("should create and show a toast if none exists", async () => {
      document.body.innerHTML = ""; // Ensure clean state

      // Mock timers for setTimeout
      vi.useFakeTimers();

      notification("Hello via helper", "info");

      // Check if element was added to DOM
      const toast = document.querySelector("toast-notification");
      expect(toast).not.toBeNull();

      // Run timers
      vi.runAllTimers();

      // Re-query or rely on the instance if we could capture it,
      // but checking the mock calls is safer
      // We need to wait for the next runloop or rely on the timer execution

      // Since notification helper instantiates the element,
      // we need to make sure the NEW instance constructor/connectedCallback runs.
      // JSDOM handles custom elements synchronously usually.

      expect(toast.querySelector(".toast-body").textContent).toBe("Hello via helper");

      vi.useRealTimers();
    });
  });
});
