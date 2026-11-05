#!/bin/bash
set -e

# Adaptar el puerto dinámico de Render
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf

# Asegurar archivo y permisos de base de datos SQLite y storage
touch /var/www/html/database/database.sqlite || true
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod 664 /var/www/html/database/database.sqlite || true

# Enlazar storage para subida de imágenes
php artisan storage:link --no-interaction || true

# Ejecutar migraciones automáticamente si la BD está conectada
php artisan migrate --force --no-interaction || true

# Limpiar caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Iniciar Apache
exec apache2-foreground
