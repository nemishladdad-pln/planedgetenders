#!/usr/bin/env bash
# Simple installer for self-hosting Planedge gateway on a Linux server.
# Usage: sudo ./install_service.sh /path/to/backend-migration

set -e

APP_DIR=${1:-/opt/planedge-gateway}
SERVICE_USER=${SERVICE_USER:-planedge}
NODE_BIN=${NODE_BIN:-/usr/bin/node}

echo "Installing Planedge gateway to ${APP_DIR}"

# Create user if not exists
if ! id -u "$SERVICE_USER" >/dev/null 2>&1; then
  useradd --system --no-create-home --shell /usr/sbin/nologin "$SERVICE_USER"
fi

mkdir -p "${APP_DIR}"
cp -r . "${APP_DIR}"

cd "${APP_DIR}"

# Ensure node & npm present (leave to admin if not)
if ! command -v node >/dev/null 2>&1; then
  echo "Node.js not found. Please install Node.js 18+ and rerun."
  exit 1
fi

# install deps
npm ci --production

# create data dirs
mkdir -p data/uploads
chown -R "${SERVICE_USER}":"${SERVICE_USER}" data

# create systemd unit
UNIT_FILE="/etc/systemd/system/planedge-gateway.service"
cat > "$UNIT_FILE" <<EOF
[Unit]
Description=Planedge Gateway
After=network.target

[Service]
Type=simple
User=${SERVICE_USER}
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/node ${APP_DIR}/dist/index.js
Restart=on-failure
Environment=NODE_ENV=production
EnvironmentFile=${APP_DIR}/.env

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable planedge-gateway
systemctl restart planedge-gateway

echo "Installed and started planedge-gateway. Check status: sudo systemctl status planedge-gateway"
