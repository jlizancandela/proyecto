# Arquitectura del Sistema de Reservas

## Stack Tecnológico

| Componente | Versión | Justificación |
|-----------|---------|---------------|
| **PHP** | 8.x | Lenguaje principal del servidor |
| **MySQL** | 8.0.45 | Base de datos relacional con soporte para UTF-8 y transacciones |
| **Docker** | Compose | Gestión de contenedores para reproducibilidad |
| **Bramus Router** | 1.6 | Enrutador lightweight para PHP |
| **Latte** | 3.1 | Motor de plantillas con seguridad integrada |
| **PDO** | Nativo | Acceso a BD con prepared statements |
| **Respect/Validation** | 2.4 | Validación declarativa de datos |
| **Stripe** | 19.1 | Procesamiento de pagos |
| **ImageKit** | 4.0 | CDN y optimización de imágenes |
| **DOMPDF** | 3.0 | Generación de PDFs (reportes y documentos) |
| **Pest** | 4.1 | Testing moderno (dev) |
| **Mockery** | 1.6 | Mocking para tests (dev) |

## Arquitectura General

```mermaid
graph TB
    subgraph Frontend["Presentación"]
        Controller["Controllers<br/>(MVC)"]
        API["API Controllers<br/>(REST)"]
        Views["Vistas Latte"]
    end

    subgraph Application["Lógica de Negocio"]
        Service["Services<br/>(Use Cases)"]
        Transformer["Transformers<br/>(DTO)"]
    end

    subgraph Domain["Dominio"]
        Entity["Entidades<br/>(Usuario, Reserva, etc.)"]
        ValueObj["Value Objects<br/>(UserRole)"]
    end

    subgraph Infrastructure["Infraestructura"]
        Repository["Repositories<br/>(PDO)"]
        DB[(("MySQL<br/>sistema_reservas"))]
        Email["Email Service"]
        Payment["Stripe Integration"]
    end

    Controller --> Service
    API --> Service
    Views --> Controller
    Service --> Repository
    Service --> Entity
    Service --> Transformer
    Repository --> DB
    Service --> Email
    Service --> Payment
```

### Por Qué Este Diseño

La arquitectura implementa **Domain-Driven Design** (DDD) con separación en capas:

- **Presentación**: Controllers aislados. Si cambia la UI, el negocio no se ve afectado
- **Aplicación**: Services orquestan la lógica. Fácil de testear
- **Dominio**: Entidades concentran reglas de negocio
- **Infraestructura**: Detalles técnicos (BD, email, pagos) aislados

Esto permite cambiar MySQL por PostgreSQL, Latte por React, sin tocar la lógica central.

## Estructura de Módulos

```
src/
├── Usuarios/                    # Gestión de usuarios y autenticación
│   ├── Domain/                 # Entidades: Usuario, UserRole
│   ├── Application/            # AuthService, UserService
│   ├── Infrastructure/         # UserRepository, PasswordResetRepository
│   └── Presentation/           # AuthController, UserController
│
├── Reservas/                    # Gestión de reservas
│   ├── Domain/                 # Entidad: Reserva
│   ├── Application/            # ReservaService
│   ├── Infrastructure/         # ReservaRepository
│   └── Presentation/           # ReservaController, ReservaApiController
│
├── Especialistas/              # Gestión de especialistas
│   ├── Domain/                 # Entidades: Especialista, HorarioEspecialista
│   ├── Application/            # EspecialistaService
│   ├── Infrastructure/         # EspecialistaRepository
│   └── Presentation/           # EspecialistaController
│
├── Servicios/                  # Catálogo de servicios
│   ├── Domain/                 # Entidad: Servicio
│   ├── Application/            # ServicioService
│   ├── Infrastructure/         # ServicioRepository
│   └── Presentation/           # ServicioController
│
└── Shared/                     # Código transversal
    ├── Domain/                 # Excepciones base, interfaces
    ├── Infrastructure/         # Router, Middleware, DI, Email, Pagination
    └── Presentation/           # Controllers comunes (Home, Admin, Stats)
```

## Patrones de Diseño

### 1. Repository Pattern
```php
// Infrastructure/
public function findById(int $id): Reserva
public function findAll(): array
public function save(Reserva $reserva): int
public function delete(int $id): bool
```
Aísla la lógica de persistencia. Cambiar BD = solo cambiar Repository.

### 2. Service Layer (Use Cases)
```php
// Application/
class ReservaService {
    public function crearReserva(int $cliente, int $especialista, ...): int
    public function cancelarReserva(int $id): bool
}
```
Orquesta Repositories y Entidades. No mezcla presentación con negocio.

### 3. Entity Pattern
```php
// Domain/
class Reserva {
    private int $id;
    private Usuario $cliente;
    private Especialista $especialista;
    private EstadoReserva $estado;  // Value Object
    
    public function puedeCancelarse(): bool { /* regla */ }
    public function confirmar(): void { /* cambio de estado */ }
}
```
Las entidades conocen sus propias reglas. No son dumps de datos.

### 4. Value Objects
```php
// Domain/
enum UserRole {
    case Admin;
    case Especialista;
    case Cliente;
}
```
Tipos seguros para conceptos del dominio. Mejor que strings sueltos.

### 5. Transformer Pattern
```php
// Presentation/Transformers/
class ReservaTransformer {
    public static function toJson(Reserva $reserva): array
    public static function toHtml(Reserva $reserva): string
}
```
Separa representación de datos internos. API devuelve DTO, no entidades.

## Flujo de una Solicitud

```
1. GET /reservas/agendar
   ↓
2. ReservaController::agendarForm()
   ├─ Carga especialistas (EspecialistaService::listar())
   ├─ Carga servicios (ServicioService::listar())
   └─ Renderiza Latte + datos
   ↓
3. Usuario completa el formulario
   ↓
4. POST /reservas/agendar
   ↓
5. ReservaController::store()
   ├─ Valida entrada (Respect\Validation)
   ├─ Llama ReservaService::crearReserva()
   ├─ ReservaService orquesta:
   │  ├─ Verifica disponibilidad (HorarioEspecialista)
   │  ├─ Valida cliente existe
   │  ├─ Crea Reserva entity
   │  └─ Persiste via ReservaRepository
   ├─ Envía email (EmailService)
   └─ Redirige a confirmación
```

## Decisiones Técnicas

### ¿Por qué PDO y no Doctrine ORM?

**PDO:**
- Proyecto pequeño/mediano (7 tablas, lógica de negocio clara)
- Queries explícitos = fácil debuguear
- Control fino sobre performance
- Menos overhead

**Doctrine estaría bien si:**
- Tendríamos 50+ entidades
- Cambios frecuentes de BD
- Necesitamos query builder complejo

### ¿Por qué Latte y no Blade/Twig?

- Latte es de Nette, la framework moderna PHP
- Compila a PHP puro (0 overhead en producción)
- Sintaxis clara y segura contra XSS por defecto
- Debugging es trivial (errores en línea)

### ¿Por qué Bramus Router?

- Ligero: 1 archivo, 200 líneas de código
- Suficiente para esta escala
- Si crece → migrar a Slim o Symfony es trivial

### Docker en Desarrollo

```dockerfile
FROM php:8.2-apache
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN docker-php-ext-install pdo_mysql
```
Garantiza que todos los devs usen MySQL 8.0.45 exactamente. CI/CD sin sorpresas.

## Testing

```php
// tests/Unit/Services/ReservaServiceTest.php
class ReservaServiceTest extends TestCase {
    public function test_crear_reserva_verifica_disponibilidad()
    public function test_crear_reserva_falla_si_especialista_no_existe()
    public function test_cancelar_reserva_actualiza_estado()
}
```

Pest permite tests claros sin ruido. Mockery simula Repositories sin BD.

## Seguridad

- **Password hashing**: Bcrypt ($2y$12)
- **SQL Injection**: PDO prepared statements
- **CSRF**: Tokens en formularios
- **XSS**: Latte escapa HTML por defecto
- **Auth**: Middleware verifica sesión antes de rutas protegidas
- **Reset de password**: Tokens con expiración

## Escalabilidad Futura

- **Caché**: Añadir Redis en layer de Repositories sin tocar Services
- **Eventos**: Implementar event bus cuando necesite notificaciones asincrónicas
- **API**: Ya separada (ReservaApiController) para permitir móvil/SPA
- **Base datos**: Estructura normalizada (3NF) permite migrar sin ruptura
