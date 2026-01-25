FROM node:20 AS frontend_build
WORKDIR /app
COPY package.json package-lock.json ./
COPY scripts/ ./scripts/
COPY src/ ./src/ 
# esbuild needs src for entrypoints
RUN npm install
RUN npm run build

FROM composer:latest AS composer_build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-interaction

FROM php:8.4-apache
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN a2enmod rewrite

COPY --from=composer_build /app/vendor ./vendor
COPY . .

# Overwrite local assets with fresh build
COPY --from=frontend_build /app/public/js/build ./public/js/build

# Required template assets from node_modules
COPY --from=frontend_build /app/node_modules/bootstrap/dist/css/bootstrap.min.css ./node_modules/bootstrap/dist/css/bootstrap.min.css
COPY --from=frontend_build /app/node_modules/bootstrap/dist/css/bootstrap.min.css.map ./node_modules/bootstrap/dist/css/bootstrap.min.css.map
COPY --from=frontend_build /app/node_modules/bootstrap-icons/font/ ./node_modules/bootstrap-icons/font/

RUN if [ ! -f .env ]; then cp .env.example .env; fi

RUN mkdir -p /var/www/html/temp \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/temp

ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
CMD ["apache2-foreground"]
