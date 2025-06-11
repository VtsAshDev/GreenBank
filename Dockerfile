FROM php:8.2-cli

RUN apt-get update && apt-get install -y libzip-dev zip unzip curl git

RUN docker-php-ext-install pdo pdo_mysql zip sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
