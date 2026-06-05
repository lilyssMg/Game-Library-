#!/usr/bin/env bash
# deploy.sh — Game Library on DietPi / Pi Zero W 2
# Run as root: sudo bash deploy/deploy.sh
# Tested against DietPi v9 (Bookworm/Bullseye), ARMv8 32/64-bit

set -euo pipefail

REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"
APP_DIR="/var/www/game-library"
VHOST_CONF="/etc/apache2/sites-available/game-library.conf"
DB_NAME="game_library"
DB_USER="game_user"
DB_PASS="game_password"
PHP_MIN="8.0"

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
ok()   { echo -e "${GREEN}[OK]${NC}  $*"; }
warn() { echo -e "${YELLOW}[WARN]${NC} $*"; }
die()  { echo -e "${RED}[FAIL]${NC} $*"; exit 1; }
step() { echo -e "\n${YELLOW}==>${NC} $*"; }

# ── 0. Must run as root ───────────────────────────────────────
[[ $EUID -eq 0 ]] || die "Run as root: sudo bash $0"

# ── 1. Environment checks ─────────────────────────────────────
step "Checking environment"

# OS
if [[ -f /etc/os-release ]]; then
    . /etc/os-release
    ok "OS: $PRETTY_NAME"
else
    warn "Cannot read /etc/os-release"
fi

# Architecture
ARCH=$(uname -m)
ok "Architecture: $ARCH"

# Free RAM (MariaDB needs ~256MB to start reliably on Pi Zero W 2)
FREE_MB=$(awk '/MemAvailable/ { printf "%d", $2/1024 }' /proc/meminfo)
if [[ $FREE_MB -lt 150 ]]; then
    warn "Low free RAM: ${FREE_MB}MB. MariaDB may OOM. Consider enabling swap."
    warn "  dietpi-config → Performance Options → Swap"
else
    ok "Free RAM: ${FREE_MB}MB"
fi

# Swap
SWAP_MB=$(awk '/SwapTotal/ { printf "%d", $2/1024 }' /proc/meminfo)
if [[ $SWAP_MB -lt 100 ]]; then
    warn "No/small swap (${SWAP_MB}MB). On Pi Zero W 2 with 512MB RAM, add ≥512MB swap."
    warn "  dietpi-config → Performance Options → Swap"
else
    ok "Swap: ${SWAP_MB}MB"
fi

# ── 2. Install packages ───────────────────────────────────────
step "Checking required packages"

PKGS_NEEDED=()
command -v apache2    >/dev/null 2>&1 || PKGS_NEEDED+=(apache2)
command -v mysqld     >/dev/null 2>&1 || dpkg -s mariadb-server &>/dev/null || PKGS_NEEDED+=(mariadb-server)
command -v php        >/dev/null 2>&1 || PKGS_NEEDED+=(php)
dpkg -s php-mysql     &>/dev/null     || PKGS_NEEDED+=(php-mysql)
dpkg -s php8.4-mysql  &>/dev/null     || PKGS_NEEDED+=(php8.4-mysql)
dpkg -s php-fileinfo  &>/dev/null     || PKGS_NEEDED+=(php-fileinfo)
dpkg -s libapache2-mod-php &>/dev/null || PKGS_NEEDED+=(libapache2-mod-php)

if [[ ${#PKGS_NEEDED[@]} -gt 0 ]]; then
    step "Installing missing packages: ${PKGS_NEEDED[*]}"
    apt-get update -qq
    apt-get install -y "${PKGS_NEEDED[@]}" --no-install-recommends
    ok "Packages installed"
else
    ok "All packages already present — skipping apt-get"
fi

# ── 3. Check PHP version ──────────────────────────────────────
step "Checking PHP version"

PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
if php -r "exit(version_compare('$PHP_VER','$PHP_MIN','<') ? 1 : 0);"; then
    ok "PHP $PHP_VER"
else
    die "PHP $PHP_VER < required $PHP_MIN"
fi

# Check required extensions
for EXT in PDO pdo_mysql fileinfo; do
    if php -m | grep -q "^$EXT$"; then
        ok "PHP extension: $EXT"
    else
        die "PHP extension missing: $EXT. Install php-$EXT"
    fi
done

# ── 4. MariaDB tuning for 512MB RAM ──────────────────────────
step "Tuning MariaDB for low-memory Pi"

TUNE_CONF="/etc/mysql/mariadb.conf.d/99-pi-tune.cnf"
if [[ ! -f "$TUNE_CONF" ]]; then
    cat > "$TUNE_CONF" <<'MYCNF'
[mysqld]
# Pi Zero W 2 (512MB RAM) tuning
innodb_buffer_pool_size   = 48M
innodb_log_file_size      = 8M
innodb_flush_log_at_trx_commit = 2
max_connections           = 20
table_open_cache          = 64
query_cache_type          = 0
performance_schema        = OFF
MYCNF
    ok "Created $TUNE_CONF"
else
    ok "MariaDB tune config already present"
fi

# ── 5. Start services ─────────────────────────────────────────
step "Starting services"

systemctl enable --now mariadb
systemctl enable --now apache2

# Wait for MariaDB socket
TRIES=0
until mysqladmin ping --silent 2>/dev/null; do
    sleep 1
    TRIES=$((TRIES+1))
    [[ $TRIES -gt 20 ]] && die "MariaDB did not start within 20s"
done
ok "MariaDB running"
ok "Apache2 running"

# ── 6. Create database and user ───────────────────────────────
step "Setting up database"

mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL
ok "Database '$DB_NAME' and user '$DB_USER' ready"

# ── 7. Load schema (idempotent check) ─────────────────────────
step "Loading schema"

TABLES=$(mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
    -e "SHOW TABLES;" 2>/dev/null | grep -c . || true)

if [[ $TABLES -gt 0 ]]; then
    warn "Tables already exist ($TABLES found). Skipping schema load."
    warn "  To reset: mysql -u root -e \"DROP DATABASE $DB_NAME;\" then re-run."
else
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$REPO_DIR/database/schema.sql"
    ok "Schema loaded"
fi

# ── 8. Deploy app files ───────────────────────────────────────
step "Deploying app to $APP_DIR"

mkdir -p "$APP_DIR/uploads"

rsync -av --delete \
    --exclude='.git' \
    --exclude='deploy/' \
    --exclude='tests/' \
    --exclude='.claude/' \
    --exclude='.remember/' \
    --exclude='database/members.db' \
    "$REPO_DIR/" "$APP_DIR/"

mkdir -p "$APP_DIR/uploads"

chown -R www-data:www-data "$APP_DIR"
chmod -R 755 "$APP_DIR"
chmod -R 775 "$APP_DIR/uploads"

ok "Files deployed"

# ── 9. Apache virtual host ────────────────────────────────────
step "Configuring Apache"

cat > "$VHOST_CONF" <<VHOST
<VirtualHost *:80>
    DocumentRoot $APP_DIR
    DirectoryIndex index.php

    <Directory $APP_DIR>
        AllowOverride None
        Require all granted
        Options -Indexes
    </Directory>

    ErrorLog  \${APACHE_LOG_DIR}/game-library-error.log
    CustomLog \${APACHE_LOG_DIR}/game-library-access.log combined
</VirtualHost>
VHOST

a2ensite game-library.conf
a2dissite 000-default.conf 2>/dev/null || true
a2enmod php* 2>/dev/null || true

apache2ctl configtest && systemctl reload apache2
ok "Apache configured and reloaded"

# ── 10. PHP upload settings ───────────────────────────────────
step "Checking PHP upload limits"

PHP_INI=$(php --ini | awk '/Loaded Configuration File/ { print $NF }')
if [[ -f "$PHP_INI" ]]; then
    UPLOAD_MAX=$(php -r "echo ini_get('upload_max_filesize');")
    POST_MAX=$(php -r "echo ini_get('post_max_size');")
    ok "upload_max_filesize=$UPLOAD_MAX  post_max_size=$POST_MAX"
    warn "If image uploads fail, edit $PHP_INI:"
    warn "  upload_max_filesize = 10M"
    warn "  post_max_size = 10M"
fi

# ── 11. Connectivity check ────────────────────────────────────
step "Smoke test"

HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/ || echo "000")
if [[ "$HTTP_CODE" == "200" ]]; then
    ok "HTTP 200 from localhost"
else
    warn "Got HTTP $HTTP_CODE from localhost. Check: sudo journalctl -u apache2 -n 30"
fi

IP=$(hostname -I | awk '{print $1}')

echo ""
echo -e "${GREEN}Deploy complete.${NC}"
echo ""
echo "  Local URL:    http://localhost/"
echo "  Network URL:  http://$IP/"
echo ""
echo "  Logs:"
echo "    Apache errors:  sudo tail -f /var/log/apache2/game-library-error.log"
echo "    Apache access:  sudo tail -f /var/log/apache2/game-library-access.log"
echo ""
echo "  To re-deploy after git pull:"
echo "    sudo bash $REPO_DIR/deploy/deploy.sh"
