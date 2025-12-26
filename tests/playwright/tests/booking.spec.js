/**
 * Booking flow tests.
 * This file contains tests for the Preact reservation interface.
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

    // If redirected to login, perform registration to have a valid session
    if (page.url().includes("/login")) {
      await page.goto("/register");

      const timestamp = Date.now();
      const email = `testuser_${timestamp}@example.com`;

      await page.fill("#nombre", "Test");
      await page.fill("#apellidos", "User");
      await page.fill("#email", email);
      await page.fill("#telefono", "600123456");
      await page.fill("#password", "TestUser123!");
      await page.fill("#password-confirm", "TestUser123!");

      await page.click('button[type="submit"]');

      // Wait for redirection
      await expect(page).not.toHaveURL(/.*register/);

      // Go to the correct booking page after registration/login
      await page.goto("/user/reservas/nueva");
    }

    // Wait for the Preact app to load
    const heading = page.locator("h1");
    await expect(heading).toContainText("Nueva Reserva");

    // Check if step 1 (Services) is active
    await expect(page.locator("text=Paso 1/3")).toBeVisible();

    // Select the first service card
    // The component uses the .card class
    const firstService = page.locator(".card").first();
    await expect(firstService).toBeVisible();
    await firstService.click();

    // The application automatically transitions to Step 2 when a service is selected.
    // Clicking the next button here is redundant and would fail because it's already
    // expecting specialists/time selection for Step 3.

    // Check if we are in step 2 (Date and Specialist)
    await expect(page.locator("text=Paso 2/3")).toBeVisible();
    await expect(page.locator("text=Fecha y Especialista")).toBeVisible();
  });
});
