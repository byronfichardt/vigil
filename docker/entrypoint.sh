#!/bin/sh
set -e

# Ensure SQLite database exists
touch /var/www/html/database/database.sqlite
chown www-data:www-data /var/www/html/database/database.sqlite

echo "Caching configuration..."
php artisan config:cache

echo "Linking storage..."
php artisan storage:link --force 2>/dev/null || true

echo "Running migrations..."
php artisan migrate --force

echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
