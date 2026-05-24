# syntax=docker/dockerfile:1.7
#
# Multi-stage build for the contacts app.
#
#   composer-bin → assets ┐
#                 vendor  ├→ production
#                 vendor  → vendor-dev ┐
#                                base ─┴→ dev → testing
#
# Targets:
#   production  — slim php-fpm image, no-dev vendor, built assets, app baked in.
#   dev         — local development; xdebug + node, source bind-mounted via compose.
#   testing     — CI-style image; dev vendor + source baked in, runs phpunit.
#
# Build a specific stage with:
#   docker build --target production -t contacts:prod .
#   docker build --target dev        -t contacts:dev  .
#   docker build --target testing    -t contacts:test .
#
# Pinned: PHP 8.5, Node 24, Composer 2.9.

ARG PHP_VERSION=8.5
ARG NODE_VERSION=24
ARG COMPOSER_VERSION=2.9


# =====================================================================
# composer-bin — provides the composer binary for later stages.
# =====================================================================
FROM composer:${COMPOSER_VERSION} AS composer-bin


# =====================================================================
# assets — builds frontend assets (Vite output) into /app/public/build.
# =====================================================================
FROM node:${NODE_VERSION}-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm \
    npm ci

COPY vite.config.js ./
COPY resources ./resources

RUN npm run build


# =====================================================================
# vendor — production-only PHP dependencies → /app/vendor.
#
# --ignore-platform-reqs skips PHP-version and ext-* checks: this stage
# only downloads packages, the runtime image (base) is the one that has
# the extensions. The PHP-version skip is also needed because
# phpoffice/phpspreadsheet 1.30 caps PHP at <8.5 — composer.json declares
# ^8.2 so the constraint is upstream-only. See memory: project-dep-pins.
# =====================================================================
FROM php:${PHP_VERSION}-cli AS vendor
WORKDIR /app

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        git \
        libzip-dev \
        unzip && \
    docker-php-ext-install zip && \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer-bin /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./

RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache \
    composer install \
        --no-dev \
        --no-scripts \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader \
        --ignore-platform-reqs


# =====================================================================
# vendor-dev — same as vendor, but with require-dev installed.
# =====================================================================
FROM vendor AS vendor-dev
RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache \
    composer install \
        --no-scripts \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --ignore-platform-reqs


# =====================================================================
# base — shared php-fpm runtime: extensions, php.ini, user.
# Nothing app-specific is baked here, so it caches well across stages.
# =====================================================================
FROM php:${PHP_VERSION}-fpm AS base
WORKDIR /app

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        bcmath \
        exif \
        gd \
        imagick \
        intl \
        opcache \
        pcntl \
        pdo_mysql \
        redis \
        zip

RUN apt-get update -y && \
    apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        sendmail \
        unzip && \
    rm -rf /var/lib/apt/lists/*

COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY --from=composer-bin /usr/bin/composer /usr/local/bin/composer

ARG HOST_USER_ID=1000
ARG HOST_GROUP_ID=1000
ENV HOST_USER_ID=$HOST_USER_ID \
    HOST_GROUP_ID=$HOST_GROUP_ID

RUN if getent group ${HOST_GROUP_ID} > /dev/null; then \
        useradd -r -u ${HOST_USER_ID} -g "$(getent group ${HOST_GROUP_ID} | cut -d: -f1)" dockeruser; \
    else \
        groupadd -g ${HOST_GROUP_ID} dockergroup && \
        useradd -r -u ${HOST_USER_ID} -g dockergroup dockeruser; \
    fi && \
    mkdir -p \
        /home/dockeruser \
        /app/storage/app/public \
        /app/storage/framework/cache \
        /app/storage/framework/sessions \
        /app/storage/framework/views \
        /app/storage/framework/testing \
        /app/storage/logs \
        /app/storage/tmp \
        /app/bootstrap/cache && \
    chown -R ${HOST_USER_ID}:${HOST_GROUP_ID} /home/dockeruser /app


# =====================================================================
# production — final image: php-fpm + app code + prod vendor + assets.
# =====================================================================
FROM base AS production

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --chown=${HOST_USER_ID}:${HOST_GROUP_ID} . /app
COPY --from=vendor --chown=${HOST_USER_ID}:${HOST_GROUP_ID} /app/vendor /app/vendor
COPY --from=assets --chown=${HOST_USER_ID}:${HOST_GROUP_ID} /app/public/build /app/public/build

USER dockeruser

CMD ["php-fpm"]


# =====================================================================
# dev — local development image. Source is bind-mounted by compose, so
# no COPY of app code here; vendor/ and node_modules/ live on the host.
# Includes xdebug and Node for `npm run dev` / `composer` inside the
# container.
# =====================================================================
FROM base AS dev

# xdebug for step-debugging from PHPStorm; pcov for fast PHPUnit code-
# coverage. PHPUnit auto-selects pcov over xdebug when both are loaded,
# and pcov adds ~0% runtime overhead when `pcov.enabled=0` (default), so
# keeping both in the dev image costs nothing for normal `composer test`
# runs and lets `--coverage-html` work without swapping images.
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    install-php-extensions xdebug pcov

RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION:-24}.x | bash - && \
    apt-get install -y --no-install-recommends nodejs && \
    rm -rf /var/lib/apt/lists/*

USER dockeruser

CMD ["php-fpm"]


# =====================================================================
# testing — CI-style image: source + dev vendor baked in, runs phpunit.
# =====================================================================
FROM dev AS testing

USER root
COPY --chown=${HOST_USER_ID}:${HOST_GROUP_ID} . /app
COPY --from=vendor-dev --chown=${HOST_USER_ID}:${HOST_GROUP_ID} /app/vendor /app/vendor
COPY --from=assets --chown=${HOST_USER_ID}:${HOST_GROUP_ID} /app/public/build /app/public/build

ENV APP_ENV=testing

USER dockeruser

CMD ["vendor/bin/phpunit"]
