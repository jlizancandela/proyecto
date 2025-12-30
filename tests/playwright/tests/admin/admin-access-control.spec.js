/**
 * @file Admin Security Tests (Mocked)
 * @description Verifies that Admins cannot deactivate themselves or change their own role.
 */

const { test, expect } = require("@playwright/test");
const fs = require("fs");
const path = require("path");

// Helper to generate the users table HTML
const generateUsersPage = (users) => {
  const rows = users
    .map(
      (u) => `
    <tr>
        <td>${u.id}</td>
        <td>${u.nombre} ${u.apellidos}</td>
        <td>${u.email}</td>
        <td>${u.rol}</td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
        <td>
           ${
             u.rol !== "Admin"
               ? `<span class="badge bg-success btn-toggle-status" data-user-id="${u.id}" style="cursor: pointer">Activo</span>`
               : `<span class="badge bg-success" style="cursor: not-allowed" title="No se puede desactivar al Administrador">Activo</span>`
           }
        </td>
        <td>
            <button class="btn btn-edit-user" data-user-id="${u.id}">Edit</button>
        </td>
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
            <table>
                <tbody>${rows}</tbody>
            </table>
        </div>
        
        <!-- Mocked Edit Modal -->
        <div id="editUserModal" class="modal" style="display: none;">
            <form id="editUserForm">
                <input type="hidden" name="id" id="editUserId">
                <input type="text" name="nombre" id="editNombre">
                <input type="text" name="apellidos" id="editApellidos">
                <input type="email" name="email" id="editEmail">
                <input type="tel" name="telefono" id="editTelefono">
                
                <select name="rol" id="editRol">
                    <option value="Cliente">Cliente</option>
                    <option value="Especialista">Especialista</option>
                    <option value="Admin">Administrador</option>
                </select>
                
                <input type="checkbox" name="activo" id="editActivo">
                
                <!-- Containers for specialist (hidden) -->
                <div id="editServicesContainer"></div>
                
                <div id="editAvatarContainer">
                    <input type="file" id="editAvatar" name="avatar">
                </div>
                
                <div id="editDescriptionContainer">
                    <textarea id="editDescripcion" name="descripcion"></textarea>
                </div>
                
                <div id="editServiciosCheckboxes"></div> 
            </form>
        </div>
        
        <!-- Mocked Create User Modal (Required by usersManager.js) -->
        <div id="createUserModal" class="modal" style="display: none;">
            <form id="createUserForm">
                <input type="text" name="createNombre" id="createNombre">
                <input type="text" name="createApellidos" id="createApellidos">
                <input type="email" name="createEmail" id="createEmail">
                <input type="tel" name="createTelefono" id="createTelefono">
                <input type="password" name="createPassword" id="createPassword">
                <input type="password" name="createPasswordConfirm" id="createPasswordConfirm">
                
                <select name="createRol" id="createRol">
                    <option value="Cliente">Cliente</option>
                    <option value="Especialista">Especialista</option>
                    <option value="Admin">Administrador</option>
                </select>
                
                <div id="createServicesContainer"></div>
                <div id="createAvatarContainer">
                     <input type="file" id="createAvatar" name="createAvatar">
                </div>
                <div id="createDescriptionContainer">
                    <textarea id="createDescripcion" name="createDescripcion"></textarea>
                </div>
                
                <div id="createServiciosCheckboxes"></div>
            </form>
        </div>
        
        <!-- Script tag pointing to module -->
        <script type="module" src="/public/js/admin/usuarios/usersManager.js"></script>
    </body>
    </html>
  `;
};

test.describe("Admin Security Protection", () => {
  test("should disable status toggle and edit fields for Admin user", async ({ page }) => {
    // Inject Mock Bootstrap & Alert overrides BEFORE page load
    await page.addInitScript(() => {
      console.log("Adding init script...");
      window.bootstrap = {
        Modal: class {
          constructor(el) {
            this.el = el;
          }
          show() {
            console.log("Bootstrap Modal Show called!");
            this.el.style.display = "block";
            // Simulate Bootstrap classes
            this.el.classList.add("show");
            this.el.setAttribute("aria-modal", "true");
            this.el.setAttribute("role", "dialog");
            document.body.classList.add("modal-open");
          }
          hide() {
            this.el.style.display = "none";
          }
          static getInstance(el) {
            return new this(el);
          }
        },
      };
      // Capture alerts
      window.alert = (msg) => console.log("ALERT INTERCEPTED:", msg);
    });

    // 1. Mock Login
    await page.route("**/login", async (route) => {
      if (route.request().method() === "POST") {
        await route.fulfill({ status: 302, headers: { Location: "/admin/users" } });
      }
    });

    // 2. Mock Admin Page Response
    await page.route("**/admin/users", async (route) => {
      const adminUser = {
        id: 1,
        nombre: "Super",
        apellidos: "Admin",
        email: "admin@test.com",
        rol: "Admin",
        activo: true,
      };
      const html = generateUsersPage([adminUser]);
      await route.fulfill({ status: 200, contentType: "text/html", body: html });
    });

    // 3. Mock JS Files (Serve local content)
    const projectRoot = path.resolve(__dirname, "../../..");

    await page.route("**/public/js/admin/usuarios/usersManager.js", async (route) => {
      try {
        console.log("Serving usersManager.js from", projectRoot);
        const content = fs.readFileSync(
          path.join(projectRoot, "public/js/admin/usuarios/usersManager.js"),
          "utf8"
        );
        await route.fulfill({ status: 200, contentType: "application/javascript", body: content });
      } catch (e) {
        console.error("Error reading usersManager.js", e);
        await route.abort();
      }
    });

    await page.route("**/public/js/admin/usuarios/api.js", async (route) => {
      try {
        console.log("Serving api.js from", projectRoot);
        const content = fs.readFileSync(
          path.join(projectRoot, "public/js/admin/usuarios/api.js"),
          "utf8"
        );
        await route.fulfill({ status: 200, contentType: "application/javascript", body: content });
      } catch (e) {
        console.error("Error reading api.js", e);
        await route.abort();
      }
    });

    // 4. Mock API for Fetching User Data
    await page.route("**/admin/api/users/1", async (route) => {
      console.log("API Fetch called for user 1");
      await route.fulfill({
        status: 200,
        json: {
          success: true,
          data: {
            id: 1,
            nombre: "Super",
            apellidos: "Admin",
            email: "admin@test.com",
            rol: "Admin",
            activo: true,
            telefono: "123",
          },
        },
      });
    });

    await page.goto("/admin/users");

    // Monitor console logs
    page.on("console", (msg) => console.log("PAGE LOG:", msg.text()));

    // === VERIFY STATUS TOGGLE RESTRICTION ===
    const statusBadge = page.locator("table tbody tr td").nth(7).locator("span");
    await expect(statusBadge).toHaveCSS("cursor", "not-allowed");
    await expect(statusBadge).toHaveAttribute("title", "No se puede desactivar al Administrador");

    // === VERIFY EDIT MODAL RESTRICTION ===
    await page.click(".btn-edit-user");

    // Wait for modal to be visible
    await expect(page.locator("#editUserModal")).toBeVisible({ timeout: 5000 });

    // Check if fields are populated
    await expect(page.locator("#editNombre")).toHaveValue("Super");

    // CRITICAL ASSERTION: Role and Active fields must be disabled
    await expect(page.locator("#editRol")).toBeDisabled();
    await expect(page.locator("#editActivo")).toBeDisabled();
  });
});
