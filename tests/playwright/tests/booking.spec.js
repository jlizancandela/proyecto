/**
 * @file Booking flow tests (Mocked).
 * @project app-reservas
 */

const { test, expect } = require("@playwright/test");

test.describe("Booking Flow", () => {
  test("should navigate through booking steps", async ({ page }) => {
    // 1. Register/Login to get a valid session (PHP requires this)
    await page.goto("/register");
    const timestamp = Date.now();
    const email = `testuser_${timestamp}@example.com`;
    const password = "TestUser123!";

    await page.fill("#nombre", "Test");
    await page.fill("#apellidos", "User");
    await page.fill("#email", email);
    await page.fill("#telefono", "600123456");
    await page.fill("#password", password);
    await page.fill("#password-confirm", password);

    await page.click('button[type="submit"]');
    await page.waitForURL("**/login");

    await page.fill("#email", email);
    await page.fill("#password", password);
    await page.click('button[type="submit"]');
    await page.waitForURL("**/");

    // 2. Setup API Mocks
    await page.route("**/api/me", async (route) => {
      await route.fulfill({
        json: {
          success: true,
          data: { id: 1, nombre: "Test", apellidos: "User", email: email },
        },
      });
    });

    await page.route("**/api/services", async (route) => {
      await route.fulfill({
        json: {
          success: true,
          servicios: [{ id: 1, nombre: "Corte de Pelo", duracion_minutos: 30, precio: 15 }],
        },
      });
    });

    // Mock Availability: Always available at 23:00 to avoid "past time" issues
    await page.route("**/api/especialistas/disponibles**", async (route) => {
      await route.fulfill({
        json: {
          data: [
            {
              id_especialista: 1,
              nombre: "Juan",
              apellidos: "e",
              horas_disponibles: ["23:00", "23:30"],
            },
          ],
          total: 1,
        },
      });
    });

    // Mock Bookings (POST = Success)
    await page.route("**/api/reservas", async (route) => {
      if (route.request().method() === "POST") {
        await route.fulfill({
          status: 200,
          json: { success: true },
        });
      } else {
        // GET (for Mis Reservas page)
        await route.fulfill({
          json: {
            reservas: [
              {
                id: 101,
                servicio_id: 1,
                // Use a date that ensures it's visible. Today is fine.
                fecha: new Date().toISOString().split("T")[0],
                hora: "23:00",
                duracion_minutos: 30,
                estado: "confirmada",
                servicio_nombre: "Corte de Pelo", // For generic list display if needed
                especialista_nombre: "Juan",
              },
            ],
          },
        });
      }
    });

    // 3. Execute Booking Flow
    await page.goto("/user/reservas/nueva");

    await page.waitForSelector("#bookings-app", { state: "visible" });
    const heading = page.locator("h1");
    // Depending on what H1 shows. The original test expected "Nueva Reserva".
    await expect(heading).toContainText("Nueva Reserva", { timeout: 10000 });

    // Step 1: Select Service
    await expect(page.locator("text=Paso 1/3")).toBeVisible();
    const firstService = page.locator(".card").first();
    await expect(firstService).toBeVisible();
    await firstService.click();

    // Step 2: Date and Specialist
    await expect(page.locator("text=Paso 2/3")).toBeVisible();

    // Select the first available time slot (23:00)
    // We target the button specifically to be robust
    const timeSlotButton = page.locator("button:has-text('23:00')").first();
    await expect(timeSlotButton).toBeVisible({ timeout: 10000 });
    await timeSlotButton.click();
    await page.waitForTimeout(500);

    // Next
    const nextButton = page.locator("button.btn-primary.rounded-circle:has(i.bi-chevron-right)");
    await nextButton.click();

    // Step 3: Confirmation
    await expect(page.locator("text=Paso 3/3")).toBeVisible({ timeout: 10000 });

    // Confirm
    const confirmButton = page.locator('button:has-text("Confirmar Reserva")');
    await expect(confirmButton).toBeVisible();
    await confirmButton.click();

    // 4. Verify Redirect
    await page.waitForURL("**/user/reservas", { timeout: 10000 });

    // 5. Verify "Mis Reservas" page content
    await expect(page.locator("h1")).toContainText("Mis Reservas");
    // Verify our mocked booking is there
    const bookingCard = page.locator(".card").first();
    await expect(bookingCard).toBeVisible();
  });
});
