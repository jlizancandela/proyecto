/**
 * @file Admin Service Management E2E Test.
 * @description Verifies service CRUD operations and status toggle.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

test.describe.configure({ mode: "serial" });

test.describe("Admin Service Management", () => {
  let connection;
  let adminUserId;
  let testServiceId;
  const adminEmail = `admin-services-${Date.now()}@test.com`;
  const adminPassword = "AdminPass123!";
  const serviceName = `Test Service ${Date.now()}`;

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);

    // Create admin user
    const hash = await bcrypt.hash(adminPassword, 10);
    const [result] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Admin", "Service", "Admin", adminEmail, "600000888", hash, 1]
    );
    adminUserId = result.insertId;
  });

  test.afterAll(async () => {
    if (connection) {
      // Clean up test service
      if (testServiceId) {
        await connection.execute("DELETE FROM SERVICIO WHERE id_servicio = ?", [testServiceId]);
      }
      // Clean up admin user
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [adminUserId]);
      await connection.end();
    }
  });

  test("should create service, deactivate it, and reactivate it", async ({ page }) => {
    // 1. Login as admin
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    // 2. Navigate to services management
    await page.goto("/admin/services");
    await expect(page.locator("h1")).toContainText(/Gestión de Servicios/i);

    // 3. Click "New Service" button (floating button)
    const newServiceBtn = page.locator('button[data-bs-target="#createServiceModal"]');
    await expect(newServiceBtn).toBeVisible();
    await newServiceBtn.click();

    // 4. Fill service creation form
    await page.fill("#createNombreServicio", serviceName);
    await page.fill("#createDescripcion", "Test service description");
    await page.fill("#createDuracion", "60");
    await page.fill("#createPrecio", "50.00");

    // 5. Submit form
    const submitBtn = page.locator('#createServiceModal button[type="submit"]');
    await submitBtn.click();

    // Wait for modal to close and table to reload
    await page.waitForTimeout(2000);

    // 6. Verify service appears in table
    const serviceRow = page.locator(`tr:has-text("${serviceName}")`);
    await expect(serviceRow).toBeVisible({ timeout: 10000 });

    // 7. Verify in database (should be active)
    const [rows] = await connection.execute(
      "SELECT id_servicio, nombre_servicio, activo FROM SERVICIO WHERE nombre_servicio = ?",
      [serviceName]
    );
    expect(rows.length).toBe(1);
    expect(rows[0].activo).toBe(1);
    testServiceId = rows[0].id_servicio;

    console.log(`Created service with ID: ${testServiceId}`);

    // 8. Verify status badge shows "Activo"
    const statusBadge = serviceRow.locator("span.badge.btn-toggle-status");
    await expect(statusBadge).toBeVisible();
    await expect(statusBadge).toContainText(/Activo/i);
    await expect(statusBadge).toHaveClass(/bg-success/);

    // 9. Click status badge to DEACTIVATE
    await statusBadge.click();

    // Wait for status update
    await page.waitForTimeout(1500);

    // 10. Verify badge changes to "Inactivo"
    const updatedBadge = serviceRow.locator("span.badge.btn-toggle-status");
    await expect(updatedBadge).toContainText(/Inactivo/i);
    await expect(updatedBadge).toHaveClass(/bg-secondary/);

    // 11. Verify in database (should be inactive)
    const [deactivatedRows] = await connection.execute(
      "SELECT activo FROM SERVICIO WHERE id_servicio = ?",
      [testServiceId]
    );
    expect(deactivatedRows[0].activo).toBe(0);

    console.log(`Service ${testServiceId} deactivated`);

    // 12. Click status badge to REACTIVATE
    await updatedBadge.click();

    // Wait for status update
    await page.waitForTimeout(1500);

    // 13. Verify badge changes back to "Activo"
    const reactivatedBadge = serviceRow.locator("span.badge.btn-toggle-status");
    await expect(reactivatedBadge).toContainText(/Activo/i);
    await expect(reactivatedBadge).toHaveClass(/bg-success/);

    // 14. Verify in database (should be active again)
    const [reactivatedRows] = await connection.execute(
      "SELECT activo FROM SERVICIO WHERE id_servicio = ?",
      [testServiceId]
    );
    expect(reactivatedRows[0].activo).toBe(1);

    console.log(`Service ${testServiceId} reactivated`);
  });
});
