/**
 * @file Admin Security Tests (Real E2E)
 * @description Verifies Role-Based Access Control (RBAC) and Admin Self-Preservation.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

// Use serial mode to avoid DB locking and ensure predictable state
test.describe.configure({ mode: "serial" });

test.describe("Admin Security & Access Control", () => {
  let connection;
  let adminUserId;
  let clientUserId;
  let specialistUserId;

  const adminEmail = `admin-sec-${Date.now()}@test.com`;
  const clientEmail = `client-sec-${Date.now()}@test.com`;
  const specialistEmail = `spec-sec-${Date.now()}@test.com`;

  const commonPassword = "TestPassword123!";

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);
    const hash = await bcrypt.hash(commonPassword, 10);

    // 1. Create Admin User
    const [adminResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Admin", "AdminSec", "User", adminEmail, "600000000", hash, 1]
    );
    adminUserId = adminResult.insertId;

    // 2. Create Client User
    const [clientResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "ClientSec", "User", clientEmail, "600000001", hash, 1]
    );
    clientUserId = clientResult.insertId;

    // 3. Create Specialist User
    const [specResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Especialista", "SpecSec", "User", specialistEmail, "600000002", hash, 1]
    );
    specialistUserId = specResult.insertId;
  });

  test.afterAll(async () => {
    if (connection) {
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario IN (?, ?, ?)", [
        adminUserId,
        clientUserId,
        specialistUserId,
      ]);
      await connection.end();
    }
  });

  // === 1. ACCESS CONTROL TESTS ===

  test("Guest (Unauthenticated) should be redirected to login when accessing admin area", async ({
    page,
  }) => {
    await page.goto("/admin/users");
    await expect(page).toHaveURL(/\/login/); // Should redirect to login
  });

  test("Client should NOT be able to access admin area", async ({ page }) => {
    // Login as Client
    await page.goto("/login");
    await page.fill('input[name="email"]', clientEmail);
    await page.fill('input[name="password"]', commonPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/"); // Wait for redirect to home

    // Attempt access
    await page.goto("/admin/users");

    // Expect redirection to home or 403.
    // Usually middleware redirects unauthorized users to home.
    await expect(page).not.toHaveURL(/\/admin\/users/);
    // Likely redirects to "/"
    const url = page.url();
    expect(url.endsWith("/") || url.includes("/login")).toBeTruthy();
  });

  test("Specialist should NOT be able to access admin area", async ({ page }) => {
    // Login as Specialist
    await page.goto("/login");
    await page.fill('input[name="email"]', specialistEmail);
    await page.fill('input[name="password"]', commonPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    // Attempt access
    await page.goto("/admin/users");

    await expect(page).not.toHaveURL(/\/admin\/users/);
  });

  test("Admin SHOULD be able to access admin area", async ({ page }) => {
    // Login as Admin
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', commonPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/"); // Might go to home first

    await page.goto("/admin/users");

    await expect(page).toHaveURL(/\/admin\/users/);
    await expect(page.locator("h1")).toContainText(/Usuarios/i);
  });

  // === 2. ADMIN SELF-PRESERVATION TESTS ===

  test("Admin should NOT be able to deactivate themselves", async ({ page }) => {
    // Login as Admin
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', commonPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    await page.goto("/admin/users");

    // Search for self
    const filtersBtn = page.locator('button[data-bs-target="#collapseFilters"]');
    if ((await filtersBtn.getAttribute("aria-expanded")) === "false") {
      await filtersBtn.click();
    }
    const searchInput = page.locator('input[name="search"]');
    await searchInput.waitFor({ state: "visible" });
    await searchInput.fill(adminEmail);
    await searchInput.press("Enter");
    await page.waitForLoadState("networkidle");

    // Verify User Row exists
    const userRow = page.locator(`tr#user-row-${adminUserId}`);
    await expect(userRow).toBeVisible();

    // Verify Status Badge is NOT a toggle button (or disabled)
    // Robust selector: Find badge with text "Activo" inside the row
    const statusBadge = userRow.locator(".badge").filter({ hasText: "Activo" });
    await expect(statusBadge).toBeVisible();

    // Use getAttribute to avoid potential toHaveClass strictness issues
    const badgeClass = await statusBadge.getAttribute("class");
    expect(badgeClass).toContain("bg-success");
    await expect(statusBadge).not.toHaveClass(/btn-toggle-status/); // Should NOT have the click handler class

    // Explicitly check it doesn't do anything if clicked (optional, but class check is strong)
    await statusBadge.click({ force: true });
    // Should still be active
    await expect(statusBadge).toContainText("Activo");
  });

  test("Admin should NOT be able to change their own role or active status in Edit Modal", async ({
    page,
  }) => {
    // Login as Admin
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', commonPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    await page.goto("/admin/users");

    // Search for self
    const filtersBtn = page.locator('button[data-bs-target="#collapseFilters"]');
    if ((await filtersBtn.getAttribute("aria-expanded")) === "false") {
      await filtersBtn.click();
    }
    const searchInput = page.locator('input[name="search"]');
    await searchInput.waitFor({ state: "visible" });
    await searchInput.fill(adminEmail);
    await searchInput.press("Enter");

    // Click Edit
    await page.locator(`.btn-edit-user[data-user-id="${adminUserId}"]`).click();

    // Wait for Modal
    const modal = page.locator("#editUserModal");
    await expect(modal).toBeVisible();

    // Verify Fields are Disabled
    const roleSelect = modal.locator("#editRol");
    const activeCheck = modal.locator("#editActivo");

    await expect(roleSelect).toBeDisabled();
    await expect(activeCheck).toBeDisabled();
  });
});
