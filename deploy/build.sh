#!/usr/bin/env bash
# Build script for the server's deploy-app webhook.
#
# server-config's deploy/update-app.sh runs this with APP set and CWD = repo root,
# and expects it to produce ${APP}:latest and (for an nginx-fronted app)
# ${APP}-web:latest.
#
# WHY this file exists rather than relying on the webhook's default `docker build`:
# this repo's Dockerfile is multi-stage (assets/vendor/base/production/dev/testing),
# and a plain `docker build` builds the LAST stage -- `testing` -- which carries dev
# dependencies, xdebug and Node. Production needs `--target production`.
set -euo pipefail

# The deploy manager calls this as `./deploy/build.sh <app>`, server-config's
# update-app.sh exports APP instead -- accept either.
APP="${1:-${APP:-contacts}}"

# Vite inlines VITE_* while the image is being built, so an exported shell
# variable never reaches it: forward every VITE_* in the environment as a build
# arg. The deploy manager exports its project variables before running this, so
# defining one there (with the build_arg flag) is all it takes. Values are not
# printed -- only the names, so the log shows what was forwarded.
BUILD_ARGS=()
for name in $(compgen -e | grep '^VITE_' | sort); do
    BUILD_ARGS+=(--build-arg "${name}=${!name}")
    echo "[build] forwarding ${name} to the image build"
done

echo "[build] ${APP}:latest  (Dockerfile target: production)"
docker build --target production "${BUILD_ARGS[@]}" -t "${APP}:latest" .

echo "[build] ${APP}-web:latest  (nginx front, serves public/ from the app image)"
docker build -f docker/nginx.Dockerfile --build-arg "APP_IMAGE=${APP}:latest" -t "${APP}-web:latest" .

echo "[build] done: $(docker image inspect -f '{{.RepoTags}}' "${APP}:latest") $(docker image inspect -f '{{.RepoTags}}' "${APP}-web:latest")"
