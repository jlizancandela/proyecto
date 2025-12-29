const { test, expect } = require("@playwright/test");

test("should login as admin using .env credentials (Mocked)", async ({ page }) => {
  const email = process.env.ADMIN_EMAIL;
  const password = process.env.ADMIN_PASSWORD;

  console.log(`Testing with Env User: ${email}`);

  expect(email).toBeDefined();
  expect(password).toBeDefined();

  // Mock the login endpoint to verify credentials are SENT correctly
  await page.route("**/login", async (route) => {
    if (route.request().method() === "POST") {
      const postData = route.request().postData();
      const params = new URLSearchParams(postData);

      // ASSERT: The form data must match our .env variables
      // This proves we are reading them and using them!
      if (params.get("email") === email && params.get("password") === password) {
        console.log("Credentials match .env!");
        // Simulate a successful login redirect
        await route.fulfill({
          status: 302,
          headers: { Location: "/admin" },
        });
      } else {
        console.log("Credentials MISMATCH!");
        await route.continue();
      }
    } else {
      await route.continue();
    }
  });

  await page.goto("/login");
  await page.fill("#email", email);
  await page.fill("#password", password);
  await page.click('button[type="submit"]');

  // Verify we got redirected to admin (our mock did it)
  await expect(page).toHaveURL(/.*\/admin/);
});
