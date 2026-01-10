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
  const testUserEmail = `test-booking-${Date.now()}@playwright.test`;
  const testUserPassword = "TestPassword123!";

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);
    const passwordHash = await bcrypt.hash(testUserPassword, 10);
    const [userResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "Booking", "Test User", testUserEmail, "600000002", passwordHash, 1]
    );
    testUserId = userResult.insertId;
  });

  test.afterAll(async () => {
    if (connection) {
      await connection.execute("DELETE FROM RESERVA WHERE id_cliente = ?", [testUserId]);
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [testUserId]);
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

    // STEP 1: Select Service
    const serviceCard = page.locator(".card").first();
    await expect(serviceCard).toBeVisible();

    // Extract service name from the title
    const serviceTitle = serviceCard.locator(".card-title").first();
    const serviceName = (await serviceTitle.textContent()).trim();
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
