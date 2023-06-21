FROM php:8.2.5-fpm

ARG user
ARG uid

# Install Postgre PDO
RUN apt-get update
RUN apt-get install -y curl git vim libpq-dev libonig-dev zip unzip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u 1000 -d /home/1000 1000
RUN mkdir -p /home/1000/.composer && \
    chown -R 1000:1000 /home/1000

WORKDIR /app

RUN chown -R www-data:www-data /app

USER 1000

