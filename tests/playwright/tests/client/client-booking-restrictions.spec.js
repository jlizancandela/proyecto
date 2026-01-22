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

  // Helpers to create temporary resources for each test to ensure availability
  async function createTestResources() {
    const serviceName = `Restrict Service ${Date.now()}`;
    const email = `spec-restrict-${Date.now()}@test.com`;
    const passwordHash = await bcrypt.hash("Pass123!", 10);
    
    // 1. Service
    const [svc] = await connection.execute(
      `INSERT INTO SERVICIO (nombre_servicio, duracion_minutos, precio, descripcion) VALUES (?, 60, 20.00, 'Test')`,
      [serviceName]
    );
    const serviceId = svc.insertId;

    // 2. Specialist User
    const [usr] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES ('Especialista', 'Restrict', 'Spec', ?, '600000777', ?, CURDATE(), 1)`,
      [email, passwordHash]
    );
    const specUserId = usr.insertId;

    // 3. Specialist Profile
    const [spec] = await connection.execute(
      `INSERT INTO ESPECIALISTA (id_usuario, descripcion) VALUES (?, 'Test')`,
      [specUserId]
    );
    const specialistId = spec.insertId;

    // 4. Link
    await connection.execute(`INSERT INTO ESPECIALISTA_SERVICIO VALUES (?, ?)`, [specialistId, serviceId]);

    // 5. Schedule (Super Robust 0-8, full day)
    for (let d = 0; d <= 8; d++) {
      await connection.execute(
        `INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES (?, ?, '00:00:00', '23:59:00')`,
        [specialistId, d]
      );
    }

    return { serviceId, serviceName, specUserId, specialistId };
  }

  async function cleanTestResources(res) {
    if (!res) return;
    if (res.specUserId) await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [res.specUserId]);
    if (res.serviceId) await connection.execute("DELETE FROM SERVICIO WHERE id_servicio = ?", [res.serviceId]);
  }

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
    const resources = await createTestResources();

    try {
      const today = new Date();
      const year = today.getFullYear();
      const monthRaw = today.getMonth() + 1;
      const dayRaw = today.getDate();
      const day = String(dayRaw).padStart(2, "0");
      const month = String(monthRaw).padStart(2, "0");

      const dateStr = `${year}-${month}-${day}`;

      // Pre-insert a booking for today using our test resources
      await connection.execute(
        `INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [user.id, resources.specialistId, resources.serviceId, dateStr, "09:00:00", "10:00:00", "Confirmada"]
      );

      // Login
      await page.goto("/login");
      await page.fill('input[name="email"]', user.email);
      await page.fill('input[name="password"]', user.password);
      await page.click('button[type="submit"]');
      await page.waitForURL("/");

      await page.goto("/user/reservas/nueva");
      await page.waitForSelector("#bookings-app");

      // Select our specific test service
      await page.locator(".card").filter({ hasText: resources.serviceName }).click();

      // Wait for calendar/specialist selection to be visible
      await expect(page.locator("text=Paso 2/3")).toBeVisible();
      await page.waitForTimeout(1000);

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

      const tomorrowDateBtn = page.locator("button.btn-outline-primary:not([disabled])").first();
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
      await cleanTestResources(resources);
      await cleanTestUser(user.id);
    }
  });

  test.skip("should prevent overlapping bookings", async ({ page }) => {
    // Skip on Sundays to avoid date logic mismatches when shop might be closed or UI behaves differently
    if (new Date().getDay() === 0) {
        test.skip(true, "Skipping on Sundays as 'tomorrow' falls in the next week/month or shop is closed");
        return;
    }

    const user = await createTestUser(`test-overlap-${Date.now()}@playwright.test`);
    const resources = await createTestResources();

    try {
      await page.goto("/login");
      await page.fill('input[name="email"]', user.email);
      await page.fill('input[name="password"]', user.password);
      await page.click('button[type="submit"]');
      await page.waitForURL("/");

      await page.goto("/user/reservas/nueva");
      await page.waitForSelector("#bookings-app");

      // Select OUR Service
      await page.locator(".card").filter({ hasText: resources.serviceName }).click();

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

      // Select ANY available date (Dynamic Sync)
      const dateBtn = page.locator("button.btn-outline-primary:not([disabled])").first();
      await expect(dateBtn).toBeVisible({ timeout: 10000 });
      
      const dayText = await dateBtn.textContent();
      const targetDay = parseInt(dayText.trim());
      
      await dateBtn.click();
      await page.waitForTimeout(1500);
      
      // Construct target date for SQL based on UI selection
      const targetDateStr = `${tYear}-${tMonth}-${String(targetDay).padStart(2, "0")}`;

      // Find ANY available time slot
      const timeButton = page.locator("button.btn-outline-primary:not([disabled])").first();
      await expect(timeButton).toBeVisible({ timeout: 10000 });

      const selectedTime = (await timeButton.textContent()).trim(); // e.g., "10:00"
      await timeButton.click();

      await page.getByRole("button", { name: "Siguiente paso" }).click();

      // Create a specific conflict user to test "Specialist Busy" scenario
      const conflictUser = await createTestUser(`conflict-${Date.now()}@test.com`);

      // Calculate proper end time (1 hour duration)
      const [hours, minutes] = selectedTime.split(":").map(Number);
      const endHour = hours + 1;
      const endTimeStr = `${String(endHour).padStart(2, "0")}:${String(minutes).padStart(2, "0")}:00`;
      const startTimeStr = `${selectedTime}:00`;

      // NOW, inject the conflicting booking for the SAME Time
      // Using a different user to ensure it blocks due to Specialist unavailability
      await connection.execute(
        `INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [
          conflictUser.id,
          resources.specialistId,
          resources.serviceId,
          targetDateStr,
          startTimeStr,
          endTimeStr,
          "Confirmada",
        ]
      );

      // Try to confirm
      await page.locator('button:has-text("Pagar en el local")').click();

      await expect(page.locator(".alert-danger")).toContainText(/Ya tienes otra reserva|horario/i, {
        timeout: 10000,
      });

      // Cleanup conflict user
      await cleanTestUser(conflictUser.id);
    } finally {
      await cleanTestResources(resources);
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
          [user.id, 2, 2, dStr, hStr, endHStr, "Confirmada"]
        );
      }

      await page.goto("/login");
      await page.fill('input[name="email"]', user.email);
      await page.fill('input[name="password"]', user.password);
      await page.click('button[type="submit"]');
      await page.waitForURL("/");

      await page.goto("/user/reservas/nueva");
      await page.waitForSelector("#bookings-app");

      // Select "Corte de Pelo" (ID 1)
      await page.locator(".card").filter({ hasText: "Corte de Pelo" }).click();

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
