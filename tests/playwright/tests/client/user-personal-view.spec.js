/**
 * @file User Personal View Test (Real E2E)
 * @description Verifies users can only see their own bookings and access available resources.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");

const { dbConfig } = require("../../helpers/db-config");

test.describe.configure({ mode: "serial" });

test.describe("User Personal View - Booking Isolation", () => {
  let connection;
  let userAId, userBId;
  let bookingAId, bookingBId;
  let serviceId, specialistId;

  const userAEmail = `usera-${Date.now()}@test.com`;
  const userBEmail = `userb-${Date.now()}@test.com`;
  const password = "TestPassword123!";

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);
    const hash = await bcrypt.hash(password, 10);

    // Create User A
    const [resultA] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "UserA", "Test", userAEmail, "600000100", hash, 1]
    );
    userAId = resultA.insertId;

    // Create User B
    const [resultB] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "UserB", "Test", userBEmail, "600000101", hash, 1]
    );
    userBId = resultB.insertId;

    // Get or create a service
    const [services] = await connection.execute(
      "SELECT id_servicio FROM SERVICIO WHERE activo = 1 LIMIT 1"
    );
    if (services.length > 0) {
      serviceId = services[0].id_servicio;
    } else {
      const [serviceResult] = await connection.execute(
        `INSERT INTO SERVICIO (nombre_servicio, descripcion, duracion_minutos, precio, activo)
         VALUES (?, ?, ?, ?, ?)`,
        ["Test Service", "Service for testing", 60, 50.0, 1]
      );
      serviceId = serviceResult.insertId;
    }

    // Get or create a specialist
    const [specialists] = await connection.execute(
      `SELECT e.id_especialista, u.id_usuario 
       FROM ESPECIALISTA e 
       JOIN USUARIO u ON e.id_usuario = u.id_usuario 
       WHERE u.activo = 1 LIMIT 1`
    );
    if (specialists.length > 0) {
      specialistId = specialists[0].id_especialista;
    } else {
      // Create specialist user
      const [specUserResult] = await connection.execute(
        `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
         VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
        ["Especialista", "TestSpec", "User", `spec-${Date.now()}@test.com`, "600000102", hash, 1]
      );
      const specUserId = specUserResult.insertId;

      const [specResult] = await connection.execute(
        `INSERT INTO ESPECIALISTA (id_usuario, foto_url, descripcion)
         VALUES (?, ?, ?)`,
        [specUserId, null, "Test specialist"]
      );
      specialistId = specResult.insertId;
    }

    // Create booking for User A (tomorrow at 10:00)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split("T")[0];

    const [bookingAResult] = await connection.execute(
      `INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado, observaciones)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        userAId,
        specialistId,
        serviceId,
        tomorrowStr,
        "10:00:00",
        "11:00:00",
        "Confirmada",
        "Booking for User A",
      ]
    );
    bookingAId = bookingAResult.insertId;

    // Create booking for User B (tomorrow at 14:00)
    const [bookingBResult] = await connection.execute(
      `INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado, observaciones)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        userBId,
        specialistId,
        serviceId,
        tomorrowStr,
        "14:00:00",
        "15:00:00",
        "Confirmada",
        "Booking for User B",
      ]
    );
    bookingBId = bookingBResult.insertId;
  });

  test.afterAll(async () => {
    if (connection) {
      // Delete bookings
      await connection.execute("DELETE FROM RESERVA WHERE id_reserva IN (?, ?)", [
        bookingAId,
        bookingBId,
      ]);

      // Delete users
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario IN (?, ?)", [
        userAId,
        userBId,
      ]);

      await connection.end();
    }
  });

  test("User A should see only their own bookings", async ({ page }) => {
    // Login as User A
    await page.goto("/login");
    await page.fill('input[name="email"]', userAEmail);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    // Navigate to bookings
    await page.goto("/user/reservas");

    // Verify page loaded
    await expect(page.locator("h1")).toContainText(/Mis Reservas/i);

    // Verify User A's booking is visible
    const bookingCards = page.locator(".card");
    await expect(bookingCards).toHaveCount(1);

    // Verify the booking contains User A's notes
    await expect(page.locator("text=Booking for User A")).toBeVisible();

    // Verify User B's booking is NOT visible
    await expect(page.locator("text=Booking for User B")).not.toBeVisible();
  });

  test("User B should see only their own bookings", async ({ page }) => {
    // Login as User B
    await page.goto("/login");
    await page.fill('input[name="email"]', userBEmail);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    // Navigate to bookings
    await page.goto("/user/reservas");

    // Verify page loaded
    await expect(page.locator("h1")).toContainText(/Mis Reservas/i);

    // Verify User B's booking is visible
    const bookingCards = page.locator(".card");
    await expect(bookingCards).toHaveCount(1);

    // Verify the booking contains User B's notes
    await expect(page.locator("text=Booking for User B")).toBeVisible();

    // Verify User A's booking is NOT visible
    await expect(page.locator("text=Booking for User A")).not.toBeVisible();
  });

  test("User can access available resources for new booking", async ({ page }) => {
    // Login as User A
    await page.goto("/login");
    await page.fill('input[name="email"]', userAEmail);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    // Navigate to new booking page
    await page.goto("/user/reservas/nueva");

    // Verify services are available
    await expect(page.locator("h1, h2")).toContainText(/Servicios|Nueva Reserva/i);

    // Verify page loaded successfully (not redirected or error)
    await expect(page).toHaveURL(/\/user\/reservas\/nueva/);
  });
});
