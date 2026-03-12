#!/bin/bash

# Argumen dari Laravel Controller
PROJECT_PATH=$1
DB_NAME=$2
DB_USER=$3
DB_PASS=$4
FULL_DOMAIN=$5       # Contoh: budi.prodi.ac.id
PROJECT_NAME=$6
FLASK_APP_FILE=$7    # Contoh: app.py
FLASK_INSTANCE=$8    # Contoh: app
PYTHON_VERSION=$9

# Ambil kata pertama untuk identitas service (budi.prodi.ac.id -> budi)
SUB_NAME=$(echo $FULL_DOMAIN | cut -d'.' -f1)
SERVICE_NAME="flask_${SUB_NAME}"
SERVICE_FILE="/etc/systemd/system/${SERVICE_NAME}.service"
LOG_FILE="$PROJECT_PATH/install_log.txt"

# Mendefinisikan Path Biner Python berdasarkan versi
PYTHON_BIN="/usr/bin/python${PYTHON_VERSION}"

# Bersihkan nama file dari .py untuk parameter Gunicorn (misal: app.py -> app)
# Gunicorn butuh format 'modul:variabel'
APP_MODULE=$(echo $FLASK_APP_FILE | sed 's/\.py$//')

echo "--- DEPLOYMENT FLASK DIMULAI: $(date) ---" > "$LOG_FILE"
echo "Target Runtime: Python $PYTHON_VERSION" >> "$LOG_FILE"

# 1. SETUP VIRTUAL ENVIRONMENT & DEPENDENCIES
echo "[1/4] Menyiapkan Virtual Environment menggunakan $PYTHON_BIN..." >> "$LOG_FILE"
cd "$PROJECT_PATH"
$PYTHON_BIN -m venv venv >> "$LOG_FILE" 2>&1

if [ $? -ne 0 ]; then
    echo "❌ ERROR: Gagal membuat venv dengan Python $PYTHON_VERSION. Pastikan python${PYTHON_VERSION}-venv terinstall di server." >> "$LOG_FILE"
    exit 1
fi

# Cukup upgrade pip saja, biarkan setuptools menggunakan versi bawaan venv
echo "Mengupdate pip..." >> "$LOG_FILE"
./venv/bin/pip install --upgrade pip >> "$LOG_FILE" 2>&1

./venv/bin/pip install gunicorn pymysql cryptography >> "$LOG_FILE" 2>&1

echo "----------------------------------------------------" >> "$LOG_FILE"
if [ -f "requirements.txt" ]; then
    echo "Menginstal dependensi dari requirements.txt..." >> "$LOG_FILE"
    ./venv/bin/pip install --no-cache-dir -r requirements.txt >> "$LOG_FILE" 2>&1
    
    if [ $? -ne 0 ]; then
        echo "❌ ERROR: Instalasi requirements.txt gagal!" >> "$LOG_FILE"
        exit 1
    fi
else
    echo "⚠️ requirements.txt tidak ditemukan, menginstal Flask default..." >> "$LOG_FILE"
    ./venv/bin/pip install flask >> "$LOG_FILE" 2>&1
fi
echo "----------------------------------------------------" >> "$LOG_FILE"

# 2. PERMISSION
echo "[2/4] Mengatur izin direktori..." >> "$LOG_FILE"
sudo /usr/bin/chown -R www-data:www-data "$PROJECT_PATH"
sudo /usr/bin/chmod -R 755 "$PROJECT_PATH"

# 3. SYSTEMD SERVICE GENERATION
echo "[3/4] Konfigurasi Systemd Service: ${SERVICE_NAME}" >> "$LOG_FILE"
sudo /usr/bin/bash -c "cat > $SERVICE_FILE" <<EOF
[Unit]
Description=Gunicorn instance for $PROJECT_NAME
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=$PROJECT_PATH
# Menambahkan HOME agar library AI (Deepface/Keras) menyimpan model di folder proyek
Environment="HOME=$PROJECT_PATH"
Environment="PATH=$PROJECT_PATH/venv/bin"
# Format Gunicorn: modul:instansi (misal app:app)
ExecStart=$PROJECT_PATH/venv/bin/gunicorn --workers 3 --bind unix:$PROJECT_PATH/app.sock $APP_MODULE:$FLASK_INSTANCE

[Install]
WantedBy=multi-user.target
EOF

sudo /usr/bin/systemctl daemon-reload
sudo /usr/bin/systemctl enable $SERVICE_NAME
sudo /usr/bin/systemctl restart $SERVICE_NAME >> "$LOG_FILE" 2>&1

# 4. NGINX CONFIGURATION
echo "[4/4] Membuat konfigurasi Nginx Reverse Proxy..." >> "$LOG_FILE"
NGINX_CONF="/etc/nginx/sites-available/$FULL_DOMAIN.conf"

sudo /usr/bin/bash -c "cat > $NGINX_CONF" <<EOF
server {
    listen 80;
    server_name $FULL_DOMAIN;

    access_log /var/log/nginx/$FULL_DOMAIN.access.log;
    error_log /var/log/nginx/$FULL_DOMAIN.error.log;

    location / {
        include proxy_params;
        proxy_pass http://unix:$PROJECT_PATH/app.sock;
    }

    location ~ \.(sql|zip|log|env|git|py|sh)$ {
        deny all;
    }
}
EOF

sudo /usr/bin/ln -sf "$NGINX_CONF" "/etc/nginx/sites-enabled/$FULL_DOMAIN.conf"
sudo /usr/bin/systemctl reload nginx

echo "--- DEPLOYMENT SELESAI: $(date) ---" >> "$LOG_FILE"
