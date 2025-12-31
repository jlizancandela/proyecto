/**
 * @file User profile and account deactivation tests.
 * @project app-reservas
 */

const { test, expect } = require("@playwright/test");

test.describe("User Profile", () => {
  /**
   * Test for user account deactivation (logical deletion).
   *
   * This test verifies that when a user deactivates their account:
   * 1. The account is marked as inactive in the database (activo = 0)
   * 2. The user is logged out and redirected to login
   * 3. Attempting to login again redirects to the reactivation page
   *
   * Note: The database field change (activo = 0) is verified indirectly
   * through the application behavior - if the account wasn't deactivated,
   * the user would be able to login normally instead of being redirected
   * to the reactivation page.
   */
  test("should deactivate user account (logical deletion)", async ({ page }) => {
    // First, register and login a new user
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

    // Login with the created credentials
    await page.fill("#email", email);
    await page.fill("#password", password);
    await page.click('button[type="submit"]');

    // Wait for successful login
    await page.waitForURL("**/");

    // Navigate to user profile
    await page.goto("/user/profile");

    // Verify we're on the profile page
    await expect(page).toHaveURL("/user/profile");

    // Scroll to the danger zone section
    await page.locator("text=Zona de Peligro").scrollIntoViewIfNeeded();

    // Click the "Darme de baja" button
    const deleteButton = page.getByRole("button", { name: "Darme de baja" });
    await expect(deleteButton).toBeVisible();
    await deleteButton.click();

    // Wait for confirmation modal to appear
    const modal = page.locator("#confirmDeleteAccountModal");
    await expect(modal).toBeVisible();

    // Verify modal title
    await expect(page.locator("text=Confirmar desactivación de cuenta")).toBeVisible();

    // Click the confirmation button in the modal
    const confirmButton = page.getByRole("button", { name: "Sí, desactivar mi cuenta" });
    await expect(confirmButton).toBeVisible();
    await confirmButton.click();

    // Should redirect to login page with deactivation message
    await page.waitForURL(/.*\/login\?message=account_deactivated/);

    // Verify we're on the login page
    await expect(page).toHaveURL(/.*\/login/);

    // Try to login again with the same credentials
    await page.fill("#email", email);
    await page.fill("#password", password);
    await page.click('button[type="submit"]');

    // Should be redirected to reactivation page (account is inactive)
    await page.waitForURL("**/reactivate");

    // Verify we're on the reactivation page
    await expect(page).toHaveURL("/reactivate");

    // Verify reactivation message is shown
    await expect(page.locator("text=Cuenta Desactivada")).toBeVisible();
  });

  /**
   * Test for account reactivation.
   *
   * This test verifies that a user with an inactive account can:
   * 1. Be redirected to the reactivation page when trying to login
   * 2. See the reactivation option
   * 3. Successfully reactivate their account
   * 4. Access the user dashboard after reactivation
   */
  test("should reactivate inactive account and grant access", async ({ page }) => {
    // First, register and login a new user
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

    // Login with the created credentials
    await page.fill("#email", email);
    await page.fill("#password", password);
    await page.click('button[type="submit"]');

    // Wait for successful login
    await page.waitForURL("**/");

    // Navigate to user profile and deactivate account
    await page.goto("/user/profile");

    // Scroll to the danger zone section
    await page.locator("text=Zona de Peligro").scrollIntoViewIfNeeded();

    // Click the "Darme de baja" button
    const deleteButton = page.getByRole("button", { name: "Darme de baja" });
    await deleteButton.click();

    // Wait for confirmation modal and confirm
    const modal = page.locator("#confirmDeleteAccountModal");
    await expect(modal).toBeVisible();

    const confirmButton = page.getByRole("button", { name: "Sí, desactivar mi cuenta" });
    await confirmButton.click();

    // Should redirect to login page
    await page.waitForURL(/.*\/login/);

    // Now try to login again with the deactivated account
    await page.fill("#email", email);
    await page.fill("#password", password);
    await page.click('button[type="submit"]');

    // Should be redirected to reactivation page
    await page.waitForURL("**/reactivate");
    await expect(page).toHaveURL("/reactivate");

    // Verify reactivation page content
    await expect(page.locator("text=Cuenta Desactivada")).toBeVisible();
    await expect(page.locator("text=está actualmente desactivada")).toBeVisible();

    // Click the reactivation button
    const reactivateButton = page.getByRole("button", { name: "Reactivar Mi Cuenta" });
    await expect(reactivateButton).toBeVisible();
    await reactivateButton.click();

    // Should redirect to user dashboard after reactivation
    await page.waitForURL("**/user");
    await expect(page).toHaveURL("/user");

    // Verify we can access the dashboard (account is now active)
    await expect(page.locator("h1")).toContainText("Hola");
  });
});
