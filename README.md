# 💇‍♂️ Hair Salon Booking System (Proyecto Peluquería)

[![Tech Stack](https://img.shields.io/badge/Stack-PHP_8.4_|_Preact_|_MySQL_8.0-blue.svg)](https://github.com/jlizancandela/proyecto)
[![License: ISC](https://img.shields.io/badge/License-ISC-000000.svg)](https://opensource.org/licenses/ISC)
[![Docker](https://img.shields.io/badge/Container-Docker_Compose-informational.svg)](https://www.docker.com/)

A professional, containerized booking system for hair salons. Built with a modular PHP backend and a Preact-driven reactive frontend. This project is production-ready using a multi-stage Docker build.

---

## 🛠 Tech Stack

### Backend
- **Core:** PHP 8.4 (Apache)
- **Routing:** `bramus/router`
- **Template Engine:** `latte/latte`
- **Validation:** `respect/validation`
- **Database:** MySQL 8.0

### Frontend
- **UI Library:** Preact
- **State Management:** `@nanostores`
- **Styling:** Bootstrap 5 & Icons
- **Bundler:** esbuild (Custom build script)

---

## 📂 Project Structure

```text
.
├── database/               # SQL migrations & seed data
├── public/                 # Static assets & Public entry point (index.php)
│   ├── css/                # Styles
│   ├── js/                 # JS (bundles are gitignored)
│   └── images/             # Static images
├── src/                    # Backend & Frontend Core Logic
│   ├── Especialistas/      # Specialist profiles & availability
│   ├── Reservas/           # Booking logic & management
│   ├── Servicios/          # Service catalog
│   ├── Usuarios/           # User management & Auth
│   ├── Shared/             # Common utilities & Infrastructure
│   └── js/                 # Preact/Frontend source code
├── tests/                  # Test suites
│   ├── Unit/               # PHP unit tests (Pest)
│   ├── Integrity/          # Integration tests
│   ├── playwright/         # E2E tests
│   └── js/                 # Frontend unit tests (Vitest)
├── views/                  # Latte Templates
│   ├── components/         # Reusable UI fragments
│   ├── layouts/            # Base templates (Admin, App, Landing)
│   └── pages/              # Main view templates
├── docker-compose.yml      # Docker service definitions
├── Dockerfile              # Container configuration
└── docker-init.sql         # DB schema & initial data
```

---

## 🚀 Getting Started (Docker)

No need to install PHP, Node, or MySQL locally. Just Docker.

### 1. Clone the repository
```bash
git clone https://github.com/jlizancandela/proyecto.git
cd proyecto
```

### 2. Configure Environment Variables
Copy the example environment file. The `docker-compose.yml` will automatically inject these into the containers.
```bash
cp .env.example .env
```
*Note: Ensure `DB_HOST` is set to `db` in your `.env` file to match the service name in docker-compose.*

### 3. Build and Run
This command will build the frontend, install PHP dependencies, and start the services.
```bash
docker compose up -d --build
```
> **Ports:**
> - **App:** http://localhost:8082
> - **Database:** localhost:3307

### 4. Database Setup
The database is automatically initialized using `docker-init.sql`. If you need to re-import it manually:
```bash
docker compose exec -T db mysql -u root -proot sistema_reservas < docker-init.sql
```

### 5. Access the App
Open your browser and navigate to: [http://localhost:8082](http://localhost:8082)

---

## 🧪 Development & Testing

### 1. PHP Unit Tests (Pest)
Run tests inside the container:
```bash
docker compose exec app ./vendor/bin/pest
```

### 2. JS Unit Tests (Vitest)
```bash
npm run test:unit
```

### 3. E2E Tests (Playwright)
The application must be running (e.g., via Docker) before starting E2E tests.
```bash
cd tests/playwright
npm install
npx playwright test
```

### Rebuilding Assets
If you make changes to the frontend and want to rebuild without restarting everything:
```bash
docker compose exec app npm run build
```

---

## 🤝 Development Guidelines

1. **Language:** All code (variables, comments, commits) must be in **English**.
2. **Architecture:** Respect the modular domain structure in `src/`.
3. **KISS Principle:** Don't over-engineer. Keep the logic simple and maintainable.

---
*Created with ❤️ by the Hair Salon Booking Team.*
