/**
 * @file Login flow tests.
 * @project app-reservas
 */

const { test, expect } = require("@playwright/test");

test.describe("Login", () => {
  test("should login with valid credentials and create session", async ({ page }) => {
    // First, register a new user to ensure we have valid credentials
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

    // Verify success message
    await expect(page.locator("text=Cuenta creada con éxito")).toBeVisible();

    // Now test the login with the created credentials
    await page.fill("#email", email);
    await page.fill("#password", password);

    await page.click('button[type="submit"]');

    // Should redirect to home page after successful login
    await page.waitForURL("**/");

    // Verify we can access protected routes (user dashboard)
    // This confirms the session was created successfully
    await page.goto("/user");

    // Should not be redirected to login (we have a valid session)
    await expect(page).toHaveURL("/user");

    // Verify user dashboard elements are visible
    await expect(page.locator("h1")).toContainText("Hola");
  });

  test("should fail login with invalid credentials", async ({ page }) => {
    await page.goto("/login");

    // Try to login with non-existent credentials
    await page.fill("#email", "nonexistent@example.com");
    await page.fill("#password", "WrongPassword123!");

    await page.click('button[type="submit"]');

    // Should stay on login page
    await expect(page).toHaveURL("/login");

    // Should show error message
    await expect(page.locator("text=Email o contraseña incorrectos")).toBeVisible();
  });

  test("should require all fields for login", async ({ page }) => {
    await page.goto("/login");

    // Try to submit without filling fields
    await page.click('button[type="submit"]');

    // Browser validation should prevent submission
    // Check that email field is required
    const emailInput = page.locator("#email");
    await expect(emailInput).toHaveAttribute("required", "");

    const passwordInput = page.locator("#password");
    await expect(passwordInput).toHaveAttribute("required", "");
  });
});
