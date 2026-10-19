FROM php:8.3-cli-alpine

RUN apk add --no-cache git unzip libzip-dev postgresql-dev postgresql-client \
    && docker-php-ext-install zip pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
