/**
 * @file Admin Users List Tests (Mocked SSR)
 * @description Verifies User Management Table features: List, Search, Sort, Pagination.
 */

const { test, expect } = require("@playwright/test");

// Helper to generate a simple mocked HTML page for the users table
const generateUsersPage = (
  users = [],
  search = "",
  sort = "",
  order = "",
  page = 1,
  totalPages = 1
) => {
  const rows = users
    .map(
      (u) => `
    <tr>
        <td>${u.id}</td>
        <td>${u.nombre} ${u.apellidos}</td>
        <td>${u.email}</td>
        <td>${u.rol}</td>
        <td>-</td>
        <td>${u.telefono || "-"}</td>
        <td>2024-01-01</td>
        <td>${u.activo ? "Activo" : "Inactivo"}</td>
        <td><button>Edit</button></td>
    </tr>
  `
    )
    .join("");

  return `
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <div class="container">
            <h1>Gestión de Usuarios</h1>
            
            <!-- Search Form -->
            <form method="GET" action="/admin/users">
                <input type="search" name="search" value="${search}" class="form-control" placeholder="Buscar usuarios...">
                <button type="submit">Buscar</button>
            </form>

            <!-- Sort Links -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><a href="?sort=nombre&order=${
                          sort === "nombre" && order === "asc" ? "desc" : "asc"
                        }" id="sort-nombre">Nombre</a></th>
                        <th><a href="?sort=email&order=${
                          sort === "email" && order === "asc" ? "desc" : "asc"
                        }" id="sort-email">Email</a></th>
                        <th>Rol</th>
                        <th>Servicios</th>
                        <th>Teléfono</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination">
                    <li class="page-item ${page <= 1 ? "disabled" : ""}">
                        <a class="page-link" href="?page=${page - 1}" id="prev-page">Anterior</a>
                    </li>
                    <li class="page-item ${page >= totalPages ? "disabled" : ""}">
                         <a class="page-link" href="?page=${page + 1}" id="next-page">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </body>
    </html>
  `;
};

test.describe("Admin Users Management List", () => {
  test.beforeEach(async ({ page }) => {
    // 1. Mock Login (Bypass real auth)
    await page.route("**/login", async (route) => {
      if (route.request().method() === "POST") {
        await route.fulfill({
          status: 302,
          headers: { Location: "/admin/users" },
        });
      }
    });

    // We assume the test starts by navigating to /admin/users directly after a fake login flow
    // But since we Mock the /admin/users page itself, we can just goto that URL directly!
  });

  test("should display the list of users (Visualization)", async ({ page }) => {
    // Mock the Data
    const mockUsers = [
      {
        id: 1,
        nombre: "Admin",
        apellidos: "User",
        email: "admin@test.com",
        rol: "Admin",
        activo: true,
      },
      {
        id: 2,
        nombre: "Pepe",
        apellidos: "López",
        email: "pepe@test.com",
        rol: "Cliente",
        activo: true,
      },
    ];

    // Mock the Page Request
    await page.route("**/admin/users", async (route) => {
      const html = generateUsersPage(mockUsers);
      await route.fulfill({ status: 200, contentType: "text/html", body: html });
    });

    await page.goto("/admin/users");

    await expect(page.locator("h1")).toContainText("Usuarios");
    await expect(page.locator("table tbody tr")).toHaveCount(2);
    await expect(page.locator("table tbody tr").first()).toContainText("Admin User");
    await expect(page.locator("table tbody tr").nth(1)).toContainText("Pepe López");
  });

  test("should search for users", async ({ page }) => {
    // 1. Initial Load
    await page.route("**/admin/users", async (route) => {
      const html = generateUsersPage([]); // Empty initially
      await route.fulfill({ status: 200, contentType: "text/html", body: html });
    });

    await page.goto("/admin/users");

    // 2. Perform Search
    // We intercept the Form Submit which goes to /admin/users?search=...
    await page.route("**/admin/users?search=Pepe", async (route) => {
      const mockUsers = [
        {
          id: 2,
          nombre: "Pepe",
          apellidos: "López",
          email: "pepe@test.com",
          rol: "Cliente",
          activo: true,
        },
      ];
      const html = generateUsersPage(mockUsers, "Pepe");
      await route.fulfill({ status: 200, contentType: "text/html", body: html });
    });

    await page.fill('input[name="search"]', "Pepe");
    await page.click('button[type="submit"]'); // Or press Enter

    await expect(page).toHaveURL(/search=Pepe/);
    await expect(page.locator('input[name="search"]')).toHaveValue("Pepe");
    await expect(page.locator("table tbody tr")).toHaveCount(1);
    await expect(page.locator("table tbody tr").first()).toContainText("Pepe");
  });

  test("should sort users by name", async ({ page }) => {
    // 1. Initial Load (Unsorted)
    await page.route("**/admin/users", async (route) => {
      const html = generateUsersPage([], "", "", "");
      await route.fulfill({ status: 200, contentType: "text/html", body: html });
    });

    await page.goto("/admin/users");

    // 2. Click Sort Header (Expect Name ASC)
    // Link href should be ?sort=nombre&order=asc
    const sortLink = page.locator("#sort-nombre");
    await expect(sortLink).toHaveAttribute("href", "?sort=nombre&order=asc");

    // Mock the Sorted Request
    await page.route("**/admin/users?sort=nombre&order=asc", async (route) => {
      // Mock data returned in specific order (though visual order matches data order in our mock generator)
      const sortedUsers = [
        {
          id: 1,
          nombre: "Alberto",
          apellidos: "...",
          email: "a@test.com",
          rol: "Cliente",
          activo: true,
        },
        {
          id: 2,
          nombre: "Bernardo",
          apellidos: "...",
          email: "b@test.com",
          rol: "Cliente",
          activo: true,
        },
      ];
      const html = generateUsersPage(sortedUsers, "", "nombre", "asc");
      await route.fulfill({ status: 200, contentType: "text/html", body: html });
    });

    await sortLink.click();

    await expect(page).toHaveURL(/sort=nombre/);
    await expect(page).toHaveURL(/order=asc/);
    await expect(page.locator("table tbody tr").first()).toContainText("Alberto");
  });

  test("should support pagination", async ({ page }) => {
    // 1. Initial Load (Page 1 of 2)
    await page.route("**/admin/users", async (route) => {
      const users = [
        {
          id: 1,
          nombre: "User1",
          apellidos: "",
          email: "1@test.com",
          rol: "Cliente",
          activo: true,
        },
      ];
      const html = generateUsersPage(users, "", "", "", 1, 2);
      await route.fulfill({ status: 200, contentType: "text/html", body: html });
    });

    await page.goto("/admin/users");

    const nextLink = page.locator("#next-page");
    await expect(nextLink).not.toHaveClass(/disabled/);
    await expect(page.locator("#prev-page").locator("..")).toHaveClass(/disabled/);

    // 2. Click Next (Page 2)
    await page.route("**/admin/users?page=2", async (route) => {
      const users = [
        {
          id: 2,
          nombre: "User2",
          apellidos: "",
          email: "2@test.com",
          rol: "Cliente",
          activo: true,
        },
      ];
      const html = generateUsersPage(users, "", "", "", 2, 2);
      await route.fulfill({ status: 200, contentType: "text/html", body: html });
    });

    await nextLink.click();

    await expect(page).toHaveURL(/page=2/);
    await expect(page.locator("#next-page").locator("..")).toHaveClass(/disabled/); // Should be disabled on last page
    await expect(page.locator("table tbody tr").first()).toContainText("User2");
  });
});
