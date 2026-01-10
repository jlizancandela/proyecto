set shell := ["bash", "-c"]

# Variables de configuración
IMAGE := "jlizancandela/peluqueria:latest"
DOCKERFILE := "Dockerfile"

# Listar todos los comandos disponibles
default:
    @just --list

# ==========================================
# 🛠️  ENTORNO DE DESARROLLO (DDEV)
# ==========================================

# Levantar el entorno de desarrollo
up:
    ddev start

# Apagar el entorno
down:
    ddev stop

# Reiniciar el entorno
restart: down up

# Entrar a la terminal del contenedor web
ssh:
    ddev ssh

# Instalar dependencias (PHP y Node)
install:
    ddev composer install
    npm install

# ==========================================
# 🎨 FRONTEND
# ==========================================

# Compilar assets en modo desarrollo y observar cambios
watch:
    npm run watch

# Compilar assets para producción
build-assets:
    npm run build

# ==========================================
# 🧪 TESTING & CALIDAD
# ==========================================

# Ejecutar TODOS los tests (Unitarios PHP + JS)
test: test-php test-js

# Ejecutar tests de PHP (Pest) dentro del contenedor
test-php:
    ddev exec ./vendor/bin/pest

# Ejecutar tests unitarios de JS (Vitest)
test-js:
    npm run test:unit

# Ejecutar tests E2E (Playwright) - Requiere entorno levantado
test-e2e:
    cd tests/playwright && npx playwright test

# ==========================================
# 🚀 DEPLOY / DOCKER
# ==========================================

# Construir imagen de Docker localmente
build-image:
    docker buildx build -t {{IMAGE}} -f {{DOCKERFILE}} .

# Construir y pushear imagen (Requiere login)
publish:
    docker buildx build --push -t {{IMAGE}} -f {{DOCKERFILE}} .
