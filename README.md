# 💇‍♂️ Hair Salon Booking System / Sistema de Reservas

[![Tech Stack](https://img.shields.io/badge/Stack-PHP_8.4_|_Preact_|_MySQL_8.0-blue.svg)](https://github.com/jlizancandela/proyecto)
[![License: ISC](https://img.shields.io/badge/License-ISC-000000.svg)](https://opensource.org/licenses/ISC)
[![Docker](https://img.shields.io/badge/Container-Docker_Compose-informational.svg)](https://www.docker.com/)

> **Select Language / Selecciona tu idioma:**
>
> 🇬🇧 [**ENGLISH**](#-english) | 🇪🇸 [**ESPAÑOL**](#-español)

---

## 🇬🇧 English

A professional, containerized booking system for hair salons. Built with a modular PHP backend and a Preact-driven reactive frontend. This project is production-ready using a multi-stage Docker build.

### 🛠 Tech Stack

#### Backend
- **Core:** PHP 8.4 (Apache)
- **Routing:** `bramus/router`
- **Template Engine:** `latte/latte`
- **Validation:** `respect/validation`
- **Database:** MySQL 8.0

#### Frontend
- **UI Library:** Preact
- **State Management:** `@nanostores`
- **Styling:** Bootstrap 5 & Icons
- **Bundler:** esbuild (Custom build script)

### 📂 Project Structure

```text
.
├── database/               # SQL migrations & seed data
├── public/                 # Static assets & Public entry point (index.php)
├── src/                    # Backend & Frontend Core Logic
│   ├── Especialistas/      # Specialist profiles & availability
│   ├── Reservas/           # Booking logic & management
│   ├── Servicios/          # Service catalog
│   ├── Usuarios/           # User management & Auth
│   ├── Shared/             # Common utilities & Infrastructure
│   └── js/                 # Preact/Frontend source code
├── tests/                  # Test suites (Unit, Integrity, E2E)
├── views/                  # Latte Templates
├── docker-compose.yml      # Docker service definitions
└── docker-init.sql         # DB schema & initial data
```

### 🚀 Getting Started (Docker)

No need to install PHP, Node, or MySQL locally. Just Docker.

#### 1. Clone the repository
```bash
git clone https://github.com/jlizancandela/proyecto.git
cd proyecto
```

#### 2. Configure Environment Variables
Copy the example environment file.
```bash
cp .env.example .env
```
*Note: Ensure `DB_HOST` is set to `db`.*

#### 3. Build and Run
```bash
docker compose up -d --build
```
> **Ports:**
> - **App:** http://localhost:8082
> - **Database:** localhost:3307

#### 4. Access the App
Open your browser: [http://localhost:8082](http://localhost:8082)

### 🧪 Development & Testing

- **PHP Unit Tests (Pest):** `docker compose exec app ./vendor/bin/pest`
- **JS Unit Tests (Vitest):** `npm run test:unit`
- **E2E Tests (Playwright):** `npx playwright test` (inside `tests/playwright`)

### 🤝 Development Guidelines

1. **Language:** All code must be in **English**.
2. **Architecture:** Respect the modular domain structure.
3. **KISS Principle:** Keep it simple.

---

## 🇪🇸 Español

Un sistema de reservas profesional y contenerizado para peluquerías. Construido con un backend modular en PHP y un frontend reactivo impulsado por Preact. Listo para producción con Docker.

### 🛠 Tecnologías (Tech Stack)

#### Backend
- **Núcleo:** PHP 8.4 (Apache)
- **Enrutamiento:** `bramus/router`
- **Motor de Plantillas:** `latte/latte`
- **Validación:** `respect/validation`
- **Base de Datos:** MySQL 8.0

#### Frontend
- **Librería UI:** Preact
- **Gestión de Estado:** `@nanostores`
- **Estilos:** Bootstrap 5 & Icons
- **Empaquetador:** esbuild

### 📂 Estructura del Proyecto

(Ver estructura en la sección en inglés, es idéntica).

### 🚀 Comenzando (Docker)

No necesitas instalar nada localmente. Solo Docker.

#### 1. Clonar el repositorio
```bash
git clone https://github.com/jlizancandela/proyecto.git
cd proyecto
```

#### 2. Configurar Variables
Copia el archivo de ejemplo.
```bash
cp .env.example .env
```

#### 3. Construir e Iniciar
```bash
docker compose up -d --build
```
> **Puertos:**
> - **Aplicación:** http://localhost:8082
> - **Base de Datos:** localhost:3307

#### 4. Acceder
Navegador: [http://localhost:8082](http://localhost:8082)

### 🧪 Desarrollo y Pruebas

- **Tests PHP (Pest):** `docker compose exec app ./vendor/bin/pest`
- **Tests JS (Vitest):** `npm run test:unit`
- **Tests E2E (Playwright):** `npx playwright test` (en `tests/playwright`)

### 🤝 Guías de Desarrollo

1. **Idioma:** Todo el código en **Inglés**.
2. **Arquitectura:** Respetar la estructura modular.
3. **Principio KISS:** Mantenerlo simple.


