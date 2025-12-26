/**
 * Playwright configuration file.
 * This file configures how the tests should be run.
 */

const { defineConfig, devices } = require("@playwright/test");

module.exports = defineConfig({
  testDir: "./tests",
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: "html",
  use: {
    baseURL: "https://proyecto.ddev.site", // DDEV URL
    trace: "on-first-retry",
    ignoreHTTPSErrors: true, // DDEV uses self-signed certs
  },
  projects: [
    {
      name: "chromium",
      use: { ...devices["Desktop Chrome"] },
    },
  ],
});
