/**
 * Booking flow tests.
 * This file contains tests for the Preact reservation interface.
 */

/**
 * @file E2E tests for booking flow.
 * @project app-reservas
 */

const { test, expect } = require("@playwright/test");

test.describe("Booking Flow", () => {
  /**
   * Test the booking process navigation.
   * This test will register a new user if it's not logged in.
   */
  test("should navigate through booking steps", async ({ page }) => {
    // Go directly to the new booking page
    await page.goto("/user/reservas/nueva");

    // If redirected to login, perform registration and login to have a valid session
    if (page.url().includes("/login")) {
      // Register a new user
      await page.goto("/register");

      const timestamp = Date.now();
      const email = `testuser_${timestamp}@example.com`;
      const password = "TestUser123!";

      await page.fill("#nombre", "Test");
      await page.fill("#apellidos", "User");
      await page.fill("#email", email);
      await page.fill("#telefono", "600123456");
      await page.fill("#password", password);
      await page.fill("#password-confirm", password);

      await page.click('button[type="submit"]');

      // Wait for redirect to login page after registration
      await page.waitForURL("**/login");

      // Now login with the new account
      await page.fill("#email", email);
      await page.fill("#password", password);
      await page.click('button[type="submit"]');

      // Wait for successful login (should redirect to home page)
      await page.waitForURL("**/");

      // Now go to the booking page
      await page.goto("/user/reservas/nueva");
    }

    // Wait for the Preact app to load
    await page.waitForSelector("#bookings-app", { state: "visible" });
    const heading = page.locator("h1");
    await expect(heading).toContainText("Nueva Reserva", { timeout: 10000 });

    // Check if step 1 (Services) is active
    await expect(page.locator("text=Paso 1/3")).toBeVisible();

    // Select the first service card
    const firstService = page.locator(".card").first();
    await expect(firstService).toBeVisible();
    await firstService.click();

    // Check if we are in step 2 (Date and Specialist)
    await expect(page.locator("text=Paso 2/3")).toBeVisible();
    await expect(page.locator("text=Fecha y Especialista")).toBeVisible();

    // Select a time slot from the first available specialist
    // Wait for time slots to be visible
    const timeSlotButton = page.locator("button.btn-outline-primary").first();
    await expect(timeSlotButton).toBeVisible({ timeout: 10000 });
    await timeSlotButton.click();

    // Wait a moment for the selection to be processed
    await page.waitForTimeout(500);

    // Click the "Next" button to go to confirmation
    const nextButton = page.locator("button.btn-primary.rounded-circle:has(i.bi-chevron-right)");
    await nextButton.click();

    // Check if we are in step 3 (Confirmation)
    await expect(page.locator("text=Paso 3/3")).toBeVisible({ timeout: 10000 });
    await expect(page.locator("text=Confirmación")).toBeVisible();

    // Verify the confirmation details are displayed
    await expect(page.locator("text=Servicio")).toBeVisible();
    await expect(page.locator("text=Especialista")).toBeVisible();
    await expect(page.locator("text=Fecha")).toBeVisible();
    await expect(page.locator("text=Hora")).toBeVisible();

    // Click the "Confirm Booking" button
    const confirmButton = page.locator('button:has-text("Confirmar Reserva")');
    await expect(confirmButton).toBeVisible();
    await confirmButton.click();

    // Wait for redirect to bookings list page
    await page.waitForURL("**/user/reservas", { timeout: 10000 });

    // Verify we're on the bookings page and the new booking appears
    await expect(page.locator("h1")).toContainText("Mis Reservas");

    // Verify that at least one booking card is visible
    const bookingCard = page.locator(".card").first();
    await expect(bookingCard).toBeVisible();
  });
});
