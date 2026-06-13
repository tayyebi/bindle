FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

RUN addgroup -g 1000 app && \
    adduser -u 1000 -G app -s /bin/sh -D app

RUN chown -R app:app /var/www

USER app

EXPOSE 9000
