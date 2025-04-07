FROM php:8.2-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip supervisor \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Criar diretórios e definir permissões
RUN mkdir -p /var/run/supervisor /var/log/supervisor \
    && chmod -R 777 /var/run/supervisor

# Copiar configurações do Supervisor
COPY supervisor/ /etc/supervisor/conf.d/

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar script de inicialização
COPY ./php/start-container.sh /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

WORKDIR /var/www/html

# Executar como root para garantir permissões
USER root
ENTRYPOINT ["start-container"]