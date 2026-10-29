FROM php:8.4-fpm AS php-base
RUN apt-get update && apt-get install -y git unzip libzip-dev default-mysql-client curl \
    && docker-php-ext-install pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY resources ./resources
COPY vite.config.js .
COPY public ./public
COPY vendor/livewire/flux/dist ./vendor/livewire/flux/dist
RUN npm run build

FROM php-base
COPY --from=assets /app/public/build /opt/build
CMD ["php-fpm"]
