# 💇‍♂️ Sistema de Reservas para Peluquería (Hair Salon Booking System)

> 🇬🇧 [English Version](README.md)

[![Tech Stack](https://img.shields.io/badge/Stack-PHP_8.4_|_Preact_|_MySQL_8.0-blue.svg)](https://github.com/jlizancandela/proyecto)
[![License: ISC](https://img.shields.io/badge/License-ISC-000000.svg)](https://opensource.org/licenses/ISC)
[![Docker](https://img.shields.io/badge/Container-Docker_Compose-informational.svg)](https://www.docker.com/)

Un sistema de reservas profesional y contenerizado para peluquerías. Construido con un backend modular en PHP y un frontend reactivo impulsado por Preact. Este proyecto está listo para producción utilizando una construcción multi-etapa de Docker.

---

## 🛠 Tecnologías (Tech Stack)

### Backend
- **Núcleo:** PHP 8.4 (Apache)
- **Enrutamiento:** `bramus/router`
- **Motor de Plantillas:** `latte/latte`
- **Validación:** `respect/validation`
- **Base de Datos:** MySQL 8.0

### Frontend
- **Librería UI:** Preact
- **Gestión de Estado:** `@nanostores`
- **Estilos:** Bootstrap 5 & Icons
- **Empaquetador:** esbuild (Script de construcción personalizado)

---

## 📂 Estructura del Proyecto

```text
.
├── database/               # Migraciones SQL y datos semilla
├── public/                 # Recursos estáticos y punto de entrada público (index.php)
│   ├── css/                # Estilos
│   ├── js/                 # JS (los paquetes compilados están en gitignore)
│   └── images/             # Imágenes estáticas
├── src/                    # Lógica del Backend y Frontend
│   ├── Especialistas/      # Perfiles de especialistas y disponibilidad
│   ├── Reservas/           # Lógica y gestión de reservas
│   ├── Servicios/          # Catálogo de servicios
│   ├── Usuarios/           # Gestión de usuarios y Autenticación
│   ├── Shared/             # Utilidades comunes e Infraestructura
│   └── js/                 # Código fuente Preact/Frontend
├── tests/                  # Suites de pruebas
│   ├── Unit/               # Pruebas unitarias PHP (Pest)
│   ├── Integrity/          # Pruebas de integración
│   ├── playwright/         # Pruebas E2E
│   └── js/                 # Pruebas unitarias Frontend (Vitest)
├── views/                  # Plantillas Latte
│   ├── components/         # Fragmentos de UI reutilizables
│   ├── layouts/            # Plantillas base (Admin, App, Landing)
│   └── pages/              # Plantillas de vistas principales
├── docker-compose.yml      # Definiciones de servicios Docker
├── Dockerfile              # Configuración del contenedor
└── docker-init.sql         # Esquema de BD y datos iniciales
```

---

## 🚀 Comenzando (Docker)

No necesitas instalar PHP, Node o MySQL localmente. Solo Docker.

### 1. Clonar el repositorio
```bash
git clone https://github.com/jlizancandela/proyecto.git
cd proyecto
```

### 2. Configurar Variables de Entorno
Copia el archivo de entorno de ejemplo. El archivo `docker-compose.yml` inyectará automáticamente estas variables en los contenedores.
```bash
cp .env.example .env
```
*Nota: Asegúrate de que `DB_HOST` esté configurado como `db` en tu archivo `.env` para coincidir con el nombre del servicio en docker-compose.*

### 3. Construir e Iniciar
Este comando construirá el frontend, instalará las dependencias de PHP e iniciará los servicios.
```bash
docker compose up -d --build
```
> **Puertos:**
> - **Aplicación:** http://localhost:8082
> - **Base de Datos:** localhost:3307

### 4. Configuración de la Base de Datos
La base de datos se inicializa automáticamente usando `docker-init.sql`. Si necesitas reimportarla manualmente:
```bash
docker compose exec -T db mysql -u root -proot sistema_reservas < docker-init.sql
```

### 5. Acceder a la Aplicación
Abre tu navegador y navega a: [http://localhost:8082](http://localhost:8082)

---

## 🧪 Desarrollo y Pruebas

### 1. Pruebas Unitarias PHP (Pest)
Ejecutar pruebas dentro del contenedor:
```bash
docker compose exec app ./vendor/bin/pest
```

### 2. Pruebas Unitarias JS (Vitest)
```bash
npm run test:unit
```

### 3. Pruebas E2E (Playwright)
La aplicación debe estar ejecutándose (ej. vía Docker) antes de iniciar las pruebas E2E.
```bash
cd tests/playwright
npm install
npx playwright test
```

### Reconstrucción de Recursos (Assets)
Si realizas cambios en el frontend y quieres reconstruir sin reiniciar todo:
```bash
docker compose exec app npm run build
```

---

## 🤝 Guías de Desarrollo

1. **Idioma:** Todo el código (variables, comentarios, commits) debe estar en **Inglés**.
2. **Arquitectura:** Respeta la estructura de dominio modular en `src/`.
3. **Principio KISS:** No sobreingeniería. Mantén la lógica simple y mantenible.

---
*Creado con ❤️ por el Equipo de Reservas de Peluquería.*
