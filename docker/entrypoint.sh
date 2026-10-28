#!/bin/sh
set -e

# Buat symlink storage bila belum ada
php artisan storage:link || true

# Jalankan migrasi bila konfigurasi DB disetel
if [ -n "$DB_HOST" ] || [ -n "$DB_URL" ]; then
    echo "Menjalankan migrasi database..."
    php artisan migrate --force --seed || php artisan migrate --force || true
fi

# Cache konfigurasi untuk performa produksi
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Jalankan supervisor (menjalankan php-fpm dan nginx)
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf

