/**
 * @file Admin Third-Party Booking Management E2E Test.
 * @description Verifies admin can create and edit bookings for other users without time restrictions.
 */

const { test, expect } = require("@playwright/test");
const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");
const { dbConfig } = require("../../helpers/db-config");

test.describe.configure({ mode: "serial" });

test.describe("Admin Third-Party Booking Management", () => {
  let connection;
  let adminUserId;
  let clientUserId;
  let specialistUserId;
  let specialistId;
  let serviceId;
  let bookingId;

  const adminEmail = `admin-bookings-${Date.now()}@test.com`;
  const adminPassword = "AdminPass123!";
  const clientEmail = `client-booking-${Date.now()}@test.com`;

  test.beforeAll(async () => {
    connection = await mysql.createConnection(dbConfig);

    // Create admin user
    const adminHash = await bcrypt.hash(adminPassword, 10);
    const [adminResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Admin", "Booking", "Admin", adminEmail, "600000666", adminHash, 1]
    );
    adminUserId = adminResult.insertId;

    // Create client user
    const clientHash = await bcrypt.hash("ClientPass123!", 10);
    const [clientResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      ["Cliente", "Test", "Client", clientEmail, "600000555", clientHash, 1]
    );
    clientUserId = clientResult.insertId;

    // Create specialist user
    const specHash = await bcrypt.hash("SpecPass123!", 10);
    const [specUserResult] = await connection.execute(
      `INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo)
       VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)`,
      [
        "Especialista",
        "Test",
        "Specialist",
        `spec-${Date.now()}@test.com`,
        "600000444",
        specHash,
        1,
      ]
    );
    specialistUserId = specUserResult.insertId;

    // Create ESPECIALISTA record
    const [specResult] = await connection.execute(
      `INSERT INTO ESPECIALISTA (id_usuario, descripcion) VALUES (?, ?)`,
      [specialistUserId, "Test specialist"]
    );
    specialistId = specResult.insertId;

    // Get or create a service
    const [services] = await connection.execute(
      "SELECT id_servicio FROM SERVICIO WHERE activo = 1 LIMIT 1"
    );

    if (services.length > 0) {
      serviceId = services[0].id_servicio;
    } else {
      // Create a test service if none exists
      const [serviceResult] = await connection.execute(
        `INSERT INTO SERVICIO (nombre_servicio, descripcion, duracion_minutos, precio, activo)
         VALUES (?, ?, ?, ?, ?)`,
        ["Test Service", "Test description", 60, 50.0, 1]
      );
      serviceId = serviceResult.insertId;
    }

    // Assign service to specialist
    await connection.execute(
      "INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES (?, ?)",
      [specialistId, serviceId]
    );
  });

  test.afterAll(async () => {
    if (connection) {
      // Clean up booking
      if (bookingId) {
        await connection.execute("DELETE FROM RESERVA WHERE id_reserva = ?", [bookingId]);
      }
      // Clean up specialist service
      await connection.execute("DELETE FROM ESPECIALISTA_SERVICIO WHERE id_especialista = ?", [
        specialistId,
      ]);
      // Clean up specialist
      await connection.execute("DELETE FROM ESPECIALISTA WHERE id_especialista = ?", [
        specialistId,
      ]);
      // Clean up users
      await connection.execute("DELETE FROM USUARIO WHERE id_usuario IN (?, ?, ?)", [
        adminUserId,
        clientUserId,
        specialistUserId,
      ]);
      await connection.end();
    }
  });

  test("should create booking for client without time restrictions", async ({ page }) => {
    // 1. Login as admin
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    // 2. Navigate to bookings management
    await page.goto("/admin/bookings");
    await expect(page.locator("h1")).toContainText(/Gestión de Reservas/i);

    // 3. Click "New Booking" button (floating button)
    const newBookingBtn = page.locator('button[data-bs-target="#createBookingModal"]');
    await expect(newBookingBtn).toBeVisible();
    await newBookingBtn.click();

    // Wait for modal to open
    await expect(page.locator("#createBookingModal")).toBeVisible();

    // 4. Fill booking creation form
    // Select client
    await page.selectOption("#createCliente", clientUserId.toString());

    // Select specialist
    await page.selectOption("#createEspecialista", specialistId.toString());

    // Select service
    await page.selectOption("#createServicio", serviceId.toString());

    // Set date (use yesterday to verify admin can bypass time restrictions)
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    const yesterdayStr = yesterday.toISOString().split("T")[0];
    await page.fill("#createFecha", yesterdayStr);

    // Set time
    await page.fill("#createHora", "14:00");

    // Set status
    await page.selectOption("#createEstado", "Confirmada");

    // Add observations
    await page.fill("#createObservaciones", "Test booking created by admin");

    // 5. Submit form
    const submitBtn = page.locator('#createBookingForm button[type="submit"]');
    await submitBtn.click();

    // Wait for page to reload after successful creation
    await page.waitForLoadState("networkidle", { timeout: 15000 });
    await page.waitForTimeout(1000);

    // 6. Verify booking in database
    const [bookings] = await connection.execute(
      `SELECT id_reserva, id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, estado, observaciones
       FROM RESERVA 
       WHERE id_cliente = ? AND id_especialista = ? AND fecha_reserva = ?
       ORDER BY id_reserva DESC LIMIT 1`,
      [clientUserId, specialistId, yesterdayStr]
    );

    expect(bookings.length).toBe(1);
    expect(bookings[0].id_servicio).toBe(serviceId);
    expect(bookings[0].hora_inicio).toBe("14:00:00");
    expect(bookings[0].estado).toBe("Pendiente"); // Default status when created by admin

    expect(bookings[0].observaciones).toContain("Test booking");

    bookingId = bookings[0].id_reserva;
    console.log(
      `Created booking with ID: ${bookingId} for date: ${yesterdayStr} (past date - admin bypass)`
    );
  });

  test("should update existing booking to past date via UI", async ({ page }) => {
    // 1. Login as admin
    await page.goto("/login");
    await page.fill('input[name="email"]', adminEmail);
    await page.fill('input[name="password"]', adminPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL("/");

    // 2. Navigate to bookings management
    await page.goto("/admin/bookings");
    await expect(page.locator("h1")).toContainText(/Gestión de Reservas/i);

    // 3. Use filters to find our booking (in case it's on another page)
    await page.click('button[data-bs-target="#collapseFilters"]');
    await page.waitForTimeout(500); // Wait for accordion animation

    // Filter by client
    await page.selectOption("#cliente", clientUserId.toString());

    // Set date range to include yesterday (the creation date)
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    const yesterdayStr = yesterday.toISOString().split("T")[0];

    await page.fill("#fecha_desde", yesterdayStr);
    await page.fill("#fecha_hasta", yesterdayStr);

    // Apply filters
    await page.click('button[type="submit"]:has-text("Aplicar Filtros")');
    await page.waitForLoadState("networkidle");

    // 4. Find and click edit button for our booking
    const editBtn = page.locator(`.btn-edit-booking[data-booking-id="${bookingId}"]`);
    await expect(editBtn).toBeVisible({ timeout: 10000 });
    await editBtn.click();

    // Wait for edit modal to open and load data
    await expect(page.locator("#editBookingModal")).toBeVisible();
    await expect(page.locator("#editBookingLoading")).toBeHidden({ timeout: 10000 });

    // 5. Change date to a past date (5 days ago)
    const fiveDaysAgo = new Date();
    fiveDaysAgo.setDate(fiveDaysAgo.getDate() - 5);
    const fiveDaysAgoStr = fiveDaysAgo.toISOString().split("T")[0];

    await page.fill("#editFecha", fiveDaysAgoStr);
    await page.selectOption("#editEstado", "Confirmada");
    await page.fill("#editObservaciones", "Updated to past date by admin bypass");

    // 6. Submit form
    const submitBtn = page.locator('#editBookingForm button[type="submit"]');
    await submitBtn.click();

    // Wait for modal to close and page to reload
    await expect(page.locator("#editBookingModal")).toBeHidden({ timeout: 10000 });
    await page.waitForLoadState("networkidle");

    // 7. Verify changes in database
    const [updatedBookings] = await connection.execute(
      "SELECT fecha_reserva, estado, observaciones FROM RESERVA WHERE id_reserva = ?",
      [bookingId]
    );

    expect(updatedBookings.length).toBe(1);

    // Format DB date to YYYY-MM-DD manually to avoid timezone shifts
    const dbDate = updatedBookings[0].fecha_reserva;
    const dbDateStr = `${dbDate.getFullYear()}-${String(dbDate.getMonth() + 1).padStart(
      2,
      "0"
    )}-${String(dbDate.getDate()).padStart(2, "0")}`;

    expect(dbDateStr).toBe(fiveDaysAgoStr);
    expect(updatedBookings[0].estado).toBe("Confirmada");
    expect(updatedBookings[0].observaciones).toContain("admin bypass");

    console.log(`Booking ${bookingId} updated successfully to past date: ${dbDateStr}`);
  });
});
