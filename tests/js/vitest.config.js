/**
 * @file Vitest configuration for unit testing.
 * @project app-reservas
 */

import { defineConfig } from "vitest/config";
import path from "node:path";

export default defineConfig({
  test: {
    environment: "jsdom",
    globals: true,
    setupFiles: "./setup.js",
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "../../"),
    },
  },
});
