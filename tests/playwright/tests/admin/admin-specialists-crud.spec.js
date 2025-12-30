/**
 * @file Admin Specialist Management E2E Test.
 * @description Verifies specialist creation, service assignment, and status toggle.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

test.describe.configure({ mode: "serial" });

test.describe("Admin Specialist Management", () => {
  let connection;
  let adminUserId;
  let specialistUserId;
  let specialistId;
  const adminEmail = `admin-spec-${Date.now()}@test.com`;
  const adminPassword = "AdminPass123!";
  const specialistEmail = `specialist-${Date.now()}@test.com`;

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);

    // Create admin user
    const hash = await bcrypt.hash(adminPassword, 10);
    const [result] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Admin", "Specialist", "Admin", adminEmail, "600000777", hash, 1]
    );
    adminUserId = result.insertId;
  });

  test.afterAll(async () => {
    if (connection) {
      // Clean up specialist services
      if (specialistId) {
        await connection.execute("DELETE FROM ESPECIALISTA_SERVICIO WHERE id_especialista = ?", [
          specialistId,
        ]);
        await connection.execute("DELETE FROM ESPECIALISTA WHERE id_especialista = ?", [
          specialistId,
        ]);
      }
      // Clean up specialist user
      if (specialistUserId) {
        await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [specialistUserId]);
      }
      // Clean up admin user
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [adminUserId]);
      await connection.end();
    }
  });

  test("should create specialist, assign service, deactivate and reactivate", async ({ page }) => {
    // 1. Login as admin
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    // 2. Navigate to users management
    await page.goto("/admin/users");
    await expect(page.locator("h1")).toContainText(/Usuarios/i);

    // 3. Click "New User" button
    await page.click('button[data-bs-target="#createUserModal"]');
    await expect(page.locator("#createUserModal")).toBeVisible();

    // 4. Fill specialist creation form
    await page.fill("#createNombre", "Test");
    await page.fill("#createApellidos", "Specialist");
    await page.fill("#createEmail", specialistEmail);
    await page.fill("#createPassword", "SpecPass123!");
    await page.fill("#createPasswordConfirm", "SpecPass123!");

    // Select "Especialista" role - this will show service checkboxes
    await page.selectOption("#createRol", "Especialista");

    // Wait for services checkboxes to appear
    await page.waitForSelector("#createServicesContainer", { state: "visible", timeout: 5000 });

    // Select first available service checkbox
    const firstServiceCheckbox = page
      .locator('#createServiciosCheckboxes input[type="checkbox"]')
      .first();
    await expect(firstServiceCheckbox).toBeVisible();
    await firstServiceCheckbox.check();

    // 5. Submit form
    await page.click('#createUserForm button[type="submit"]');
    await expect(page.locator("#createUserModal")).toBeHidden();

    // Wait for page to reload/update
    await page.waitForTimeout(2000);

    // 6. Verify specialist user in database
    const [userRows] = await connection.execute(
      "SELECT id_usuario, rol FROM USUARIO WHERE email = ?",
      [specialistEmail]
    );
    expect(userRows.length).toBe(1);
    expect(userRows[0].rol).toBe("Especialista");
    specialistUserId = userRows[0].id_usuario;

    console.log(`Created specialist user with ID: ${specialistUserId}`);

    // 7. Verify ESPECIALISTA record was created
    const [specRows] = await connection.execute(
      "SELECT id_especialista FROM ESPECIALISTA WHERE id_usuario = ?",
      [specialistUserId]
    );
    expect(specRows.length).toBe(1);
    specialistId = specRows[0].id_especialista;

    console.log(`Specialist record created with ID: ${specialistId}`);

    // 8. Verify service was assigned (via UI checkbox selection)
    const [serviceAssignments] = await connection.execute(
      "SELECT id_servicio FROM ESPECIALISTA_SERVICIO WHERE id_especialista = ?",
      [specialistId]
    );
    expect(serviceAssignments.length).toBeGreaterThan(0);
    console.log(`Specialist has ${serviceAssignments.length} service(s) assigned`);

    // 9. Search for the specialist in the UI

    const filtersBtn = page.locator('button[data-bs-target="#collapseFilters"]');
    if ((await filtersBtn.getAttribute("aria-expanded")) === "false") {
      await filtersBtn.click();
    }
    const searchInput = page.locator('input[name="search"]');
    await searchInput.waitFor({ state: "visible" });
    await searchInput.fill(specialistEmail);
    await searchInput.press("Enter");

    // Wait for search results
    await page.waitForTimeout(1500);

    // 10. Verify specialist appears in table with "Activo" status
    const userRow = page.locator(`tr#user-row-${specialistUserId}`);
    await expect(userRow).toBeVisible({ timeout: 10000 });

    const statusBadge = page.locator(`.btn-toggle-status[data-user-id="${specialistUserId}"]`);
    await expect(statusBadge).toContainText("Activo");
    await expect(statusBadge).toHaveClass(/bg-success/);

    // 11. Click status badge to DEACTIVATE
    const togglePromise = page.waitForResponse(
      (response) =>
        response.url().includes(`/admin/api/users/${specialistUserId}`) &&
        response.request().method() === "POST" &&
        response.status() === 200
    );
    await statusBadge.click();
    await togglePromise;
    await page.waitForLoadState("load");

    // 12. Verify badge changes to "Inactivo"
    const statusBadgeAfter = page.locator(`.btn-toggle-status[data-user-id="${specialistUserId}"]`);
    await expect(statusBadgeAfter).toContainText("Inactivo");
    await expect(statusBadgeAfter).toHaveClass(/bg-secondary/);

    // 13. Verify in database (should be inactive)
    const [deactivatedRows] = await connection.execute(
      "SELECT activo FROM USUARIO WHERE id_usuario = ?",
      [specialistUserId]
    );
    expect(deactivatedRows[0].activo).toBe(0);

    console.log(`Specialist ${specialistUserId} deactivated`);

    // 14. Click status badge to REACTIVATE
    const togglePromise2 = page.waitForResponse(
      (response) =>
        response.url().includes(`/admin/api/users/${specialistUserId}`) &&
        response.request().method() === "POST" &&
        response.status() === 200
    );
    await statusBadgeAfter.click();
    await togglePromise2;
    await page.waitForLoadState("load");

    // 15. Verify badge changes back to "Activo"
    const statusBadgeFinal = page.locator(`.btn-toggle-status[data-user-id="${specialistUserId}"]`);
    await expect(statusBadgeFinal).toContainText("Activo");
    await expect(statusBadgeFinal).toHaveClass(/bg-success/);

    // 16. Verify in database (should be active again)
    const [reactivatedRows] = await connection.execute(
      "SELECT activo FROM USUARIO WHERE id_usuario = ?",
      [specialistUserId]
    );
    expect(reactivatedRows[0].activo).toBe(1);

    console.log(`Specialist ${specialistUserId} reactivated`);
  });
});
