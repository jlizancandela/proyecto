// Global test setup for Vitest.

import { vi } from "vitest";

// Mock global fetch
global.fetch = vi.fn();

// Mock bootstrap (used in modals)
global.bootstrap = {
  Modal: vi.fn(() => ({
    show: vi.fn(),
    hide: vi.fn(),
  })),
};

// Reset mocks before each test
beforeEach(() => {
  vi.clearAllMocks();
});
