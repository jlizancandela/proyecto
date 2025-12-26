/**
 * Booking flow tests.
 * This file contains tests for the Preact reservation interface.
 */

const { test, expect } = require("@playwright/test");

test.describe("Booking Flow", () => {
  /**
   * Test the booking process navigation.
   */
  test("should navigate through booking steps", async ({ page }) => {
    // Go to the booking page
    await page.goto("/reservar");

    // Wait for the Preact app to load
    // Check if the service selection step is visible
    const serviceStep = page.locator("text=Selecciona un servicio");
    await expect(serviceStep).toBeVisible();

    // Select the first service
    await page.click(".service-card:first-child, .service-item:first-child");

    // Next button should be clickable or auto-transition
    // Check if we are in the specialist selection step
    const specialistStep = page.locator("text=Selecciona un especialista");
    await expect(specialistStep).toBeVisible();

    await page.click(".specialist-card:first-child");

    // Check if we are in the date selection step
    const dateStep = page.locator("text=Selecciona fecha y hora");
    await expect(dateStep).toBeVisible();
  });
});
