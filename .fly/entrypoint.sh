#!/usr/bin/env bash

# ============================================================
#  Fly.io Entrypoint – Initializes persistent storage volume
#  The volume is mounted at /var/www/html/storage
# ============================================================

STORAGE="/var/www/html/storage"

# 1. Ensure all required storage subdirectories exist
#    (Volume mounts start empty on first deploy)
mkdir -p "$STORAGE/app/backups" \
         "$STORAGE/app/public" \
         "$STORAGE/database" \
         "$STORAGE/framework/cache/data" \
         "$STORAGE/framework/sessions" \
         "$STORAGE/framework/testing" \
         "$STORAGE/framework/views" \
         "$STORAGE/logs"

# 2. SQLite persistence: Copy seed database from image if not yet in volume
DB_PATH="$STORAGE/database/database.sqlite"
if [ ! -f "$DB_PATH" ]; then
    echo "[entrypoint] First deploy detected – seeding SQLite database..."
    if [ -f /var/www/html/database/database.sqlite ]; then
        cp /var/www/html/database/database.sqlite "$DB_PATH"
    else
        touch "$DB_PATH"
        /usr/bin/php /var/www/html/artisan migrate --force --seed
    fi
fi

# 3. Ensure correct ownership
chown -R www-data:www-data "$STORAGE"

# 4. Run Laravel caches
/usr/bin/php /var/www/html/artisan config:cache --no-ansi -q
/usr/bin/php /var/www/html/artisan route:cache --no-ansi -q
/usr/bin/php /var/www/html/artisan view:cache --no-ansi -q

# 5. Run any pending migrations
/usr/bin/php /var/www/html/artisan migrate --force

# 6. Run user scripts, if they exist
for f in /var/www/html/.fly/scripts/*.sh; do
    [ -f "$f" ] && bash "$f" -e
done

# 7. Start supervisor (nginx + php-fpm)
if [ $# -gt 0 ]; then
    exec "$@"
else
    exec supervisord -c /etc/supervisor/supervisord.conf
fi
