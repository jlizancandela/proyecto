import { defineConfig } from "vitest/config";
import path from "path";

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
