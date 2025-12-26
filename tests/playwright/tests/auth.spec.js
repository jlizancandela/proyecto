/**
 * Authentication flow tests.
 * This file contains tests for login and registration.
 */

const { test, expect } = require("@playwright/test");

test.describe("Authentication", () => {
  /**
   * Test the login flow.
   * Note: This uses jlizancandela@gmail.com which is an admin in the DB.
   * You should ensure you know the password or use a test user.
   */
  test("should show login page and allow login attempt", async ({ page }) => {
    await page.goto("/login");

    await expect(page.locator("h2")).toContainText("Iniciar Sesión");

    // Fill in credentials
    await page.fill('input[name="email"]', "admin@admin.com");
    await page.fill('input[name="password"]', "wrongpassword");

    // Submit form
    await page.click('button[type="submit"]');

    // Verify error message as the credentials were purposefully wrong
    const errorMsg = page.locator("text=Email o contraseña incorrectos");
    await expect(errorMsg).toBeVisible();
  });
});
