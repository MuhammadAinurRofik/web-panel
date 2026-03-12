#!/bin/bash

# Argumen dari Laravel Controller
PROJECT_PATH=$1
DOMAIN=$2
PHP_VER=$3

PHP_BIN="/usr/bin/php${PHP_VER}"
LOG_FILE="$PROJECT_PATH/install_log.txt"

# Inisialisasi file log (Gunakan > untuk overwrite log lama)
echo "--- DEPLOYMENT LARAVEL DIMULAI: $(date) ---" > "$LOG_FILE"
echo "Target Runtime: PHP $PHP_VER" >> "$LOG_FILE"

# 1. COMPOSER INSTALL
echo "[1/4] Menjalankan Composer Install (Library PHP)..." >> "$LOG_FILE"
cd "$PROJECT_PATH"

# Menjalankan composer secara non-interaktif
export COMPOSER_ALLOW_SUPERUSER=1
$PHP_BIN /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction >> "$LOG_FILE" 2>&1

if [ $? -ne 0 ]; then
    echo "❌ ERROR: Composer install gagal!" >> "$LOG_FILE"
    exit 1
fi

# 2. NPM INSTALL & BUILD
if [ -f "package.json" ]; then
    echo "[2/4] Menyiapkan Node Modules & Vite Build..." >> "$LOG_FILE"
    npm install --no-audit --no-fund >> "$LOG_FILE" 2>&1
    npm run build >> "$LOG_FILE" 2>&1
    
    if [ $? -ne 0 ]; then
        echo "⚠️ WARNING: Build aset gagal, pastikan konfigurasi Vite benar." >> "$LOG_FILE"
    fi
else
    echo "[SKIP] package.json tidak ditemukan, melewati build aset." >> "$LOG_FILE"
fi

# 3. PERMISSION & STORAGE (Sangat Krusial)
echo "[3/4] Mengatur izin direktori dan storage link..." >> "$LOG_FILE"

# FIX ERROR CACHE: Paksa driver cache ke 'file' untuk sementara agar migrasi lancar
# Ini mencegah Laravel mencari tabel 'cache' di database yang belum dimigrasi
sed -i 's/CACHE_STORE=database/CACHE_STORE=file/g' .env 2>/dev/null
sed -i 's/CACHE_DRIVER=database/CACHE_DRIVER=file/g' .env 2>/dev/null

# Pastikan kepemilikan ke www-data agar PHP-FPM bisa menulis file
sudo /usr/bin/chown -R www-data:www-data "$PROJECT_PATH"

# Berikan izin tulis folder storage & cache secara rekursif
sudo /usr/bin/find "$PROJECT_PATH/storage" -type d -exec chmod 775 {} \;
sudo /usr/bin/find "$PROJECT_PATH/storage" -type f -exec chmod 664 {} \;
sudo /usr/bin/chmod -R 775 "$PROJECT_PATH/bootstrap/cache"

# --- TAMBAHAN: KONFIGURASI NGINX ---
echo "[3.5/4] Membuat konfigurasi Nginx..." >> "$LOG_FILE"
PUBLIC_PATH="$PROJECT_PATH/public"
NGINX_CONF="/etc/nginx/sites-available/$DOMAIN.conf"

sudo /usr/bin/bash -c "cat > $NGINX_CONF" <<EOF
server {
    listen 80;
    server_name $DOMAIN;
    root $PUBLIC_PATH;

    index index.php index.html;

    access_log /var/log/nginx/$DOMAIN.access.log;
    error_log /var/log/nginx/$DOMAIN.error.log;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php${PHP_VER}-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ \.(sql|zip|log|env|git)$ {
        deny all;
    }
}
EOF

# Aktifkan konfigurasi (Symlink)
sudo /usr/bin/ln -sf "$NGINX_CONF" "/etc/nginx/sites-enabled/$DOMAIN.conf"
sudo /usr/bin/systemctl reload nginx >> "$LOG_FILE" 2>&1
echo "✅ Nginx berhasil dikonfigurasi." >> "$LOG_FILE"

# 4. DATABASE, KEY & CACHING
echo "[4/4] Finalisasi: Migrasi Database & Caching..." >> "$LOG_FILE"

# Jalankan semua artisan COMMAND sebagai www-data agar file yang terbuat bukan milik root
sudo -u www-data $PHP_BIN artisan key:generate --force >> "$LOG_FILE" 2>&1
sudo -u www-data $PHP_BIN artisan storage:link >> "$LOG_FILE" 2>&1

# Bersihkan cache sebelum migrasi untuk mematikan user 'forge' default
sudo -u www-data $PHP_BIN artisan config:clear >> "$LOG_FILE" 2>&1
sudo -u www-data $PHP_BIN artisan cache:clear >> "$LOG_FILE" 2>&1

# Jalankan migrasi
echo "Menjalankan migrasi database..." >> "$LOG_FILE"
sudo -u www-data $PHP_BIN artisan migrate --force >> "$LOG_FILE" 2>&1

# --- TAMBAHAN: JALANKAN SEEDER ---
echo "Menjalankan database seeder..." >> "$LOG_FILE"
# Kita gunakan --force karena di lingkungan production artisan db:seed sering meminta konfirmasi
sudo -u www-data $PHP_BIN artisan db:seed --force >> "$LOG_FILE" 2>&1

if [ $? -eq 0 ]; then
    echo "✅ Seeding berhasil." >> "$LOG_FILE"
else
    echo "⚠️ Seeding dilewati atau gagal (mungkin tidak ada class DatabaseSeeder)." >> "$LOG_FILE"
fi
# ---------------------------------

# Buat cache baru untuk performa produksi
sudo -u www-data $PHP_BIN artisan config:cache >> "$LOG_FILE" 2>&1
sudo -u www-data $PHP_BIN artisan route:cache >> "$LOG_FILE" 2>&1
sudo -u www-data $PHP_BIN artisan view:cache >> "$LOG_FILE" 2>&1

echo "" >> "$LOG_FILE"
echo "--- DEPLOYMENT SELESAI: $(date) ---" >> "$LOG_FILE"
echo "DEPLOYMENT SELESAI" >> "$LOG_FILE"