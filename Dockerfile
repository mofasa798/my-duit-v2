FROM php:8.3-cli

# Add libsqlite3-dev to the installation list
RUN apt-get update && apt-get install -y \
    git curl unzip libzip-dev zip nodejs npm libsqlite3-dev

RUN docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

RUN php artisan config:cache

EXPOSE 8080

CMD ["sh", "-c", "touch database/database.sqlite && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080"]
