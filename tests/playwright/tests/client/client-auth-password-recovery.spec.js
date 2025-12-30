/**
 * @file Password recovery flow E2E test.
 * @description Verifies password reset functionality using real database.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

test.describe("Password Recovery E2E", () => {
  let connection;
  let testUserId;
  const testEmail = `pwreset-${Date.now()}@test.com`;
  const oldPassword = "OldPassword123!";
  const newPassword = "NewPassword456!";

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);

    // Create test user
    const hash = await bcrypt.hash(oldPassword, 10);
    const [result] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "Password", "Test", testEmail, "600000999", hash, 1]
    );
    testUserId = result.insertId;
  });

  test.afterAll(async () => {
    if (connection) {
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [testUserId]);
      await connection.end();
    }
  });

  test("should complete full password recovery flow", async ({ page }) => {
    // 1. Request password reset
    await page.goto("/forgot-password");
    await expect(page.locator("h2")).toContainText(/Recuperar [Cc]ontraseña/);

    await page.fill("#email", testEmail);
    await page.click('button[type="submit"]');

    // Verify success message
    await expect(page.locator("text=Si el email existe, recibirás instrucciones")).toBeVisible();

    // 2. Get reset token from database
    await page.waitForTimeout(1000); // Wait for token to be generated

    const [rows] = await connection.execute(
      "SELECT reset_token FROM USUARIO WHERE id_usuario = ?",
      [testUserId]
    );

    expect(rows.length).toBe(1);
    const token = rows[0].reset_token;
    expect(token).toBeTruthy();
    expect(token.length).toBeGreaterThan(0);

    console.log(`Retrieved reset token from DB: ${token.substring(0, 10)}...`);

    // 3. Navigate to reset password page with token
    await page.goto(`/reset-password?token=${token}`);
    await expect(page.locator("h2")).toContainText(/Nueva [Cc]ontraseña/);

    // 4. Set new password
    await page.fill("#password", newPassword);
    await page.fill("#password-confirm", newPassword);
    await page.click('button[type="submit"]');

    // Should redirect to login with success message
    await page.waitForURL("**/login");
    await expect(page.locator("text=Contraseña actualizada correctamente")).toBeVisible();

    // 5. Verify old password no longer works
    await page.fill("#email", testEmail);
    await page.fill("#password", oldPassword);
    await page.click('button[type="submit"]');
    await expect(page.locator("text=Email o contraseña incorrectos")).toBeVisible();

    // 6. Verify new password works
    await page.fill("#email", testEmail);
    await page.fill("#password", newPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("**/");

    // Verify successful login
    await page.goto("/user");
    await expect(page).toHaveURL("/user");
  });

  test("should show forgot password form", async ({ page }) => {
    await page.goto("/forgot-password");
    await expect(page.locator("h2")).toContainText(/Recuperar [Cc]ontraseña/);
    await expect(page.locator("#email")).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
  });

  test("should require email field", async ({ page }) => {
    await page.goto("/forgot-password");
    await page.click('button[type="submit"]');

    const emailInput = page.locator("#email");
    await expect(emailInput).toHaveAttribute("required", "");
  });
});
