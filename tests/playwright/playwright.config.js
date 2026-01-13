/**
 * Playwright configuration file.
 * This file configures how the tests should be run.
 */

const { defineConfig, devices } = require("@playwright/test");
require("dotenv").config({ path: __dirname + "/.env" });

module.exports = defineConfig({
  testDir: "./tests",
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: "html",
  timeout: 60000, // Subimos el timeout general a 60s
  use: {
    baseURL: process.env.BASE_URL || "https://proyecto.ddev.site", // DDEV URL
    trace: "on-first-retry",
    ignoreHTTPSErrors: true, // DDEV uses self-signed certs
    video: "on",
    actionTimeout: 15000, // Timeout para cada acción (click, fill, etc)
    navigationTimeout: 30000, // Timeout para navegaciones
    launchOptions: {
      slowMo: 300, // 300ms es más humano pero no tan lento
    },
  },
  projects: [
    {
      name: "chromium",
      use: { ...devices["Desktop Chrome"] },
    },
  ],
});
