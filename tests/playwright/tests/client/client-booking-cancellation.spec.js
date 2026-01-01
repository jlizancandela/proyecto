/**
 * @file Real E2E Booking Cancellation Test
 * @description Tests the complete booking cancellation flow against the real DDEV application and database.
 * This test creates its own test user and booking, performs the cancellation, and cleans up after itself.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

test.describe("Real E2E Booking Cancellation", () => {
  let connection;
  let testUserId;
  let testBookingId;
  const testUserEmail = `test-${Date.now()}@playwright.test`;
  const testUserPassword = "TestPassword123!";

  test.beforeAll(async () => {
    // Connect to database
    connection = await mysql.createConnection(dbConfig);

    // Create a test user with known password
    const passwordHash = await bcrypt.hash(testUserPassword, 10);
    const [userResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "Test", "Playwright User", testUserEmail, "600000000", passwordHash, 1]
    );

    testUserId = userResult.insertId;
    console.log(`Created test user with ID: ${testUserId}, email: ${testUserEmail}`);
  });

  test.afterAll(async () => {
    // Clean up: delete test booking and user
    if (testBookingId && connection) {
      await connection.execute("DELETE FROM RESERVA WHERE id_reserva = ?", [testBookingId]);
      console.log(`Deleted test booking ${testBookingId}`);
    }
    if (testUserId && connection) {
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario = ?", [testUserId]);
      console.log(`Deleted test user ${testUserId}`);
    }
    if (connection) {
      await connection.end();
    }
  });

  test("should cancel a booking and update database status", async ({ page }) => {
    // Step 1: Create a test booking in the database
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    // Use local time for both DB and UI to avoid UTC mismatches (e.g., just after midnight)
    const year = tomorrow.getFullYear();
    const monthRaw = tomorrow.getMonth() + 1;
    const dayRaw = tomorrow.getDate();

    const day = String(dayRaw).padStart(2, "0");
    const month = String(monthRaw).padStart(2, "0");

    const tomorrowDbStr = `${year}-${month}-${day}`;
    const tomorrowUiStr = `${day}/${month}/${year}`;

    const [result] = await connection.execute(
      `INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado, observaciones)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        testUserId,
        1, // Ana Fernández
        1, // Corte de Cabello Mujer
        tomorrowDbStr,
        "10:00:00",
        "10:45:00",
        "Confirmada",
        "E2E test booking for cancellation",
      ]
    );

    testBookingId = result.insertId;
    console.log(
      `Created test booking with ID: ${testBookingId} for date ${tomorrowDbStr} (UI: ${tomorrowUiStr})`
    );

    // Step 2: Login with test user
    console.log(`Attempting login with email: ${testUserEmail}`);
    await page.goto("https://proyecto.ddev.site/login");

    await page.fill('input[name="email"]', testUserEmail);
    await page.fill('input[name="password"]', testUserPassword);

    await Promise.all([
      page.waitForNavigation({ waitUntil: "load" }),
      page.click('button[type="submit"]'),
    ]);

    console.log(`Current URL after login attempt: ${page.url()}`);

    // Step 3: Navigate to bookings page
    console.log("Navigating to bookings page...");
    await page.goto("https://proyecto.ddev.site/user/reservas");
    await page.waitForLoadState("networkidle");

    // Step 4: Find the test booking card
    console.log(
      `Searching for booking card with text "Corte de Cabello Mujer" and date "${tomorrowUiStr}"`
    );
    const bookingCard = page
      .locator(".card")
      .filter({
        hasText: "Corte de Cabello Mujer",
      })
      .filter({
        hasText: tomorrowUiStr,
      })
      .first();

    await expect(bookingCard).toBeVisible({ timeout: 15000 });

    // Verify initial status
    await expect(bookingCard.locator(".badge")).toContainText("Confirmada");

    // Step 5: Click cancel button
    const cancelButton = bookingCard.locator(".btn-cancel");
    await expect(cancelButton).toBeVisible();
    await cancelButton.click();

    // Step 6: Confirm in modal
    await expect(page.locator("#actionModal")).toBeVisible();
    await expect(page.locator("#modalTitle")).toContainText("Cancelar Reserva");

    await Promise.all([
      page.waitForNavigation({ waitUntil: "load" }),
      page.click("#confirmActionBtn"),
    ]);

    console.log("Cancellation confirmed, page reloaded.");

    // Step 7: Verify the booking now shows as "Cancelada" in the UI
    const updatedCard = page
      .locator(".card")
      .filter({
        hasText: "Corte de Cabello Mujer",
      })
      .filter({
        hasText: tomorrowUiStr,
      })
      .first();

    const badge = updatedCard.locator(".badge").first();
    await expect(badge).toContainText("Cancelada", { timeout: 10000 });

    // Step 8: Verify database was updated
    const [rows] = await connection.execute("SELECT estado FROM RESERVA WHERE id_reserva = ?", [
      testBookingId,
    ]);

    expect(rows.length).toBe(1);
    expect(rows[0].estado).toBe("Cancelada");

    console.log(`✓ Booking ${testBookingId} successfully cancelled in database`);
    console.log(`✓ E2E test completed successfully!`);
  });
});
