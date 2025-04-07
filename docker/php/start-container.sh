#!/bin/sh

# Garantir criação do diretório e permissões
mkdir -p /var/run/supervisor
chmod 777 /var/run/supervisor

# Iniciar PHP-FPM
php-fpm -D

# Iniciar Supervisor
/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf

# Manter o container ativo
tail -f /dev/null