/**
 * @file Booking Restrictions tests (Mocked with Login).
 * @project app-reservas
 */

const { test, expect } = require("@playwright/test");

test.describe("Booking Restrictions", () => {
  test.beforeEach(async ({ page }) => {
    // 1. Register/Login to get a valid session
    await page.goto("/register");
    const timestamp = Date.now();
    const userEmail = `mock_test_${timestamp}@example.com`;
    const password = "TestUser123!";

    await page.fill("#nombre", "Mock");
    await page.fill("#apellidos", "User");
    await page.fill("#email", userEmail);
    await page.fill("#telefono", "600000000");
    await page.fill("#password", password);
    await page.fill("#password-confirm", password);

    await page.click('button[type="submit"]');
    await page.waitForURL("**/login");

    await page.fill("#email", userEmail);
    await page.fill("#password", password);
    await page.click('button[type="submit"]');
    await page.waitForURL("**/");

    // 2. Setup API Mocks

    // Mock Services
    await page.route("**/api/services", async (route) => {
      await route.fulfill({
        json: {
          success: true,
          servicios: [
            { id: 1, nombre: "Corte de Pelo", duracion_minutos: 30, precio: 15 },
            { id: 2, nombre: "Tinte", duracion_minutos: 60, precio: 40 },
          ],
        },
      });
    });

    // Mock Availability (Future time for Today)
    await page.route("**/api/especialistas/disponibles**", async (route) => {
      await route.fulfill({
        json: {
          data: [
            {
              id_especialista: 1,
              nombre: "Juan",
              apellidos: "Perez",
              horas_disponibles: ["22:00", "23:00", "23:30"],
            },
          ],
          total: 1,
        },
      });
    });

    // Mock Bookings (GET) - Initially empty
    await page.route("**/api/reservas", async (route) => {
      if (route.request().method() === "GET") {
        await route.fulfill({
          json: { reservas: [] },
        });
      } else {
        await route.continue();
      }
    });

    await page.route("**/api/me", async (route) => {
      await route.fulfill({
        json: {
          success: true,
          data: { id: 1, nombre: "Mock", apellidos: "User", email: userEmail },
        },
      });
    });
  });

  test("should prevent booking the same service twice in the same week", async ({ page }) => {
    const today = new Date();
    const dateStr = today.toISOString().split("T")[0];

    // Mock existing booking same week
    await page.route("**/api/reservas", async (route) => {
      if (route.request().method() === "GET") {
        await route.fulfill({
          json: {
            reservas: [
              {
                id: 100,
                id_servicio: 1, // Corrected key
                fecha_reserva: dateStr, // Corrected key
                hora: "23:00",
                estado: "confirmada",
              },
            ],
          },
        });
      } else if (route.request().method() === "POST") {
        await route.fulfill({ status: 200, json: { success: true } });
      }
    });

    await page.goto("/user/reservas/nueva");
    await page.waitForSelector("#bookings-app");

    // Select Service 1
    await page.locator(".card").first().click();
    await expect(page.locator("text=Paso 2/3")).toBeVisible();

    // Select 23:00
    const timeButton = page.locator('button:has-text("23:00")').first();
    await expect(timeButton).toBeVisible();
    await timeButton.click();
    await page.waitForTimeout(500);
    await page.locator("button.btn-primary.rounded-circle:has(i.bi-chevron-right)").click();

    await page.locator('button:has-text("Confirmar Reserva")').click();

    await expect(page.locator(".alert-danger")).toContainText(
      /Ya tienes una reserva de este servicio en esta semana/i
    );
  });

  test("should prevent overlapping bookings (Charge Control)", async ({ page }) => {
    const today = new Date();
    const dateStr = today.toISOString().split("T")[0];

    await page.route("**/api/reservas", async (route) => {
      if (route.request().method() === "GET") {
        await route.fulfill({
          json: {
            reservas: [
              {
                id: 101,
                id_servicio: 2, // Corrected key
                fecha_reserva: dateStr, // Corrected key
                hora: "23:00",
                duracion_minutos: 60,
                estado: "confirmada",
              },
            ],
          },
        });
      } else if (route.request().method() === "POST") {
        await route.fulfill({
          status: 400,
          json: { error: "Ya tienes otra reserva en ese horario" },
        });
      }
    });

    await page.goto("/user/reservas/nueva");
    await page.waitForSelector("#bookings-app");

    await page.locator(".card").first().click();

    // Select same time 23:00
    const timeButton = page.locator('button:has-text("23:00")').first();
    await expect(timeButton).toBeVisible();
    await timeButton.click();

    await page.waitForTimeout(500);
    await page.locator("button.btn-primary.rounded-circle:has(i.bi-chevron-right)").click();

    await page.locator('button:has-text("Confirmar Reserva")').click();

    await expect(page.locator(".alert-danger")).toContainText(/Ya tienes otra reserva|horario/i);
  });

  test("should enforce maximum weekly hours limit (40h)", async ({ page }) => {
    await page.route("**/api/reservas", async (route) => {
      if (route.request().method() === "GET") {
        const bookings = [];
        for (let i = 0; i < 40; i++) {
          bookings.push({
            id: 200 + i,
            id_servicio: 2, // Corrected key
            fecha_reserva: new Date().toISOString().split("T")[0], // Corrected key
            hora: "08:00",
            duracion_minutos: 60,
            estado: "confirmada",
          });
        }

        await route.fulfill({
          json: { reservas: bookings },
        });
      } else {
        await route.continue();
      }
    });

    await page.goto("/user/reservas/nueva");
    await page.waitForSelector("#bookings-app");
    await page.locator(".card").first().click();

    const timeButton = page.locator('button:has-text("23:00")').first();
    await timeButton.click();
    await page.waitForTimeout(500);
    await page.locator("button.btn-primary.rounded-circle:has(i.bi-chevron-right)").click();

    await page.locator('button:has-text("Confirmar Reserva")').click();

    await expect(page.locator(".alert-danger")).toContainText(/40 horas/i);
  });
});
