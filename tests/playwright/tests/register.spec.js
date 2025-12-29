/**
 * @file Registration flow tests.
 * @project app-reservas
 */

const { test, expect } = require("@playwright/test");

test.describe("Registration", () => {
  test("should show register page and validate fields", async ({ page }) => {
    await page.goto("/register");

    await expect(page.locator("h2")).toContainText("Crear cuenta");

    // Test empty fields
    await page.click('button[type="submit"]');
    await expect(page.locator("#nombre")).toHaveClass(/is-invalid/);
    await expect(page.locator("#apellidos")).toHaveClass(/is-invalid/);
    await expect(page.locator("#email")).toHaveClass(/is-invalid/);
    await expect(page.locator("#password")).toHaveClass(/is-invalid/);

    // Test invalid email
    await page.fill('input[name="nombre"]', "Test User");
    await page.fill('input[name="apellidos"]', "Test Apellidos");
    await page.fill('input[name="email"]', "not-an-email");
    await page.fill('input[name="password"]', "password123");
    await page.fill('input[name="password-confirm"]', "password123");
    await page.click('button[type="submit"]');
    await expect(page.locator("#email")).toHaveClass(/is-invalid/);

    // Test password mismatch
    await page.fill('input[name="email"]', "test@example.com");
    await page.fill('input[name="password"]', "password123");
    await page.fill('input[name="password-confirm"]', "password456");
    await page.click('button[type="submit"]');
    await expect(page.locator("#password-confirm")).toHaveClass(/is-invalid/);

    // Test successful registration
    const email = `test.user+${Date.now()}@example.com`;
    await page.fill('input[name="nombre"]', "Test User");
    await page.fill('input[name="apellidos"]', "Test Apellidos");
    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', "password123");
    await page.fill('input[name="password-confirm"]', "password123");
    await page.click('button[type="submit"]');

    // It should redirect to login page
    await expect(page).toHaveURL("/login");
    await expect(page.locator("text=Cuenta creada con éxito")).toBeVisible();
  });
});
