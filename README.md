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
├── src/                    # Core Logic (Domain-Driven Lite)
│   ├── Especialistas/      # Profiles & availability
│   ├── Reservas/           # Booking & cancellation logic
│   ├── Servicios/          # Services catalog
│   ├── Usuarios/           # Auth & profile management
│   └── Shared/             # Infrastructure & Common utilities
├── views/                  # Latte Templates
│   ├── components/         # Reusable UI parts
│   ├── layouts/            # Master layouts (Admin, Client, Landing)
│   └── pages/              # Specific page views
├── public/                 # Static assets & Entry point
├── tests/                  # Test suites (PHPUnit, Pest, Playwright, Vitest)
├── Dockerfile              # Multi-stage production build
├── docker-compose.yml      # Service orchestration
└── docker-init.sql         # Database initialization script
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

### 4. Database Setup
The database is automatically initialized using `docker-init.sql`. If you need to re-import it manually:
```bash
docker compose exec -T db mysql -u root -proot sistema_reservas < docker-init.sql
```

### 5. Access the App
Open your browser and navigate to:
- **Main App:** [http://localhost:8080](http://localhost:8080)

---

## 🧪 Development & Testing

### Running Tests inside the Container
```bash
# PHP Unit Tests (Pest)
docker compose exec app ./vendor/bin/pest

# JS Unit Tests (Vitest)
npm run test:unit
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
