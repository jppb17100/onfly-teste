#!/bin/bash

# Configuração de permissões (essencial para volumes Docker)
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Instala dependências (somente se não estiverem instaladas)
if [ ! -d "vendor" ]; then
    composer install --no-interaction --optimize-autoloader
fi

# Verifica se o banco está respondendo antes de executar as migrations
while ! php artisan db:monitor > /dev/null 2>&1; do
    echo "Aguardando o banco de dados ficar disponível..."
    sleep 2
done

# Executa as migrations (com tratamento de erro)
if php artisan migrate --force; then
    echo "Migrations executadas com sucesso"
else
    echo "Falha ao executar migrations" >&2
    exit 1
fi

# Executa otimizações
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configuração do worker da queue
QUEUE_LOG="/var/www/html/storage/logs/queue-$(date +'%Y-%m-%d').log"
touch $QUEUE_LOG
chown www-data:www-data $QUEUE_LOG

# Inicia o worker da queue com reinício automático
while true; do
    nohup php artisan queue:work --tries=3 --timeout=30 >> $QUEUE_LOG 2>&1 &
    QUEUE_PID=$!

    # Monitora o worker e reinicia se cair
    wait $QUEUE_PID
    echo "Queue worker caiu, reiniciando..." >> $QUEUE_LOG
    sleep 2
done &

# Mantém o PHP-FPM em primeiro plano (evita que o container pare)
exec php-fpm