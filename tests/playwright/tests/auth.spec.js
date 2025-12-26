/**
 * Authentication flow tests.
 * This file contains tests for login and registration.
 */

const { test, expect } = require("@playwright/test");

test.describe("Authentication", () => {
  /**
   * Test the login flow.
   */
  test("should login with valid credentials", async ({ page }) => {
    await page.goto("/login");

    // Fill in credentials
    // Note: Adjust selectors and credentials based on real environment
    await page.fill('input[name="email"]', "admin@admin.com");
    await page.fill('input[name="password"]', "admin123");

    // Submit form
    await page.click('button[type="submit"]');

    // Verify redirection to dashboard
    await expect(page).toHaveURL(/.*admin/);
  });

  /**
   * Test failed login.
   */
  test("should show error with invalid credentials", async ({ page }) => {
    await page.goto("/login");

    await page.fill('input[name="email"]', "wrong@example.com");
    await page.fill('input[name="password"]', "wrongpass");
    await page.click('button[type="submit"]');

    // Verify error message exists
    // Adjust selector based on how you show errors (e.g., .alert-danger)
    const errorMsg = page.locator('.alert-danger, .error-message, [role="alert"]');
    await expect(errorMsg).toBeVisible();
  });
});
