FROM php:7.4-fpm

RUN apt update -y && apt upgrade -y \
    && apt install -y vim cron make libcurl4-gnutls-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*