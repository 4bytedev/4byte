ARG PHP_VERSION=8.4.12
ARG COMPOSER_VERSION=2.8
ARG BUN_VERSION="latest"
ARG ROOT="/var/www/html"

FROM composer:${COMPOSER_VERSION} AS vendor

FROM php:${PHP_VERSION}-cli-bookworm AS base

LABEL maintainer="4Byte <info@4byte.dev>"
LABEL org.opencontainers.image.title="4Byte Backend Dockerfile"
LABEL org.opencontainers.image.description="Production-ready Dockerfile for 4Byte Web Application"
LABEL org.opencontainers.image.source=https://github.com/4bytedev/web
LABEL org.opencontainers.image.licenses=MIT

ARG WWWUSER=1000
ARG WWWGROUP=1000
ARG TZ=UTC
ARG ROOT
ARG APP_ENV

ENV DEBIAN_FRONTEND=noninteractive \
    TERM=xterm-color \
    TZ=${TZ} \
    USER=4byte \
    APP_ENV=${APP_ENV} \
    ROOT=${ROOT} \
    COMPOSER_FUND=0 \
    COMPOSER_MAX_PARALLEL_HTTP=48

WORKDIR ${ROOT}

COPY deployment/.bashrc /root/

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apt-get update; \
    apt-get upgrade -yqq; \
    apt-get install -yqq --no-install-recommends --show-progress \
    apt-utils \
    curl \
    wget \
    vim \
    ncdu \
    procps \
    unzip \
    ca-certificates \
    supervisor \
    libsodium-dev \
    && install-php-extensions \
    apcu \
    bz2 \
    mbstring \
    bcmath \
    pdo_pgsql \
    opcache \
    exif \
    zip \
    intl \
    gd \
    redis \
    ldap \
    pcntl \
    swoole \
    && apt-get -y autoremove \
    && apt-get clean \
    && docker-php-source delete \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/log/lastlog /var/log/faillog

RUN userdel --remove --force www-data \
    && groupadd --force -g ${WWWGROUP} ${USER} \
    && useradd -ms /bin/bash --no-log-init --no-user-group -g ${WWWGROUP} -u ${WWWUSER} ${USER}

RUN mkdir -p /var/log /var/run \
    && chown -R ${USER}:${USER} ${ROOT} /var/log /var/run \
    && chmod -R a+rw ${ROOT} /var/log /var/run

RUN cp ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini

USER ${USER}

COPY --link --chown=${WWWUSER}:${WWWUSER} --from=vendor /usr/bin/composer /usr/bin/composer

COPY --link --chown=${WWWUSER}:${WWWUSER} deployment/supervisord/supervisord.conf /etc/
COPY --link --chown=${WWWUSER}:${WWWUSER} deployment/supervisord/supervisord.laravel.conf /etc/supervisor/conf.d/
COPY --link --chown=${WWWUSER}:${WWWUSER} deployment/php.ini ${PHP_INI_DIR}/conf.d/99-octane.ini
COPY --link --chown=${WWWUSER}:${WWWUSER} deployment/entrypoint /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint

###########################################
# Install production-only laravel dependencies
###########################################

FROM base AS common

USER ${USER}

COPY --link --chown=${WWWUSER}:${WWWUSER} . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-ansi \
    --no-scripts \
    --audit

###########################################
# Build frontend assets with Bun
###########################################

FROM oven/bun:${BUN_VERSION} AS build

ARG ROOT

WORKDIR ${ROOT}

COPY --link package.json bun.lock* ./

RUN bun install --frozen-lockfile

COPY --link --from=common ${ROOT} .

RUN bun run build

###########################################

FROM common AS runner

USER ${USER}

COPY --link --chown=${WWWUSER}:${WWWUSER} --from=build ${ROOT}/public public

RUN mkdir -p \
    storage/framework/{sessions,views,cache,testing} \
    storage/logs \
    bootstrap/cache && chmod -R a+rw storage

RUN composer dump-autoload \
    --optimize \
    --apcu \
    --no-dev

EXPOSE 80

ENTRYPOINT ["entrypoint"]
