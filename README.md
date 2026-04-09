# 💇‍♂️ Sistema de Reservas para Peluquería

[![PHP](https://img.shields.io/badge/PHP-8.2-purple?logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?logo=mysql)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ed?logo=docker)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-ISC-green)](LICENSE)

Sistema profesional de reservas para peluquerías. Backend modular en PHP con Domain-Driven Design, base de datos relacional, y API REST. Listo para producción con Docker Compose.



## 🎯 Qué es

Un sistema web que permite a clientes reservar citas en una peluquería. Los especialistas pueden registrar su disponibilidad horaria, y los admins confirman o rechazan las reservas. Generación de reportes, integración con Stripe para pagos, y optimización de imágenes con ImageKit.

**Caso de uso real:** Peluquería con 25 especialistas, 60+ clientes activos, ~40 reservas/semana.

## 🏗 Arquitectura (Resumen Ejecutivo)

```
PRESENTACIÓN (Controllers + Latte)
         ↓
APLICACIÓN (Services + DTOs)
         ↓
DOMINIO (Entities + Business Rules)
         ↓
INFRAESTRUCTURA (Repositories + BD)
```

- **7 tablas relaciones** normalizadas en MySQL 8.0
- **4 módulos funcionales** independientes (DDD): Usuarios, Reservas, Especialistas, Servicios
- **60+ tests unitarios** con Pest
- **Sin ORMs** - PDO con prepared statements (control total, sin overhead)
- **Deployment** en un único contenedor Docker

**Más detalles:** [`docs/ARQUITECTURA.md`](docs/ARQUITECTURA.md)

## 📊 Stack Tecnológico

| Capa | Tecnología | Versión | Rol |
|------|-----------|---------|-----|
| **Lenguaje** | PHP | 8.2 | Backend del servidor |
| **Servidor Web** | Apache | 2.4 | Sirve requests HTTP |
| **BD** | MySQL | 8.0.45 | Persistencia (7 tablas) |
| **Enrutador** | bramus/router | 1.6 | Mapea rutas HTTP |
| **Plantillas** | Latte | 3.1 | Motor seguro contra XSS |
| **Validación** | respect/validation | 2.4 | Input validation |
| **Pagos** | Stripe SDK | 19.1 | Procesamiento de pagos |
| **Imágenes** | ImageKit | 4.0 | CDN + optimización |
| **PDFs** | DOMPDF | 3.0 | Generación de reportes |
| **Testing** | Pest | 4.1 | Suite de tests moderna |
| **Mocking** | Mockery | 1.6 | Mocks para tests |
| **Env Config** | phpdotenv | 5.6 | Variables de entorno |

## 📁 Estructura

```
proyecto/
├── docs/                           # 📖 Documentación técnica
│   ├── ARQUITECTURA.md            # Stack, patrones, decisiones
│   ├── MODELADO_DATOS.md          # Diagrama ER, tablas, constraints
│   ├── CASOS_DE_USO.md            # Flujos de negocio principales
│   └── GUIA_INSTALACION.md        # Setup local + troubleshooting
│
├── src/
│   ├── Usuarios/                  # 🔐 Auth & user management
│   │   ├── Domain/                # Entity: Usuario, UserRole
│   │   ├── Application/           # AuthService, UserService
│   │   ├── Infrastructure/        # UserRepository, DB queries
│   │   └── Presentation/          # AuthController, routes
│   │
│   ├── Reservas/                  # 📅 Booking system (core)
│   │   ├── Domain/                # Entity: Reserva
│   │   ├── Application/           # ReservaService (business logic)
│   │   ├── Infrastructure/        # ReservaRepository
│   │   └── Presentation/          # ReservaController, API
│   │
│   ├── Especialistas/             # 💇 Specialist profiles
│   │   └── [Domain/App/Infra/Presentation]
│   │
│   ├── Servicios/                 # 🏷 Service catalog
│   │   └── [Domain/App/Infra/Presentation]
│   │
│   └── Shared/                    # 🔌 Cross-cutting concerns
│       ├── Domain/                # Custom exceptions
│       ├── Infrastructure/        # Router, DI, Email, Pagination
│       └── Presentation/          # Home, Admin, Stats controllers
│
├── tests/
│   ├── Unit/Services/             # 🧪 Service layer tests
│   ├── Unit/Repositories/         # Repository mocking tests
│   └── _ide_helper.php            # PHPStorm intellisense
│
├── views/                         # 🎨 Latte templates
│   ├── layouts/                   # Page templates
│   ├── reservas/                  # Booking UI
│   └── errors/                    # 404, 500, etc
│
├── public/                        # 🌐 Entry point & static assets
│   ├── index.php                  # Bootstrap de la app
│   ├── css/                       # Stylesheets
│   ├── js/                        # JavaScript
│   └── images/                    # Static images
│
├── docker-compose.yml             # 🐳 Services: app, db
├── Dockerfile                     # PHP 8.2 + Apache image
├── docker-init.sql                # BD schema + seed data
│
├── composer.json                  # PHP dependencies
├── composer.lock                  # Locked versions (reproducible)
├── .env.example                   # Template de env vars
└── README.md                      # Este archivo
```

## 🚀 Inicio Rápido (5 minutos)

### Requisitos
- Docker Desktop instalado
- Git

### Pasos

```bash
# 1. Clonar
git clone https://github.com/jlizancandela/proyecto.git
cd proyecto

# 2. Environment
cp .env.example .env

# 3. Build & Start
docker compose up -d --build

# 4. Instalar dependencias PHP
docker compose exec app composer install

# 5. Abrir
open http://localhost:8000
# o
curl http://localhost:8000
```

**Credenciales de prueba:**
- Admin: `test+alberto.garcia@jorgelizancandela.com` / `password123`
- Especialista: `test+maria.fernandez@jorgelizancandela.com` / `password123`
- Cliente: `test+fernando.alvarez@jorgelizancandela.com` / `password123`

**Más detalles:** [`docs/GUIA_INSTALACION.md`](docs/GUIA_INSTALACION.md)

## 📖 Documentación

| Documento | Contenido | Tiempo de Lectura |
|-----------|----------|------------------|
| **[ARQUITECTURA.md](docs/ARQUITECTURA.md)** | Stack, patrones DDD, decisiones técnicas, flujo de requests, seguridad, escalabilidad | 7 min |
| **[MODELADO_DATOS.md](docs/MODELADO_DATOS.md)** | Diagrama ER, esquema de 7 tablas, constraints, queries reales | 6 min |
| **[CASOS_DE_USO.md](docs/CASOS_DE_USO.md)** | Diagrama de casos de uso, flujos de negocio con secuencias Mermaid, state machine | 8 min |
| **[GUIA_INSTALACION.md](docs/GUIA_INSTALACION.md)** | Setup local, variables de env, comandos Docker, tests, troubleshooting, deployment | 10 min |

**Total: ~30 minutos para entender completamente el proyecto.**

## 🧪 Testing

```bash
# Todos los tests
docker compose exec app ./vendor/bin/pest

# Solo servicios
docker compose exec app ./vendor/bin/pest tests/Unit/Services/

# Con coverage
docker compose exec app ./vendor/bin/pest --coverage

# Watch mode
docker compose exec app ./vendor/bin/pest --watch
```

**Coverage actual:** ~75% en Application layer

## 🔑 Puntos Clave del Código

### 1. Domain-Driven Design
```php
// src/Reservas/Domain/Reserva.php
class Reserva {
    private int $id;
    private Usuario $cliente;
    private Especialista $especialista;
    private EstadoReserva $estado;  // Value Object
    
    public function puedeCancelarse(): bool { /* regla */ }
}
```
Las entidades concentran lógica de negocio. No son DTOs vacíos.

### 2. Service Layer Aislada
```php
// src/Reservas/Application/ReservaService.php
public function crearReserva(...): int {
    // Orquesta Repositories, valida reglas, maneja transacciones
    // Testeable sin mock de BD
}
```

### 3. Repository Pattern
```php
// src/Reservas/Infrastructure/ReservaRepository.php
public function findById(int $id): Reserva
public function findReservasEnFecha(int $especialista, \DateTime $fecha): array
```
Persistencia aislada. Cambiar MySQL a PostgreSQL = solo cambiar esto.

### 4. Validación Declarativa
```php
Validator::stringType()->length(1, 100)->validate($email);
Validator::intType()->positive()->validate($id);
```

### 5. Tests Claros
```php
// tests/Unit/Services/ReservaServiceTest.php
test('crear_reserva_verifica_disponibilidad', function () {
    // Arrange
    // Act
    // Assert
});
```

## 📈 Métricas

- **Líneas de código (src/):** ~3,500
- **Líneas de tests:** ~2,200
- **Tablas BD:** 7 (USUARIO, ESPECIALISTA, RESERVA, SERVICIO, HORARIO_ESPECIALISTA, ESPECIALISTA_SERVICIO, PASSWORD_RESET)
- **Endpoints API:** 15+
- **Routes web:** 20+
- **Controllers:** 8
- **Services:** 5
- **Repositories:** 6
- **Test classes:** 12

## 🔒 Seguridad

✅ **Password hashing:** Bcrypt
✅ **SQL Injection:** PDO prepared statements
✅ **XSS:** Latte escapa HTML por defecto
✅ **CSRF:** Tokens en formularios
✅ **Auth middleware:** Valida sesión antes de rutas protegidas
✅ **Reset token:** Expiración configurable

## 🌱 Escalabilidad Futura

- **Caché:** Redis en layer de Repositories
- **Eventos:** Event bus para notificaciones asincrónicas
- **API:** Ya separada en ApiControllers
- **Móvil:** Flutter/React Native consume endpoints API

## 🤝 Contribuciones

1. Fork del repo
2. Crea rama: `git checkout -b feature/mi-feature`
3. Haz commits: `git commit -am 'Add feature'`
4. Push: `git push origin feature/mi-feature`
5. Abre PR con descripción clara

## 📜 Licencia

ISC License - Ver [LICENSE](LICENSE)

## 👨‍💻 Autor

**Jorge Lizan Candela**
- Portfolio: [jorgelizancandela.com](https://jorgelizancandela.com)
- LinkedIn: [in/jlizancandela](https://linkedin.com/in/jlizancandela/)
- Email: jlizancandela@gmail.com

---

## 🎓 Para Reclutadores

**¿Qué demuestra este proyecto?**

1. **Arquitectura profesional:** Domain-Driven Design, separación de capas, SOLID principles
2. **PHP moderno:** Type hints, namespaces, composer, PSR-4 autoloading
3. **Diseño de BD:** Normalización 3NF, foreign keys, constraints, índices
4. **Testing:** Suite de tests unitarios, setup de testing, cobertura
5. **DevOps:** Docker, docker-compose, multi-stage builds
6. **Seguridad:** Hashing, prepared statements, middleware de autenticación
7. **Mentabilidad:** Código legible, documentación clara, fácil de mantener

**Stack:** PHP + MySQL + Docker + Pest

**Complejidad:** Empresa pequeña/mediana (~3,500 LOC, bien estructurado)

---

### Quick Links

- 📖 [Leer Arquitectura](docs/ARQUITECTURA.md)
- 📊 [Ver Modelo de Datos](docs/MODELADO_DATOS.md)
- 🎬 [Flujos de Negocio](docs/CASOS_DE_USO.md)
- 🛠 [Instalar Localmente](docs/GUIA_INSTALACION.md)
- 🐳 [Entender Docker Setup](docker-compose.yml)
- 🧪 [Revisar Tests](tests/Unit/)
