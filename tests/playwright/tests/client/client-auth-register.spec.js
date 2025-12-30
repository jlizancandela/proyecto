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

  test("should toggle password visibility with eye button", async ({ page }) => {
    // Test on registration page
    await page.goto("/register");

    // Fill password field
    await page.fill("#password", "MySecretPassword123!");

    // Initially password should be hidden (type="password")
    await expect(page.locator("#password")).toHaveAttribute("type", "password");

    // Verify the eye icon is showing (bi-eye)
    const eyeIcon = page.locator("#toggle-password i");
    await expect(eyeIcon).toHaveClass(/bi-eye/);

    // Click the eye button to show password
    const passwordToggle = page.locator("#toggle-password");
    await passwordToggle.click();

    // Password should now be visible (type="text")
    await expect(page.locator("#password")).toHaveAttribute("type", "text");

    // Verify the icon changed to eye-slash
    await expect(eyeIcon).toHaveClass(/bi-eye-slash/);

    // Click again to hide password
    await passwordToggle.click();

    // Password should be hidden again (type="password")
    await expect(page.locator("#password")).toHaveAttribute("type", "password");

    // Verify the icon changed back to eye
    await expect(eyeIcon).toHaveClass(/bi-eye/);

    // Test on login page
    await page.goto("/login");

    // Fill password field
    await page.fill("#password", "AnotherPassword456!");

    // Initially password should be hidden (type="password")
    await expect(page.locator("#password")).toHaveAttribute("type", "password");

    // Verify the eye icon is showing (bi-eye)
    const loginEyeIcon = page.locator("#toggle-password i");
    await expect(loginEyeIcon).toHaveClass(/bi-eye/);

    // Click the eye button to show password
    const loginPasswordToggle = page.locator("#toggle-password");
    await loginPasswordToggle.click();

    // Password should now be visible (type="text")
    await expect(page.locator("#password")).toHaveAttribute("type", "text");

    // Verify the icon changed to eye-slash
    await expect(loginEyeIcon).toHaveClass(/bi-eye-slash/);

    // Click again to hide password
    await loginPasswordToggle.click();

    // Password should be hidden again (type="password")
    await expect(page.locator("#password")).toHaveAttribute("type", "password");

    // Verify the icon changed back to eye
    await expect(loginEyeIcon).toHaveClass(/bi-eye/);
  });
});
