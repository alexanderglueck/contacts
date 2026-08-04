# nginx front for the production deployment (built as <app>-web:latest).
#
# It COPIES public/ out of the already-built app image, so static files and the
# Vite build output are baked in and served directly by nginx -- no shared volume
# between the two containers, and no dependency on their start order.
#
# Build via deploy/build.sh (it builds contacts:latest first and passes it in):
#   docker build -f docker/nginx.Dockerfile --build-arg APP_IMAGE=contacts:latest -t contacts-web:latest .
ARG APP_IMAGE=contacts:latest

FROM ${APP_IMAGE} AS app

FROM nginx:1.27-alpine

# Production vhost: proxies PHP to the php-fpm container by its compose service
# name. NOTE this differs from .docker/nginx/default.conf, which is the LOCAL dev
# config and points at the dev compose service ('app').
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# The built application's public directory (index.php + public/build assets).
COPY --from=app /app/public /app/public
