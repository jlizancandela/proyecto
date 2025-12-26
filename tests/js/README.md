# JavaScript Unit Tests

Unit tests for vanilla JavaScript modules using Vitest.

## Setup

```bash
cd tests/js
npm install
```

## Running Tests

```bash
# Run tests in watch mode
npm test

# Run tests with UI
npm run test:ui

# Run tests with coverage
npm run test:coverage
```

## Test Structure

```
tests/js/
├── admin/
│   ├── bookings/
│   │   └── api.test.js       # Bookings API tests
│   └── servicios/
│       └── api.test.js       # Services API tests
├── setup.js                   # Global test setup
├── vitest.config.js          # Vitest configuration
└── package.json              # Dependencies
```

## Writing Tests

Tests import modules from the project using the `@` alias:

```javascript
import { fetchBooking } from "@/public/js/admin/bookings/api.js";
```

The `@` alias points to the project root, so you can import any JS file.

## Mocks

Global mocks are configured in `setup.js`:

- `fetch` - Mocked for API calls
- `bootstrap` - Mocked for modal interactions

## Coverage Goals

- **API modules**: 100% coverage
- **Utility functions**: 100% coverage
- **UI handlers**: 70%+ coverage
