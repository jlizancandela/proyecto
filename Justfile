set shell := ["bash", "-c"]

IMAGE := "jlizancandela/peluqueria:latest"
DOCKERFILE := "Dockerfile"
STACK_DIR := "/home/jorge/svr"

default:
    @just --list

up:
    docker compose up -d

down:
    docker compose down

logs:
    docker compose logs -f app

shell:
    docker compose exec app bash

install:
    docker compose exec app composer install
    npm install
    cd tests/playwright && npm install

build:
    npm run build

test: test-unit test-e2e

test-unit:
    docker compose exec app ./vendor/bin/pest
    npm run test:unit

test-e2e:
    cd tests/playwright && npx playwright test

publish:
    docker buildx build --push -t {{IMAGE}} -f {{DOCKERFILE}} .

# Pull the published image into the hosting stack (recreates only the app)
deploy:
    cd {{STACK_DIR}} && docker compose pull reservas && docker compose up -d reservas
