#!/bin/bash
set -e

# Adaptar el puerto dinámico de Render
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf

# Crear y preparar archivo SQLite si no existe
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite || true

# Enlazar storage para subida de imágenes
php artisan storage:link --no-interaction || true

# Ejecutar migraciones automáticamente
php artisan migrate --force --no-interaction || true

# Asegurar permisos COMPLETOS para www-data sobre base de datos y storage (DESPUÉS de migraciones)
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 777 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod 666 /var/www/html/database/database.sqlite || true

# Limpiar caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Iniciar Apache
exec apache2-foreground
