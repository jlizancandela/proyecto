set shell := ["bash", "-c"]

# Variables
IMAGE := "jlizancandela/peluqueria:latest"
DOCKERFILE := "Dockerfile"
SERVER_IP := "138.199.203.37"

# Listar comandos
default:
    @just --list

# --- 🚀 Ciclo de Vida (Docker Compose) ---

# Levantar el entorno de desarrollo
up:
    docker compose up -d

# Detener el entorno
down:
    docker compose down

# Ver logs de la aplicación
logs:
    docker compose logs -f app

# Entrar a la consola del contenedor
shell:
    docker compose exec app bash

# --- 📦 Dependencias y Assets ---

# Instalar dependencias de PHP y JS (Raíz y E2E)
install:
    docker compose exec app composer install
    npm install
    cd tests/playwright && npm install

# Build de assets frontend
build:
    npm run build

# --- 🧪 Testing ---

# Correr todos los tests (Unitarios + E2E)
test: test-unit test-e2e

# Tests unitarios (PHP con Pest y JS con Vitest)
test-unit:
    docker compose exec app ./vendor/bin/pest
    npm run test:unit

# Tests E2E con Playwright
test-e2e:
    cd tests/playwright && npx playwright test

# --- 🚢 Despliegue ---

# Buildear, pushear y disparar el deploy en el server
publish:
    docker buildx build --push -t {{IMAGE}} -f {{DOCKERFILE}} .
    ssh root@{{SERVER_IP}} just --justfile '~/proyecto/justfile' --working-directory '~/proyecto' deploy

# IA-powered commit, push and merge workflow
commit context="":
    node tools/git-automation.mjs "{{context}}"
