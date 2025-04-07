FROM php:8.2-fpm

# Instalar dependências (removido supervisor)
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Configurar diretórios do Laravel
RUN mkdir -p /var/www/html/storage/framework/{views,cache,sessions} \
    && mkdir -p /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar script de inicialização
COPY ./php/start-container.sh /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

WORKDIR /var/www/html

# Definir usuário
USER www-data

ENTRYPOINT ["start-container"]