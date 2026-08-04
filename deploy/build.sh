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

APP="${APP:-contacts}"

echo "[build] ${APP}:latest  (Dockerfile target: production)"
docker build --target production -t "${APP}:latest" .

echo "[build] ${APP}-web:latest  (nginx front, serves public/ from the app image)"
docker build -f docker/nginx.Dockerfile --build-arg "APP_IMAGE=${APP}:latest" -t "${APP}-web:latest" .

echo "[build] done: $(docker image inspect -f '{{.RepoTags}}' "${APP}:latest") $(docker image inspect -f '{{.RepoTags}}' "${APP}-web:latest")"
