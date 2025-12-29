/**
 * @file Password recovery flow tests.
 * @project app-reservas
 */

const { test, expect } = require("@playwright/test");
const fs = require("fs");
const path = require("path");
const crypto = require("crypto");

/**
 * Extracts the password reset token from the token file
 * @param {string} email - Email address to search for
 * @returns {Promise<string|null>} The reset token or null if not found
 */
const getResetTokenFromFile = async (email) => {
  try {
    // Calculate the filename (same hash as PHP uses)
    const emailHash = crypto.createHash("md5").update(email).digest("hex");
    const tokenFile = path.join(__dirname, "..", "..", "..", ".tokens", `${emailHash}.txt`);

    // Wait a bit for the file to be created
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Read the token from file
    if (fs.existsSync(tokenFile)) {
      const token = fs.readFileSync(tokenFile, "utf8").trim();
      return token || null;
    }

    return null;
  } catch (error) {
    console.error("Error reading token from file:", error);
    return null;
  }
};

test.describe("Password Recovery", () => {
  test("should complete password recovery flow with real email", async ({ page }) => {
    // Register a new user with real email
    await page.goto("/register");

    const timestamp = Date.now();
    const email = `sr.tu.peluqueria+test${timestamp}@gmail.com`;
    const oldPassword = "OldPassword123!";
    const newPassword = "NewPassword456!";

    await page.fill("#nombre", "Test");
    await page.fill("#apellidos", "User");
    await page.fill("#email", email);
    await page.fill("#telefono", "600123456");
    await page.fill("#password", oldPassword);
    await page.fill("#password-confirm", oldPassword);

    await page.click('button[type="submit"]');
    await page.waitForURL("**/login");

    // Login to ensure user is saved in database
    await page.fill("#email", email);
    await page.fill("#password", oldPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("**/");

    // Now logout and go to forgot password page
    await page.goto("/logout");
    await page.goto("/forgot-password");

    // Verify we're on the forgot password page
    await expect(page.locator("h2")).toContainText(/Recuperar [Cc]ontraseña/);

    // Submit email for password recovery
    await page.fill("#email", email);
    await page.click('button[type="submit"]');

    // Verify success message
    await expect(page.locator("text=Si el email existe, recibirás instrucciones")).toBeVisible();

    // Wait for email to be sent and token file to be created
    await page.waitForTimeout(2000);

    // Get the reset token from file
    const token = await getResetTokenFromFile(email);
    expect(token).not.toBeNull();
    expect(token).toBeTruthy();

    console.log(`Retrieved reset token: ${token?.substring(0, 10)}...`);

    // Navigate to reset password page with token
    await page.goto(`/reset-password?token=${token}`);

    // Verify we're on the reset password page
    await expect(page.locator("h2")).toContainText(/Nueva [Cc]ontraseña/);

    // Fill new password
    await page.fill("#password", newPassword);
    await page.fill("#password-confirm", newPassword);

    await page.click('button[type="submit"]');

    // Should redirect to login page with success message
    await page.waitForURL("**/login");
    await expect(page.locator("text=Contraseña actualizada correctamente")).toBeVisible();

    // Try to login with OLD password (should fail)
    await page.fill("#email", email);
    await page.fill("#password", oldPassword);
    await page.click('button[type="submit"]');

    // Should show error
    await expect(page.locator("text=Email o contraseña incorrectos")).toBeVisible();

    // Now login with NEW password (should succeed)
    await page.fill("#email", email);
    await page.fill("#password", newPassword);
    await page.click('button[type="submit"]');

    // Should redirect to home page
    await page.waitForURL("**/");

    // Verify we can access user dashboard
    await page.goto("/user");
    await expect(page).toHaveURL("/user");
    await expect(page.locator("h1")).toContainText("Hola");
  });

  test("should show forgot password form and accept email submission", async ({ page }) => {
    // Go to forgot password page
    await page.goto("/forgot-password");

    // Verify we're on the forgot password page
    await expect(page.locator("h2")).toContainText(/Recuperar [Cc]ontraseña/);

    // Verify form elements exist
    await expect(page.locator("#email")).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();

    // Submit email for password recovery
    const email = `sr.tu.peluqueria+test${Date.now()}@gmail.com`;
    await page.fill("#email", email);
    await page.click('button[type="submit"]');

    // Verify success message
    await expect(page.locator("text=Si el email existe, recibirás instrucciones")).toBeVisible();
  });

  test("should require email field", async ({ page }) => {
    await page.goto("/forgot-password");

    // Try to submit without email
    await page.click('button[type="submit"]');

    // Browser validation should prevent submission
    const emailInput = page.locator("#email");
    await expect(emailInput).toHaveAttribute("required", "");
  });
});
