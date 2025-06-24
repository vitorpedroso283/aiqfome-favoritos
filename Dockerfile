# Imagem base com PHP 8.4 e extensões Laravel
FROM php:8.4-fpm-alpine

# Instala as extensões e dependências para Laravel e PostgreSQL
RUN apk add --no-cache \
    git \
    zip \
    unzip \
    postgresql-dev \
    libpq-dev \
    oniguruma-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia o projeto para o container
WORKDIR /var/www/html
COPY . .

# Dá permissão para o storage e cache
RUN chmod -R 777 storage bootstrap/cache

# Expõe a porta padrão do Laravel
EXPOSE 8000

# Comando para subir o Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
