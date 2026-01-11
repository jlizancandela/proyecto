/**
 * @file Booking Process Real E2E Test.
 * @description Verifies the full 3-step booking flow against a real database.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

  test.describe("Booking Flow E2E", () => {
  let connection;
  let testUserId;
  let serviceId;
  let specialistUserId;
  const testUserEmail = `test-booking-${Date.now()}@playwright.test`;
  const testUserPassword = "TestPassword123!";
  const serviceName = `Test Service ${Date.now()}`;

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);
    
    // 1. Create Test Client
    const passwordHash = await bcrypt.hash(testUserPassword, 10);
    const [userResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "Booking", "Test User", testUserEmail, "600000002", passwordHash, 1]
    );
    testUserId = userResult.insertId;

    // 2. Create Test Service
    const [serviceResult] = await connection.execute(
      `INSERT INTO SERVICIO (nombre_servicio, duracion_minutos, precio, descripcion)
       VALUES (?, ?, ?, ?)`,
      [serviceName, 30, 25.00, "Test Service for E2E Flow"]
    );
    serviceId = serviceResult.insertId;

    // 3. Create Test Specialist User
    const [specUserResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Especialista", "Flow", "Specialist", `spec-flow-${Date.now()}@test.com`, "600000999", passwordHash, 1]
    );
    specialistUserId = specUserResult.insertId;

    // 4. Create Specialist Profile
    const [specResult] = await connection.execute(
      `INSERT INTO ESPECIALISTA (id_usuario, descripcion) VALUES (?, ?)`,
      [specialistUserId, "Expert in E2E Testing"]
    );
    const specialistId = specResult.insertId;

    // 5. Assign Service to Specialist
    await connection.execute(
      `INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES (?, ?)`,
      [specialistId, serviceId]
    );

    // 6. Create Schedule (Super Robust: 0-8 covers all day numbering conventions, full day availability)
    for (let day = 0; day <= 8; day++) {
      await connection.execute(
        `INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin)
         VALUES (?, ?, ?, ?)`,
        [specialistId, day, "00:00:00", "23:59:00"]
      );
    }
  });

  test.afterAll(async () => {
    if (connection) {
      // Clean up Booking
      await connection.execute("DELETE FROM RESERVA WHERE id_cliente = ?", [testUserId]);
      // Clean up Users (Cascade should handle Specialist, Schedules, etc.)
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [testUserId]);
      if (specialistUserId) {
        await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [specialistUserId]);
      }
      // Clean up Service
      if (serviceId) {
        await connection.execute("DELETE FROM SERVICIO WHERE id_servicio = ?", [serviceId]);
      }
      await connection.end();
    }
  });

  test("should successfully complete a 3-step booking process", async ({ page }) => {
    // 1. Login
    await page.goto("/login");
    await page.fill('input[name="email"]', testUserEmail);
    await page.fill('input[name="password"]', testUserPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    // 2. Start new booking
    await page.goto("/user/reservas/nueva");
    await page.waitForSelector("#bookings-app");

    // STEP 1: Select Our Test Service
    // Filter by our specific service name to ensure we pick the one with guaranteed availability
    const serviceCard = page.locator(".card").filter({ hasText: serviceName }).first();
    await expect(serviceCard).toBeVisible();

    console.log(`Selecting service: ${serviceName}`);
    await serviceCard.click();

    // STEP 2: Date & specialist selection
    await expect(page.locator("text=Paso 2/3")).toBeVisible();

    // Wait for calendar to load
    await page.waitForTimeout(1000);

    // Select tomorrow's date
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowDay = tomorrow.getDate();

    // Check current month displayed
    const monthTitle = page.locator(".fw-bold.text-capitalize.fs-5");
    const currentMonthText = await monthTitle.textContent();
    console.log(`Current calendar month: ${currentMonthText}`);

    // If we are in December and tomorrow is January, navigate
    if (currentMonthText.toLowerCase().includes("diciembre") && tomorrowDay === 1) {
      console.log("Navigating to January...");
      await page.getByRole("button", { name: "Mes siguiente" }).click();

      // Wait for the month to actually change to "enero"
      await expect(monthTitle).toContainText("enero", { ignoreCase: true, timeout: 5000 });
      console.log("Navigation successful, now in January.");
    }

    // Target the specific day button in the calendar
    const tomorrowDateBtn = page.locator("button.btn-outline-primary:not([disabled])").first();
    await expect(tomorrowDateBtn).toBeVisible({ timeout: 10000 });
    await tomorrowDateBtn.click();
    
    // Tuning: Wait for time slots to regenerate properly
    await page.waitForTimeout(2000);

    // Select first available time slot
    const timeButton = page.locator("button.btn-outline-primary:not([disabled])").first();
    await expect(timeButton).toBeVisible({ timeout: 15000 });
    const selectedTime = (await timeButton.textContent()).trim();
    console.log(`Selecting time: ${selectedTime}`);
    await timeButton.click();

    // Wait for state to settle
    await page.waitForTimeout(1000);

    // Use aria-label for next button
    const nextBtn = page.getByRole("button", { name: "Siguiente paso" });
    await nextBtn.click();

    // STEP 3: Confirmation
    await expect(page.locator("text=Paso 3/3")).toBeVisible();

    // Locate the summary card
    const summaryCard = page.locator(".card").filter({ hasText: /Resumen/i });
    await expect(summaryCard).toBeVisible({ timeout: 15000 });

    // In Step 3, we expect the service name to appear eventually.
    // Sometimes Preact needs a moment to re-render with the full state.
    await expect(async () => {
      const text = await summaryCard.textContent();
      expect(text).toContain(serviceName);
    }).toPass({ timeout: 15000 });

    // Confirm booking
    const confirmBtn = page.locator('button:has-text("Pagar en el local")');
    await confirmBtn.waitFor({ state: "visible" });

    await Promise.all([
      page.waitForURL("**/user/reservas", { timeout: 30000 }),
      confirmBtn.click(),
    ]);

    // 4. Verify in UI (Mis Reservas)
    await expect(page.locator("h1")).toContainText(/Mis Reservas/i);
    const firstBooking = page.locator(".card").first();
    await expect(firstBooking).toBeVisible();
    await expect(firstBooking).toContainText(serviceName);

    // 5. Verify in Database
    const [rows] = await connection.execute(
      "SELECT * FROM RESERVA WHERE id_cliente = ? ORDER BY id_reserva DESC LIMIT 1",
      [testUserId]
    );
    expect(rows.length).toBe(1);
    expect(rows[0].estado.toLowerCase()).toBe("pendiente");
  });
});
