# Project: Hair Salon Booking System (Proyecto Peluquería)

## Overview
A web-based booking system for a hair salon, developed as a final project for DAW. The project follows a KISS philosophy (Keep It Simple, Stupid) and uses a custom modular architecture.

## Tech Stack
- **Backend:** PHP 8.x (Custom Framework)
  - `bramus/router`: Routing
  - `latte/latte`: Template Engine
  - `respect/validation`: Data Validation
- **Frontend:**
  - Preact (with `@nanostores` for state management)
  - Bootstrap 5
  - Modern JavaScript (ES Modules)
- **Database:** MariaDB
- **Infrastructure:** DDEV (Docker-based)
- **External Services:**
  - ImageKit (Image hosting)
  - Brevo (Email service)
- **Testing:**
  - **E2E:** Playwright (`tests/playwright`)
  - **Unit (JS):** Vitest (`vitest.config.js`)
  - **Unit (PHP):** Pest (`tests/Unit`)

## Project Structure
The project follows a Domain-Driven Design (Lite) approach, organizing code by functionality rather than layers.

### Source Code (`src/`)
- **`Especialistas/`**: Logic related to hair specialists (profiles, availability).
- **`Reservas/`**: Core booking logic, cancellations, and modifications.
- **`Servicios/`**: Catalog of services (cuts, coloring, etc.).
- **`Usuarios/`**: Authentication (Login/Register), profile management, and roles (Admin, Client, Specialist).
- **`Shared/`**: Shared infrastructure, router configuration (`Infrastructure/Router/`), and common utilities.

### Views (`views/`)
Latte templates organized by context:
- `layouts/`: Master layouts for Landing, Admin Panel, and User Panel.
- `pages/`: Specific page views.
- `components/`: Reusable UI components.
- `pdf/`: Templates for PDF export.

## Database Schema (Key Tables)
Defined in `init.sql`:
- **`USUARIO`**: Central user table with `rol` (Admin, Cliente, Especialista).
- **`ESPECIALISTA`**: Extended profile for specialists (linked to `USUARIO`).
- **`SERVICIO`**: Services offered with duration and price.
- **`RESERVA`**: Bookings linking Client, Specialist, and Service.
- **`HORARIO_ESPECIALISTA`**: Weekly working hours definition.

## Key Commands

### Environment (DDEV)
```bash
ddev start      # Start the project
ddev ssh        # Access the container shell
```

### Frontend Build
```bash
npm run watch   # Development mode
npm run build   # Production build
```

### Testing
```bash
# E2E Tests (Playwright)
cd tests/playwright
npx playwright test

# JS Unit Tests (Vitest)
npm run test:unit

# PHP Unit Tests (Pest)
./vendor/bin/pest
```

## Development Guidelines

1.  **KISS Principle:** Keep it simple. Do not over-engineer.
2.  **Language:** Write all code, variable names, and comments in **English**.
3.  **Documentation:** Use JSDoc and PHPDoc for functions and methods.
4.  **Commits:** Write simple, "humanized" commit messages in **English**.
5.  **Workflow:** Create Branch -> Commit -> Push -> PR (using `gh cli`).
