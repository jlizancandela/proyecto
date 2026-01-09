# Stage 1: Build Frontend Assets
FROM node:20 AS frontend_build
WORKDIR /app
COPY package.json package-lock.json ./
COPY scripts/ ./scripts/
COPY src/ ./src/ 
# Copiar `src` es necesario si tus scripts de build dependen de él (ej: esbuild buscando entrypoints)
# Si no, `npm run build` fallará si no encuentra los archivos fuente.
RUN npm install
RUN npm run build

# Stage 2: Install PHP Dependencies
FROM composer:latest AS composer_build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-interaction

# Stage 3: Production Image
FROM php:8.4-apache
WORKDIR /var/www/html

# Install system dependencies for PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy PHP dependencies from Stage 2
COPY --from=composer_build /app/vendor ./vendor

# Copy Frontend Assets from Stage 1
# Assuming compiled assets go to public/js/build and public/css based on previous analysis
COPY --from=frontend_build /app/public/js/build ./public/js/build
# Copy other compiled assets if any exists. safely copy public structure if needed.

# Copy Application Source Code
COPY . .

# Copy .env.example to .env if .env doesn't exist (Runtime check, acceptable here)
# But ideally handled by entrypoint or volume mapping in prod
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Set permissions
RUN mkdir -p /var/www/html/temp \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/temp

# Configure Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
CMD ["apache2-foreground"]
