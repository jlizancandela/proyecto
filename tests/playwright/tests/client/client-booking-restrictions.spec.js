/**
 * @file Booking Restrictions Real E2E Test.
 * @description Verifies business logic enforcement (weekly limits, overlaps) against real DB.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

test.describe.serial("Booking Restrictions", () => {
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
    // Skip if today is Sunday, because "Tomorrow" would be next week, invalidating the "Same Week" check
    if (new Date().getDay() === 0) {
      test.skip(true, "Skipping on Sundays as 'tomorrow' falls in the next week");
      return;
    }

    const user = await createTestUser(`test-same-week-${Date.now()}@playwright.test`);

    try {
      const today = new Date();
      const year = today.getFullYear();
      const monthRaw = today.getMonth() + 1;
      const dayRaw = today.getDate();
      const day = String(dayRaw).padStart(2, "0");
      const month = String(monthRaw).padStart(2, "0");

      const dateStr = `${year}-${month}-${day}`;

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

      // Select tomorrow's date
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const tomorrowDay = tomorrow.getDate();

      const monthTitle = page.locator(".fw-bold.text-capitalize.fs-5");
      const currentMonthText = await monthTitle.textContent();

      if (currentMonthText.toLowerCase().includes("diciembre") && tomorrowDay === 1) {
        await page.getByRole("button", { name: "Mes siguiente" }).click();
        await expect(monthTitle).toContainText("enero", { ignoreCase: true, timeout: 5000 });
      }

      const tomorrowDateBtn = page.getByRole("button", { name: `Día ${tomorrowDay}`, exact: true });
      await expect(tomorrowDateBtn).toBeVisible({ timeout: 10000 });
      await tomorrowDateBtn.click();
      await page.waitForTimeout(1500);

      const timeButton = page.locator("button.btn-outline-primary:not([disabled])").first();
      await expect(timeButton).toBeVisible({ timeout: 15000 });
      await timeButton.click();

      // Go to summary
      await page.getByRole("button", { name: "Siguiente paso" }).click();

      // Confirm step
      const confirmBtn = page.locator('button:has-text("Pagar en el local")');
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
      await page.goto("/login");
      await page.fill('input[name="email"]', user.email);
      await page.fill('input[name="password"]', user.password);
      await page.click('button[type="submit"]');
      await page.waitForURL("/");

      await page.goto("/user/reservas/nueva");
      await page.waitForSelector("#bookings-app");

      // Select known service (Corte de Cabello Hombre) which works in other tests
      await page.locator(".card").filter({ hasText: "Corte de Cabello Hombre" }).click();

      // Select tomorrow's date
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const tomorrowDay = tomorrow.getDate();

      // Use local time for DB string
      const tYear = tomorrow.getFullYear();
      const tMonthRaw = tomorrow.getMonth() + 1;
      const tDayRaw = tomorrow.getDate();
      const tDay = String(tDayRaw).padStart(2, "0");
      const tMonth = String(tMonthRaw).padStart(2, "0");
      const tomorrowStr = `${tYear}-${tMonth}-${tDay}`;

      const monthTitle = page.locator(".fw-bold.text-capitalize.fs-5");
      const currentMonthText = await monthTitle.textContent();

      if (currentMonthText.toLowerCase().includes("diciembre") && tomorrowDay === 1) {
        await page.getByRole("button", { name: "Mes siguiente" }).click();
        await expect(monthTitle).toContainText("enero", { ignoreCase: true, timeout: 5000 });
      }

      const tomorrowDateBtn = page.getByRole("button", { name: `Día ${tomorrowDay}`, exact: true });
      await expect(tomorrowDateBtn).toBeVisible({ timeout: 10000 });
      await tomorrowDateBtn.click();
      await page.waitForTimeout(1500);

      // Find ANY available time slot
      const timeButton = page.locator("button.btn-outline-primary:not([disabled])").first();
      await expect(timeButton).toBeVisible({ timeout: 10000 });

      const selectedTime = (await timeButton.textContent()).trim(); // e.g., "10:00"
      await timeButton.click();

      await page.getByRole("button", { name: "Siguiente paso" }).click();

      // NOW, inject the conflicting booking for the SAME Time
      // We use a different service/specialist (ID 1, 1) to avoid "Specialist Busy" hidden slot issues
      // ensuring the conflict is purely "Client Busy".
      await connection.execute(
        `INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [
          user.id,
          1, // Specialist 1 (Assuming Spec 1 does Service 1 too or doesn't matter for client overlap check)
          1, // Service 1 (Corte Mujer) - DIFFERENT from Service 2 to avoid weekly limit
          tomorrowStr,
          `${selectedTime}:00`,
          `${selectedTime.split(":")[0]}:59:59`, // 1 hour approx
          "Confirmada",
        ]
      );

      // Try to confirm
      await page.locator('button:has-text("Pagar en el local")').click();

      await expect(page.locator(".alert-danger")).toContainText(/Ya tienes otra reserva|horario/i, {
        timeout: 10000,
      });
    } finally {
      await cleanTestUser(user.id);
    }
  });

  test("should enforce maximum weekly hours limit (40h)", async ({ page }) => {
    // Skip on weekends as we can't book "this week" effectively if days are passed/closed
    const today = new Date();
    if (today.getDay() === 6 || today.getDay() === 0) {
      test.skip(true, "Cannot test weekly limit on weekends (shop closed or week over)");
      return;
    }

    const user = await createTestUser(`test-40h-${Date.now()}@playwright.test`);

    try {
      const startOfWeek = new Date(today);
      const dayOfWeek = today.getDay() || 7; // 1 (Mon) - 7 (Sun)
      startOfWeek.setDate(today.getDate() - dayOfWeek + 1); // Monday

      // Create 40 hours of existing bookings packed into Mon-Thu (4 days * 10 hours)
      // This leaves Friday free for testing the failure
      for (let i = 0; i < 40; i++) {
        // 0-9 = Day 0 (Mon), 10-19 = Day 1 (Tue), etc.
        const dayOffset = Math.floor(i / 10);

        const d = new Date(startOfWeek);
        d.setDate(startOfWeek.getDate() + dayOffset);

        const dYear = d.getFullYear();
        const dMonthRaw = d.getMonth() + 1;
        const dDayRaw = d.getDate();
        const dStr = `${dYear}-${String(dMonthRaw).padStart(2, "0")}-${String(dDayRaw).padStart(
          2,
          "0"
        )}`;

        const h = 8 + (i % 10); // 08:00 to 17:00
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

      // Target Friday of the current week
      const friday = new Date(startOfWeek);
      friday.setDate(startOfWeek.getDate() + 4);
      const fridayDay = friday.getDate();

      const monthTitle = page.locator(".fw-bold.text-capitalize.fs-5");
      const currentMonthText = await monthTitle.textContent();

      // Navigate month if Friday is in next month (unlikely for "this week" unless week splits month)
      const fridayMonthName = friday.toLocaleString("es-ES", { month: "long" });
      if (!currentMonthText.toLowerCase().includes(fridayMonthName.toLowerCase())) {
        await page.getByRole("button", { name: "Mes siguiente" }).click();
      }

      const fridayDateBtn = page.getByRole("button", { name: `Día ${fridayDay}`, exact: true });

      await expect(fridayDateBtn).toBeVisible({ timeout: 10000 });
      await fridayDateBtn.click();
      await page.waitForTimeout(1500);

      const timeButton = page.locator("button.btn-outline-primary:not([disabled])").first();
      await expect(timeButton).toBeVisible({ timeout: 15000 });
      await timeButton.click();

      await page.getByRole("button", { name: "Siguiente paso" }).click();
      await page.locator('button:has-text("Pagar en el local")').click();

      await expect(page.locator(".alert-danger")).toContainText(/40 horas/i);
    } finally {
      await cleanTestUser(user.id);
    }
  });
});
