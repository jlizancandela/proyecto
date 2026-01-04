/**
 * @file Booking Process with Stripe Payment E2E Test.
 * @description Verifies the full booking flow including redirection to Stripe Checkout.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

test.describe("Booking Flow with Payment E2E", () => {
  let connection;
  let testUserId;
  const testUserEmail = `test-payment-${Date.now()}@playwright.test`;
  const testUserPassword = "TestPassword123!";

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);
    const passwordHash = await bcrypt.hash(testUserPassword, 10);
    const [userResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "Payment", "Test User", testUserEmail, "600000003", passwordHash, 1]
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

  test("should successfully complete booking and redirect to Stripe", async ({ page }) => {
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
    await serviceCard.click();

    // STEP 2: Date & specialist selection
    await page.waitForTimeout(1000);
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowDay = tomorrow.getDate();

    // Handle Month Navigation if needed
    const monthTitle = page.locator(".fw-bold.text-capitalize.fs-5");
    const currentMonthText = await monthTitle.textContent();
    if (currentMonthText.toLowerCase().includes("diciembre") && tomorrowDay === 1) {
      await page.getByRole("button", { name: "Mes siguiente" }).click();
    }

    // Select Day
    const tomorrowDateBtn = page.getByRole("button", { name: `Día ${tomorrowDay}`, exact: true });
    await tomorrowDateBtn.click();

    // Select Time
    const timeButton = page.locator("button.btn-outline-primary:not([disabled])").first();
    await timeButton.click();

    // Next Step
    await page.getByRole("button", { name: "Siguiente paso" }).click();

    // STEP 3: Confirmation & Payment
    await expect(page.locator("text=Paso 3/3")).toBeVisible();
    
    // Click "Pagar ahora"
    const payBtn = page.locator('button:has-text("Pagar ahora")');
    await payBtn.waitFor({ state: "visible" });

    // Click and wait for Stripe Redirection
    await payBtn.click();
    
    // We verify redirection to Stripe domain
    await page.waitForURL(/checkout.stripe.com/, { timeout: 20000 });
    
    console.log("Successfully redirected to Stripe Checkout");
    
    // Note: Completing the actual Stripe form is complex due to iframe security
    // and can be flaky. For a DAW project, verifying the redirection is 
    // usually sufficient to prove the integration works.
    expect(page.url()).toContain("stripe.com");
  });
});
