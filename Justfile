set shell := ["bash", "-c"]

IMAGE := "jlizancandela/peluqueria:latest"
DOCKERFILE := "Dockerfile"
SERVER_IP := "138.199.203.37"

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
    ssh root@{{SERVER_IP}} just --justfile '~/proyecto/justfile' --working-directory '~/proyecto' deploy
