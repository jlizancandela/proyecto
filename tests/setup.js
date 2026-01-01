/**
 * @file Global test setup for Vitest.
 * @project app-reservas
 */

import { vi } from "vitest";

// Mock global fetch
global.fetch = vi.fn();

// Mock bootstrap (used in modals)
global.bootstrap = {
  Modal: vi.fn(function () {
    return {
      show: vi.fn(),
      hide: vi.fn(),
    };
  }),
};

// Ensure window.bootstrap is available
global.window.bootstrap = global.bootstrap;

// Reset mocks before each test
beforeEach(() => {
  vi.clearAllMocks();
});
