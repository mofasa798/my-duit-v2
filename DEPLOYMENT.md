# Deployment Guide

## MyDuit — Production Deployment

This guide covers several deployment options for MyDuit. Choose the one that best fits your needs.

---

## Prerequisites

- PHP 8.3+
- Composer
- Node.js 18+
- A server with SSH access (for VPS options)

---

## Option A: Traditional VPS (Nginx + PHP-FPM)

### 1. Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP, Nginx, and required extensions
sudo apt install -y nginx php8.3-fpm php8.3-sqlite php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath unzip git

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Application Setup

```bash
# Clone the repository
cd /var/www
git clone https://github.com/mofasa798/my-duit-v2.git
cd my-duit-v2

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Set up environment
cp .env.production.example .env
php artisan key:generate

# Create and migrate the database
touch database/database.sqlite
chmod 664 database/database.sqlite
php artisan migrate --force --seed

# Install frontend dependencies & build
npm install --ignore-scripts
npm run build

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Nginx Configuration

Create `/etc/nginx/sites-available/myduit`:

```nginx
server {
    listen 80;
    server_name myduit.example.com;
    root /var/www/my-duit-v2/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable the site
sudo ln -s /etc/nginx/sites-available/myduit /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 4. Queue Worker (Optional)

For background jobs, set up a Supervisor process:

```ini
[program:myduit-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/my-duit-v2/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/my-duit-v2/storage/logs/worker.log
stopwaitsecs=3600
```

---

## Option B: Docker Deployment

### Dockerfile

```dockerfile
FROM php:8.3-fpm AS base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libsqlite3-dev nodejs npm \
    && docker-php-ext-install pdo pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install & build frontend
RUN npm install --ignore-scripts && npm run build

# Production environment
RUN cp .env.production.example .env \
    && php artisan key:generate \
    && touch database/database.sqlite \
    && php artisan migrate --force --seed \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 9000
CMD ["php-fpm"]
```

### Docker Compose

```yaml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "9000:9000"
    volumes:
      - storage:/var/www/storage
      - database:/var/www/database
    environment:
      - APP_ENV=production
      - APP_DEBUG=false

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./public:/var/www/public
      - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app

volumes:
  storage:
  database:
```

### Nginx config for Docker (`docker/nginx.conf`)

```nginx
server {
    listen 80;
    root /var/www/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Option C: Platform as a Service (PaaS)

### Railway

1. Push your code to GitHub
2. Create a new project on [Railway](https://railway.app)
3. Connect your GitHub repository
4. Add a PHP starter
5. Set the build command:
   ```bash
   composer install --no-dev && npm install && npm run build
   ```
6. Set the start command:
   ```bash
   php artisan serve --host=0.0.0.0 --port=$PORT
   ```
7. Add environment variables from `.env.production.example`
8. Railway provides a SQLite volume automatically

### Vercel (Frontend Only)

For deploying just the frontend as a static site:

1. Build the frontend:
   ```bash
   npm run build
   ```
2. Deploy the `public/build` directory to Vercel
3. The backend must be deployed separately (use Railway or VPS for the API)

---

## Post-Deployment Checklist

- [ ] Application accessible at your domain
- [ ] `APP_DEBUG` is set to `false`
- [ ] `APP_ENV` is set to `production`
- [ ] Database migrations ran successfully
- [ ] All pages load without console errors
- [ ] Login and registration work
- [ ] Reports generate and export correctly
- [ ] Analytics charts render properly
- [ ] AG Grid inline editing works on the Dashboard
- [ ] SEO headers and meta tags are present
- [ ] SSL certificate is active (use Let's Encrypt)

---

## Environment Variables Reference

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_NAME` | Application name | `MyDuit` |
| `APP_ENV` | Environment | `production` |
| `APP_DEBUG` | Debug mode | `false` |
| `APP_URL` | Public URL | `https://myduit.example.com` |
| `DB_CONNECTION` | Database driver | `sqlite` |
| `SESSION_DRIVER` | Session storage | `database` |
| `CACHE_STORE` | Cache backend | `database` |
| `QUEUE_CONNECTION` | Queue driver | `database` |

---

## Troubleshooting

### Blank page or 500 error
- Check `storage/logs/laravel.log`
- Run `php artisan config:clear && php artisan route:clear && php artisan view:clear`

### SQLite permission denied
```bash
chmod 664 database/database.sqlite
chown -R www-data:www-data database
```

### Vite manifest not found
- Ensure you ran `npm run build` (not `npm run dev`)
- Verify `public/build/manifest.json` exists

### Route/404 errors
- Run `php artisan route:cache`
- Ensure Nginx `try_files` rule is correct
