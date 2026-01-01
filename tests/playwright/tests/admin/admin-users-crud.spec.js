/**
 * @file Admin User Management Real E2E Test.
 * @description Verifies full CRUD lifecycle for users: Create, Edit, Toggle Status.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

// Run serially so we can track the same user across tests
test.describe.configure({ mode: "serial" });

test.describe("Admin User Management Lifecycle", () => {
  let connection;
  let adminUserId;
  let testUserId; // ID of the user we create/edit/delete

  const adminEmail = `admin-manage-${Date.now()}@test.com`;
  const adminPassword = "AdminPassword123!";

  // Dynamic user data
  const testUserEmail = `newuser-${Date.now()}@test.com`;
  const testUserInitialName = "NewCreated";
  const testUserUpdatedName = "UpdatedName";

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);

    // 1. Create Admin User
    const hash = await bcrypt.hash(adminPassword, 10);
    const [adminResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Admin", "Admin", "Manager", adminEmail, "600000000", hash, 1]
    );
    adminUserId = adminResult.insertId;
  });

  test.afterAll(async () => {
    if (connection) {
      // Clean up Admin
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [adminUserId]);

      // Clean up Test User if it exists (in case test failed before manual cleanup or to be safe)
      if (testUserId) {
        await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [testUserId]);
      } else {
        // Fallback cleanup by email just in case ID wasn't captured
        await connection.execute("DELETE FROM USUARIO WHERE email = ?", [testUserEmail]);
      }

      await connection.end();
    }
  });

  test("should create a new client user successfully", async ({ page }) => {
    // Login
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');

    // Go to /admin/users (login might redirect there or to admin dashboard)
    await page.waitForURL("/");
    await page.goto("/admin/users");

    // Open Create Modal
    await page.click('button[data-bs-target="#createUserModal"]');
    await expect(page.locator("#createUserModal")).toBeVisible();

    // Fill Form
    await page.fill("#createNombre", testUserInitialName);
    await page.fill("#createApellidos", "User");
    await page.fill("#createEmail", testUserEmail);
    await page.fill("#createPassword", "UserPass123!");
    await page.fill("#createPasswordConfirm", "UserPass123!");
    await page.selectOption("#createRol", "Cliente");

    // Submit
    // Note: Use a more specific selector for the submit button inside the form if needed
    await page.click('#createUserForm button[type="submit"]');

    // Wait for success message via Toast
    await expect(page.locator("toast-notification")).toContainText("creado correctamente", {
      timeout: 10000,
    });
    await expect(page.locator("#createUserModal")).toBeHidden();

    // Verify in DB
    const [rows] = await connection.execute(
      "SELECT id_usuario, nombre, email FROM USUARIO WHERE email = ?",
      [testUserEmail]
    );
    expect(rows.length).toBe(1);
    expect(rows[0].nombre).toBe(testUserInitialName);
    testUserId = rows[0].id_usuario;

    // Verify in UI (Search for it to be sure it appears)
    // We reuse search logic: Open accordion if needed
    const filtersBtn = page.locator('button[data-bs-target="#collapseFilters"]');
    if ((await filtersBtn.getAttribute("aria-expanded")) === "false") {
      await filtersBtn.click();
    }
    const searchInput = page.locator('input[name="search"]');
    await searchInput.waitFor({ state: "visible" });
    await searchInput.fill(testUserEmail);
    await searchInput.press("Enter");

    await expect(page.locator(`tr#user-row-${testUserId}`)).toBeVisible();
  });

  test("should edit an existing user", async ({ page }) => {
    // Assuming we are logged in from previous test or session state?
    // Playwright creates new context for each test unless we configure otherwise.
    // Serial mode reuses the worker but usually NOT the browser context/page unless verified.
    // Actually, serial mode just runs them in order in the SAME worker.
    // New context is created per test by default. We must re-login.

    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');
    await page.goto("/admin/users");

    // Search to find the user row easily
    const filtersBtn = page.locator('button[data-bs-target="#collapseFilters"]');
    if ((await filtersBtn.getAttribute("aria-expanded")) === "false") {
      await filtersBtn.click();
    }
    const searchInput = page.locator('input[name="search"]');
    await searchInput.waitFor({ state: "visible" });
    await searchInput.fill(testUserEmail);
    await searchInput.press("Enter");

    // Click Edit Button
    // We can use the data attribute or row ID
    await page.click(`.btn-edit-user[data-user-id="${testUserId}"]`);
    await expect(page.locator("#editUserModal")).toBeVisible();

    // Verify fields pre-filled (optional, but good practice)
    await expect(page.locator("#editNombre")).toHaveValue(testUserInitialName);

    // Update Name
    await page.fill("#editNombre", testUserUpdatedName);

    // Submit
    await page.click('#editUserForm button[type="submit"]');
    await expect(page.locator("#editUserModal")).toBeHidden();

    // Verify in DB
    const [rows] = await connection.execute("SELECT nombre FROM USUARIO WHERE id_usuario = ?", [
      testUserId,
    ]);
    expect(rows[0].nombre).toBe(testUserUpdatedName);

    // Verify in UI
    await expect(page.locator(`tr#user-row-${testUserId}`)).toContainText(testUserUpdatedName);
  });

  test("should toggle user status (Deactivate/Activate)", async ({ page }) => {
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');
    await page.goto("/admin/users");

    // Search again
    const filtersBtn = page.locator('button[data-bs-target="#collapseFilters"]');
    if ((await filtersBtn.getAttribute("aria-expanded")) === "false") {
      await filtersBtn.click();
    }
    const searchInput = page.locator('input[name="search"]');
    await searchInput.waitFor({ state: "visible" });
    await searchInput.fill(testUserEmail);
    await searchInput.press("Enter");

    // Check current status (Active = 1)
    const toggleBtn = page.locator(`.btn-toggle-status[data-user-id="${testUserId}"]`);
    await expect(toggleBtn).toContainText("Activo");
    await expect(toggleBtn).toHaveClass(/bg-success/);

    // Click to Deactivate
    const togglePromise = page.waitForResponse(
      (response) =>
        response.url().includes(`/admin/api/users/${testUserId}`) &&
        response.request().method() === "POST" &&
        response.status() === 200
    );

    await toggleBtn.click();

    // Wait for API result
    await togglePromise;
    // const json = await response.json(); // Causes protocol error on reload
    // console.log('Toggle response:', json);

    // Wait for reload (which happens on success)
    await page.waitForLoadState("load");

    // Re-locate element after reload
    const toggleBtnAfter = page.locator(`.btn-toggle-status[data-user-id="${testUserId}"]`);

    // It might confirm via alert or just toggle?
    // Looking at UsersManagement.latte: just a span with btn-toggle-status class.
    // Likely JS handler. Assuming it updates UI immediately or after reload/ajax.
    // Let's explicitly wait for change
    await expect(toggleBtnAfter).toContainText("Inactivo");
    await expect(toggleBtnAfter).toHaveClass(/bg-secondary/);

    // Verify in DB
    const [rows] = await connection.execute("SELECT activo FROM USUARIO WHERE id_usuario = ?", [
      testUserId,
    ]);
    expect(rows[0].activo).toBe(0);

    // Click to Activate
    const togglePromise2 = page.waitForResponse(
      (response) =>
        response.url().includes(`/admin/api/users/${testUserId}`) &&
        response.request().method() === "POST" &&
        response.status() === 200
    );
    await toggleBtnAfter.click();
    await togglePromise2;
    await page.waitForLoadState("load");

    const toggleBtnFinal = page.locator(`.btn-toggle-status[data-user-id="${testUserId}"]`);
    await expect(toggleBtnFinal).toContainText("Activo");
    await expect(toggleBtnFinal).toHaveClass(/bg-success/);

    const [rows2] = await connection.execute("SELECT activo FROM USUARIO WHERE id_usuario = ?", [
      testUserId,
    ]);
    expect(rows2[0].activo).toBe(1);
  });
});
