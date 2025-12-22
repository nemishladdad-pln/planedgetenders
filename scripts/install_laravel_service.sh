#!/usr/bin/env bash
# Usage: sudo ./install_laravel_service.sh /path/to/project
set -e
APP_DIR=${1:-/var/www/planedge}
SERVICE_USER=${SERVICE_USER:-www-data}

echo "Setting up Laravel app at ${APP_DIR}"

if [ ! -d "$APP_DIR" ]; then
  echo "Please copy project to ${APP_DIR} and rerun"
  exit 1
fi

cd "$APP_DIR"

# composer install (assumes composer installed)
if ! command -v composer >/dev/null 2>&1; then
  echo "composer not found; please install composer and rerun"
  exit 1
fi

composer install --no-dev --optimize-autoloader

# set permissions for storage & bootstrap
chown -R ${SERVICE_USER}:${SERVICE_USER} storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

# create storage link if using public storage
php artisan storage:link || true

# run migrations & seed permissions
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\PermissionRoleUserSeeder --force || true

# create a simple systemd unit to run artisan serve (quick-start; for production use php-fpm+nginx)
UNIT_FILE="/etc/systemd/system/planedge-artisan.service"
cat > "$UNIT_FILE" <<EOF
[Unit]
Description=Planedge Laravel Artisan Serve
After=network.target

[Service]
Type=simple
User=${SERVICE_USER}
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php ${APP_DIR}/artisan serve --host=0.0.0.0 --port=8000
Restart=on-failure
Environment=APP_ENV=production
EOF

systemctl daemon-reload
systemctl enable planedge-artisan
systemctl restart planedge-artisan

echo "Service installed. For production, configure php-fpm and nginx and disable artisan serve."
