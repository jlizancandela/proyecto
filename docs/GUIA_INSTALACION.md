# Guía de Instalación y Setup

## Requisitos Previos

### Sistema Operativo
- **macOS** (M1/Intel), **Linux** (Debian/Ubuntu), o **Windows** (WSL2)
- Docker Desktop instalado: https://www.docker.com/products/docker-desktop

### Software Base
| Requisito | Versión | Instalación |
|-----------|---------|-------------|
| Docker | 24.x+ | `docker --version` |
| Docker Compose | 2.x+ | `docker compose version` |
| Git | 2.x+ | `git --version` |
| Node.js + npm | 16+ | `node --version` |

### Verificar Prerequisites
```bash
docker --version    # Docker version 24.0+
docker compose version  # Docker Compose version 2.0+
git --version       # git version 2.x
node --version      # v16+ (para testing/tooling)
npm --version       # 8+
```

Si falta algo:
```bash
# macOS
brew install docker docker-compose git node

# Linux (Ubuntu/Debian)
sudo apt install docker.io docker-compose git nodejs npm
sudo usermod -aG docker $USER  # Usa docker sin sudo

# Windows: Instala Docker Desktop desde el sitio oficial
```

---

## Instalación (5 minutos)

### 1. Clonar Repositorio
```bash
git clone https://github.com/jlizancandela/proyecto.git
cd proyecto
```

### 2. Variables de Entorno
```bash
# Copiar template
cp .env.example .env

# El .env debe tener (ya está en .env.example):
# DB_HOST=db
# DB_PORT=3306
# DB_NAME=sistema_reservas
# DB_USER=db
# DB_PASSWORD=db
# APP_ENV=development
# APP_DEBUG=true
```

### 3. Construir e Iniciar Contenedores
```bash
# Construir imagen Docker
docker compose build

# Levantar servicios (PHP, MySQL, Apache)
docker compose up -d

# Verificar que los servicios están corriendo
docker compose ps
```

**Salida esperada:**
```
NAME                COMMAND                  SERVICE             STATUS
proyecto-app-1     "docker-php-entryp..."   app                 Up 2 seconds
proyecto-db-1      "docker-entrypoint..."   db                  Up 3 seconds
```

### 4. Instalar Dependencias PHP
```bash
# Instalar vendor/ con Composer
docker compose exec app composer install

# Si Composer no estaba en el contenedor:
docker compose exec app php -r "copy('https://getcomposer.org/installer', 'composer-setup.php'); php composer-setup.php; unlink('composer-setup.php');"
docker compose exec app php composer.phar install
```

### 5. Inicializar Base de Datos
```bash
# La BD se crea automáticamente en docker-init.sql
# Verificar que la BD existe y tiene datos
docker compose exec db mysql -u db -pdb sistema_reservas -e "SELECT COUNT(*) FROM USUARIO;"

# Salida: COUNT(*) = 62 (usuarios de prueba ya seeded)
```

### 6. Verificar Instalación
```bash
# Abrir en navegador
open http://localhost:8000
# o
curl http://localhost:8000
```

**Deberías ver:** Página de inicio del sistema de reservas ✅

---

## Estructura de Carpetas Post-Setup

```
proyecto/
├── src/                    # Código de la aplicación (PSR-4)
├── tests/                  # Tests unitarios (Pest)
├── database/               # Seeds y migraciones
├── views/                  # Templates Latte
├── public/                 # Assets estáticos (CSS, JS, imágenes)
├── docs/                   # Documentación (ARQUITECTURA, etc)
├── vendor/                 # Dependencias (Composer) - gitignored
├── docker-compose.yml      # Configuración multi-contenedor
├── Dockerfile              # Definición imagen PHP
├── composer.json           # Dependencias y autoload
├── .env                    # Variables de entorno
└── .env.example            # Template de .env
```

---

## Variables de Entorno Detalladas

```bash
# .env (NUNCA subir a repo)

# Base de datos
DB_HOST=db                  # Nombre del servicio en docker-compose.yml
DB_PORT=3306               # Puerto MySQL
DB_NAME=sistema_reservas   # Nombre BD
DB_USER=db                 # Usuario BD
DB_PASSWORD=db             # Password BD (cambiar en producción)

# Aplicación
APP_ENV=development        # development | production
APP_DEBUG=true             # true | false (en prod = false)
APP_URL=http://localhost:8000

# Email (usando EmailService)
MAIL_DRIVER=smtp           # Proveedor de email
MAIL_HOST=smtp.mailtrap.io # Ej: servicio testing
MAIL_PORT=465
MAIL_FROM=noreply@reservas.local
MAIL_USERNAME=xxx
MAIL_PASSWORD=xxx

# Stripe (pagos)
STRIPE_PUBLIC_KEY=pk_test_xxx
STRIPE_SECRET_KEY=sk_test_xxx

# ImageKit (CDN imágenes)
IMAGEKIT_PRIVATE_KEY=xxx
IMAGEKIT_PUBLIC_KEY=xxx
IMAGEKIT_URL=https://ik.imagekit.io/xxx/
```

---

## Comandos Comunes

### Desarrollo

```bash
# Ver logs en tiempo real
docker compose logs -f app

# Ejecutar comando en contenedor PHP
docker compose exec app php -v
docker compose exec app composer list

# Acceder a shell interactivo
docker compose exec app bash
# Dentro del contenedor:
# $ php -S localhost:8000 -t public/
# $ composer test

# Reiniciar servicios
docker compose restart

# Detener todo
docker compose down

# Detener y borrar volúmenes (cuidado: borra BD)
docker compose down -v
```

### Testing

```bash
# Correr toda la suite de tests
docker compose exec app ./vendor/bin/pest

# Tests solo de Services
docker compose exec app ./vendor/bin/pest tests/Unit/Services/

# Test específico con output verboso
docker compose exec app ./vendor/bin/pest tests/Unit/Services/ReservaServiceTest.php -v

# Con coverage (reporta % líneas cubiertas)
docker compose exec app ./vendor/bin/pest --coverage

# Watch mode (re-ejecuta al cambiar archivos)
docker compose exec app ./vendor/bin/pest --watch
```

### Base de Datos

```bash
# Acceder a MySQL interactivo
docker compose exec db mysql -u db -pdb sistema_reservas

# Una vez dentro (mysql>):
mysql> SHOW TABLES;
mysql> SELECT COUNT(*) FROM USUARIO;
mysql> SELECT * FROM RESERVA LIMIT 5;
mysql> EXIT;

# Hacer dump de la BD
docker compose exec db mysqldump -u db -pdb sistema_reservas > backup.sql

# Restaurar desde dump
docker compose exec -T db mysql -u db -pdb sistema_reservas < backup.sql
```

---

## Troubleshooting

### "Error: Can't connect to MySQL"
```bash
# Verificar que el contenedor db está corriendo
docker compose ps

# Si no está:
docker compose up -d db

# Verificar conectividad
docker compose exec app php -r "
try {
    \$pdo = new PDO('mysql:host=db:3306;dbname=sistema_reservas', 'db', 'db');
    echo 'BD: OK' . PHP_EOL;
} catch (PDOException \$e) {
    echo 'Error: ' . \$e->getMessage();
}
"
```

### "Port 8000 already in use"
```bash
# Cambiar puerto en docker-compose.yml:
# ports:
#   - "8080:80"  # Cambiar de 8000 a 8080

docker compose up -d
open http://localhost:8080
```

### "Composer install falla"
```bash
# Aumentar memoria PHP en Dockerfile
# FROM php:8.2-apache
# ENV PHP_MEMORY_LIMIT=512M

docker compose build --no-cache
docker compose exec app composer install
```

### "Permisos en /var/www/html"
```bash
# Si falla escritura en carpetas:
docker compose exec app chown -R www-data:www-data /var/www/html
docker compose exec app chmod -R 755 /var/www/html
```

---

## Arquitectura del Setup

```
┌─────────────────────────────────┐
│   Tu máquina (localhost)        │
├─────────────────────────────────┤
│  Docker Compose                 │
│  ┌──────────────┐  ┌──────────┐│
│  │   app (PHP)  │  │  db (My) ││
│  │  :8000       │  │  :3306   ││
│  │              │──┤          ││
│  │ Apache       │  │ MySQL8   ││
│  │ PHP 8.2      │  │ 8.0.45   ││
│  └──────────────┘  └──────────┘│
│        ↓                        │
│  http://localhost:8000         │
└─────────────────────────────────┘
```

### Acceso a Servicios

| Servicio | URL | Usuario | Password |
|----------|-----|---------|----------|
| Aplicación | http://localhost:8000 | - | - |
| MySQL | localhost:3306 | db | db |
| Admin Panel | http://localhost:8000/admin | alberto.garcia@... | (seed) |
| API | http://localhost:8000/api/v1 | - | - |

---

## Contraseñas de Testing

**Todos los usuarios seed tienen password (hasheado):**
```
Plain: password123
Hash: $2y$12$GdBcSm9p/r/umgtTjq/W9OFZR6idICYfEomq2WhRnJqTXrv4s.Qdq
```

**Logins de prueba:**
- **Admin:** test+alberto.garcia@jorgelizancandela.com / password123
- **Especialista:** test+maria.fernandez@jorgelizancandela.com / password123
- **Cliente:** test+fernando.alvarez@jorgelizancandela.com / password123

*Cambiar passwords en producción.*

---

## Deployment a Producción

### Pre-Requisitos
- VPS con Docker
- Dominio propio
- SSL/TLS configurado

### Pasos Básicos
```bash
# 1. Clonar repo en servidor
git clone https://github.com/jlizancandela/proyecto.git /app

# 2. Copiar .env y cambiar variables
cp .env.example .env
nano .env
# Cambiar: APP_ENV=production, APP_DEBUG=false
# Cambiar: DB_PASSWORD a algo seguro
# Cambiar: STRIPE_KEY, IMAGEKIT_KEY, etc.

# 3. Build y start
docker compose build
docker compose up -d

# 4. Nginx reverse proxy (opcional, recomendado)
# Configurar Nginx para que apunte a localhost:8000

# 5. SSL con Let's Encrypt
# certbot certonly --webroot -w /var/www/html -d tudominio.com

# 6. Backups automáticos
# cron: mysqldump cada día a S3
```

---

## Siguientes Pasos

1. ✅ **Sistema corriendo?** → Abre http://localhost:8000
2. 📚 **Entiende arquitectura** → Lee `docs/ARQUITECTURA.md`
3. **Ejecuta tests** → `docker compose exec app ./vendor/bin/pest`
4. 💻 **Modifica código** → Todo está en `src/`
5. **Revisa casos de uso** → Lee `docs/CASOS_DE_USO.md`

¡Bienvenido al proyecto!
