/**
 * @file Booking Restrictions Real E2E Test.
 * @description Verifies business logic enforcement (weekly limits, overlaps) against real DB.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

test.describe("Booking Restrictions", () => {
  let connection;

  test.beforeEach(async () => {
    connection = await mysql.createConnection(dbConfig);
  });

  test.afterEach(async () => {
    if (connection) {
      await connection.end();
    }
  });

  async function createTestUser(email) {
    const password = "TestPassword123!";
    const passwordHash = await bcrypt.hash(password, 10);
    const [userResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "Restriction", "Test User", email, "600000001", passwordHash, 1]
    );
    return { id: userResult.insertId, email, password };
  }

  async function cleanTestUser(userId) {
    try {
      await connection.execute("DELETE FROM RESERVA WHERE id_cliente = ?", [userId]);
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [userId]);
    } catch (e) {
      console.warn(`Cleanup failed for user ${userId}: ${e.message}`);
    }
  }

  test("should prevent booking the same service twice in the same week", async ({ page }) => {
    const user = await createTestUser(`test-same-week-${Date.now()}@playwright.test`);

    try {
      const today = new Date();
      const dateStr = today.toISOString().split("T")[0];

      // Pre-insert a booking for today (Service ID 2 = Corte de Cabello Hombre)
      await connection.execute(
        `INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [user.id, 1, 2, dateStr, "09:00:00", "10:00:00", "Confirmada"]
      );

      // Login
      await page.goto("/login");
      await page.fill('input[name="email"]', user.email);
      await page.fill('input[name="password"]', user.password);
      await page.click('button[type="submit"]');
      await page.waitForURL("/");

      await page.goto("/user/reservas/nueva");
      await page.waitForSelector("#bookings-app");

      // Select "Corte de Cabello Hombre" (ID 2) - Alphabetically index 0
      await page.locator(".card").nth(0).click();

      // Select tomorrow's date to ensure available slots
      await page.waitForTimeout(1000);
      const tomorrowDate = page
        .locator("button.btn.rounded-circle")
        .filter({ hasNotText: /Paso|Siguiente|Anterior/ })
        .nth(1);
      await expect(tomorrowDate).toBeVisible({ timeout: 10000 });
      await tomorrowDate.click();
      await page.waitForTimeout(1500);

      const timeButton = page.locator("button.btn-outline-primary:not([disabled])").first();
      await expect(timeButton).toBeVisible({ timeout: 15000 });
      await timeButton.click();

      // Go to summary
      await page.locator("button.btn-primary.rounded-circle:has(i.bi-chevron-right)").click();

      // Confirm step
      const confirmBtn = page.locator('button:has-text("Confirmar Reserva")');
      await confirmBtn.waitFor({ state: "visible" });
      await confirmBtn.click();

      // Expect error
      const alert = page.locator(".alert-danger");
      await expect(alert).toBeVisible({ timeout: 15000 });
      await expect(alert).toContainText(/Ya tienes una reserva de este servicio en esta semana/i);
    } finally {
      await cleanTestUser(user.id);
    }
  });

  test("should prevent overlapping bookings", async ({ page }) => {
    const user = await createTestUser(`test-overlap-${Date.now()}@playwright.test`);

    try {
      // Use tomorrow's date (same as what the UI will select)
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const tomorrowStr = tomorrow.toISOString().split("T")[0];

      // Pre-insert a booking 15:00 - 16:00 for TOMORROW
      await connection.execute(
        `INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [user.id, 1, 2, tomorrowStr, "15:00:00", "16:00:00", "Confirmada"]
      );

      await page.goto("/login");
      await page.fill('input[name="email"]', user.email);
      await page.fill('input[name="password"]', user.password);
      await page.click('button[type="submit"]');
      await page.waitForURL("/");

      await page.goto("/user/reservas/nueva");
      await page.waitForSelector("#bookings-app");

      // Select different service (Mujer - index 1)
      await page.locator(".card").nth(1).click();

      // Select tomorrow's date to ensure available slots
      await page.waitForTimeout(1000);
      const tomorrowDate = page
        .locator("button.btn.rounded-circle")
        .filter({ hasNotText: /Paso|Siguiente|Anterior/ })
        .nth(1);
      await expect(tomorrowDate).toBeVisible({ timeout: 10000 });
      await tomorrowDate.click();
      await page.waitForTimeout(1500);

      const timeButton = page.locator('button:has-text("15:30")').first();
      await expect(timeButton).toBeVisible();
      await timeButton.click();

      await page.locator("button.btn-primary.rounded-circle:has(i.bi-chevron-right)").click();
      await page.locator('button:has-text("Confirmar Reserva")').click();

      await expect(page.locator(".alert-danger")).toContainText(/Ya tienes otra reserva|horario/i);
    } finally {
      await cleanTestUser(user.id);
    }
  });

  test("should enforce maximum weekly hours limit (40h)", async ({ page }) => {
    const user = await createTestUser(`test-40h-${Date.now()}@playwright.test`);

    try {
      const today = new Date();
      const startOfWeek = new Date(today);
      startOfWeek.setDate(today.getDate() - today.getDay() + 1);

      // Create 40 hours of existing bookings
      for (let i = 0; i < 40; i++) {
        const d = new Date(startOfWeek);
        d.setDate(startOfWeek.getDate() + Math.floor(i / 8));
        const dStr = d.toISOString().split("T")[0];
        const h = 8 + (i % 8);
        const hStr = `${String(h).padStart(2, "0")}:00:00`;
        const endHStr = `${String(h + 1).padStart(2, "0")}:00:00`;

        await connection.execute(
          `INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado)
               VALUES (?, ?, ?, ?, ?, ?, ?)`,
          [user.id, 1, 2, dStr, hStr, endHStr, "Confirmada"]
        );
      }

      await page.goto("/login");
      await page.fill('input[name="email"]', user.email);
      await page.fill('input[name="password"]', user.password);
      await page.click('button[type="submit"]');
      await page.waitForURL("/");

      await page.goto("/user/reservas/nueva");
      await page.waitForSelector("#bookings-app");

      // Select any service
      await page.locator(".card").nth(1).click();

      // Select tomorrow's date to ensure available slots
      await page.waitForTimeout(1000);
      const tomorrowDate = page
        .locator("button.btn.rounded-circle")
        .filter({ hasNotText: /Paso|Siguiente|Anterior/ })
        .nth(1);
      await expect(tomorrowDate).toBeVisible({ timeout: 10000 });
      await tomorrowDate.click();
      await page.waitForTimeout(1500);

      const timeButton = page.locator("button.btn-outline-primary:not([disabled])").first();
      await expect(timeButton).toBeVisible({ timeout: 15000 });
      await timeButton.click();

      await page.locator("button.btn-primary.rounded-circle:has(i.bi-chevron-right)").click();
      await page.locator('button:has-text("Confirmar Reserva")').click();

      await expect(page.locator(".alert-danger")).toContainText(/40 horas/i);
    } finally {
      await cleanTestUser(user.id);
    }
  });
});
