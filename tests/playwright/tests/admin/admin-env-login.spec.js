/**
 * @file Admin Environment Login Test (Real E2E)
 * @description Verifies that admin login works with credentials from .env file.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

test.describe("Admin Environment Login", () => {
  let connection;
  let adminUserId;
  let userCreated = false;

  const adminEmail = process.env.ADMIN_EMAIL;
  const adminPassword = process.env.ADMIN_PASSWORD;

  test.beforeAll(async () => {
    // Verify env variables are defined
    expect(adminEmail).toBeDefined();
    expect(adminPassword).toBeDefined();

    console.log(`Testing with Env User: ${adminEmail}`);

    connection = await mysql.createConnection(dbConfig);

    // Check if admin user exists
    const [rows] = await connection.execute("SELECT id_usuario FROM USUARIO WHERE email = ?", [
      adminEmail,
    ]);

    if (rows.length === 0) {
      // Create admin user if it doesn't exist
      const hash = await bcrypt.hash(adminPassword, 10);
      const [result] = await connection.execute(
        `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
         VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
        ["Admin", "EnvAdmin", "Test", adminEmail, "600000999", hash, 1]
      );
      adminUserId = result.insertId;
      userCreated = true;
      console.log(`Created test admin user with ID: ${adminUserId}`);
    } else {
      adminUserId = rows[0].id_usuario;
      console.log(`Using existing admin user with ID: ${adminUserId}`);
    }
  });

  test.afterAll(async () => {
    if (connection) {
      // Only delete if we created the user
      if (userCreated && adminUserId) {
        await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [adminUserId]);
        console.log(`Cleaned up test admin user with ID: ${adminUserId}`);
      }
      await connection.end();
    }
  });

  test("should login successfully with .env credentials", async ({ page }) => {
    // Navigate to login
    await page.goto("/login");

    // Fill credentials from .env
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);

    // Submit form
    await page.click('button[type="submit"]');

    // Verify successful redirect (to home or admin)
    await page.waitForURL("/");

    // Verify session is established by checking for user name or navigating to protected route
    await page.goto("/admin/users");
    await expect(page).toHaveURL(/\/admin\/users/);

    // Verify admin panel is accessible
    await expect(page.locator("h1")).toContainText(/Usuarios/i);
  });
});
