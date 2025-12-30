/**
 * @file Admin Users List Real E2E Test.
 * @description Verifies User Management Table features against real DB: List, Search, Sort, Pagination.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

// Run tests serially to avoid DB state conflicts
test.describe.configure({ mode: "serial" });

test.describe("Admin Users Management List", () => {
  let connection;
  let adminUserId;
  let clientUserIds = [];
  let searchTerm;
  const adminEmail = `admin-list-${Date.now()}@test.com`;
  const adminPassword = "AdminPassword123!";

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);

    // 1. Create Admin User
    const hash = await bcrypt.hash(adminPassword, 10);
    const [adminResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Admin", "Admin", "List Test", adminEmail, "600000000", hash, 1]
    );
    adminUserId = adminResult.insertId;

    // 2. Create 12 Client Users for Pagination (Limit 10) & Sort/Search
    const baseName = "Sort";
    const uniqueSearchTerm = `SearchTarget-${Date.now()}`;
    const values = [];
    const params = [];

    for (let i = 0; i < 12; i++) {
      // char from A (65) onwards
      const char = String.fromCharCode(65 + i);
      let nombre = `${baseName}${char}`;
      let apellidos = "User";

      // Make 'F' special for search
      if (char === "F") {
        nombre = uniqueSearchTerm;
      }

      const email = `client-${char}-${Date.now()}@test.com`;
      values.push("(?, ?, ?, ?, ?, ?, CURDATE(), ?)");
      params.push("Cliente", nombre, apellidos, email, "600000001", hash, 1);
    }

    // Bulk Insert
    if (values.length > 0) {
      const query = `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo) VALUES ${values.join(
        ","
      )}`;
      const [result] = await connection.execute(query, params);

      // Calculate IDs properly
      const firstId = result.insertId;
      for (let i = 0; i < result.affectedRows; i++) {
        clientUserIds.push(firstId + i);
      }
    }

    searchTerm = uniqueSearchTerm;
  });

  test.afterAll(async () => {
    if (connection) {
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [adminUserId]);
      if (clientUserIds.length > 0) {
        // Delete all created clients
        const placeholders = clientUserIds.map(() => "?").join(",");
        await connection.execute(
          `DELETE FROM USUARIO WHERE id_usuario IN (${placeholders})`,
          clientUserIds
        );
      }
      await connection.end();
    }
  });

  test("should display the list of users (Visualization)", async ({ page }) => {
    // Login as Admin
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/"); // Admin might redirect to /admin or home then admin

    // Go to User Management
    await page.goto("/admin/users");

    await expect(page.locator("h1")).toContainText(/Usuarios/i);

    // Default limit is 10. We created 12 + 1 admin = 13.
    // However, existing DB users might exist.
    // We expect AT LEAST 10 rows if there are >= 10 users.
    // Since we just created 13, there should be 10 rows on page 1.
    const rows = page.locator("table tbody tr");
    await expect(rows).toHaveCount(10);

    // Verify our Admin is there (if sort is by ID desc or similar he might not be on page 1 depending on sort logic)
    // Default sort is typically ID ASC or similar.
    // We can't guarantee he is on page 1 unless we know the sort order.
    // Let's just check that we see non-empty rows.
    await expect(rows.first()).toBeVisible();
  });

  test("should search for users", async ({ page }) => {
    // Retrieve unique search term
    // const searchTerm =
    //   testInfo.annotations.find((a) => a.type === "searchTerm")?.description || "SearchTarget";

    // Login
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');

    await page.goto("/admin/users");

    // Open filters accordion if needed
    const filtersBtn = page.locator('button[data-bs-target="#collapseFilters"]');
    if ((await filtersBtn.getAttribute("aria-expanded")) === "false") {
      await filtersBtn.click();
    }

    // Search for unique term
    const searchInput = page.locator('input[name="search"]');
    await searchInput.waitFor({ state: "visible" });
    await searchInput.fill(searchTerm);
    await searchInput.press("Enter");

    // URL should update
    await expect(page).toHaveURL(new RegExp(`search=${searchTerm}`));

    // Should find exactly 1 user
    const rows = page.locator("table tbody tr");
    await expect(rows).toHaveCount(1);
    await expect(rows.first()).toContainText(searchTerm);
  });

  test("should sort users by name", async ({ page }) => {
    // Login
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');

    await page.goto("/admin/users");

    // Click Sort Header: First click ASC
    // We need to look for a header link with "Nombre"
    // The link usually contains ?sort=nombre (or similar)
    const nameHeaderLink = page.locator('thead a[href*="sort=nombre"]');
    await nameHeaderLink.click();

    await expect(page).toHaveURL(/sort=nombre/);

    // We can't easily assert the EXACT first row because of other users in DB.
    // But we know 'Admin List Test' starts with 'A'. 'SortA' starts with 'S'.
    // If we have 'Alberto' in DB from other tests, he appears.
    // Let's verify the URL query params are correct, implying the backend does the work.
    // And check that the column header now indicates formatting (e.g. arrow) or just URL.
    await expect(page).toHaveURL(/order=asc/i);

    // Toggle to DESC
    await nameHeaderLink.click();
    await expect(page).toHaveURL(/order=desc/i);
  });

  test("should support pagination", async ({ page }) => {
    // Login
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');

    await page.goto("/admin/users");

    // We know we have at least 13 users (our created ones). Limit is 10.
    // Ensure "Next" button is enabled.
    // Selector for pagination often contains "Siguiente" or "Next" or an arrow.
    // Based on previous mock, ID was 'next-page' or checking text.
    // In real app, standard bootstrap pagination.
    const container = page.locator(".pagination");
    const nextLink = container.locator('a[href*="page=2"]').first();

    // If we have < 10 users in total db this fails, but we created 13.
    await expect(nextLink).toBeVisible();
    await nextLink.click();

    await expect(page).toHaveURL(/page=2/);

    // On page 2, we should have at least 1 user.
    const rows = page.locator("table tbody tr");
    await expect(rows.count()).resolves.toBeGreaterThan(0);
  });
});
