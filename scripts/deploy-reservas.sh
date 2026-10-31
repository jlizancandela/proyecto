#!/usr/bin/env bash
#
# Pull the published image into the hosting stack and recreate only the app.
#
# The publish workflow pushes a rebuilt image whenever the interpreter or the
# application changes. Nothing was pulling it, so the running container kept
# whatever it had started with. This runs on a timer to close that gap.
#
# Only the app service is recreated: Traefik, the database and the other
# services keep running.
#
# The pulled image is verified before the running container is touched. An
# image whose interpreter predates the patch for its branch is rejected and
# the stack is left exactly as it was.

set -euo pipefail

STACK_DIR="${STACK_DIR:-/home/jorge/svr}"
SERVICE="${SERVICE:-reservas}"
CHECK_SCRIPT="/var/www/html/scripts/check-php-version.php"

log() {
    printf '[deploy-reservas] %s\n' "$1"
}

fail() {
    printf '[deploy-reservas] %s\n' "$1" >&2
    exit 1
}

cd "$STACK_DIR"

image="$(docker compose config --format json \
    | jq -r --arg service "$SERVICE" '.services[$service].image')"

if [ -z "$image" ] || [ "$image" = "null" ]; then
    fail "could not resolve the image for service $SERVICE"
fi

log "pulling $image"
docker compose pull "$SERVICE"

# The check lives inside the published image. An image too old to contain it
# fails here, which is the safe outcome: an unverified build never restarts
# the app.
if ! docker run --rm --entrypoint php "$image" "$CHECK_SCRIPT"; then
    fail "refusing to deploy an unpatched interpreter"
fi

log "recreating $SERVICE"
docker compose up -d "$SERVICE"

log "done"
