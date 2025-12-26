/**
 * Home page smoke tests.
 * This file contains basic tests to ensure the landing page is accessible.
 */

const { test, expect } = require("@playwright/test");

test.describe("Home Page", () => {
  /**
   * Check if the home page loads correctly.
   */
  test("should load the home page successfully", async ({ page }) => {
    await page.goto("/");

    // Check if the title is correct (adjust based on your actual title)
    // For now we just check if the page has SOME content
    await expect(page).toHaveTitle(/./);

    // Check for a common element like service section or hero
    const heroTitle = page.locator("h1");
    await expect(heroTitle).toBeVisible();
  });
});
