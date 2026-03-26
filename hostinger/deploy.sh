#!/bin/bash
# ============================================
# Deploy Script untuk Hostinger
# Jalankan via SSH Terminal Hostinger
# ============================================

echo "=========================================="
echo "  Orbit WhatsApp API - Hostinger Deploy"
echo "=========================================="

# ==========================================
# PART 1: LARAVEL
# ==========================================
APP_DIR="$HOME/orbit-app"

if [ ! -d "$APP_DIR" ]; then
    echo "ERROR: Directory $APP_DIR not found!"
    echo "Pastikan kamu sudah upload project ke $APP_DIR"
    exit 1
fi

cd "$APP_DIR"

echo ""
echo "[1/7] Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo ""
echo "[2/7] Setting file permissions..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

echo ""
echo "[3/7] Creating storage link..."
if [ -L "$HOME/public_html/storage" ]; then
    rm "$HOME/public_html/storage"
fi
ln -s "$APP_DIR/storage/app/public" "$HOME/public_html/storage"
echo "Storage link created."

echo ""
echo "[4/7] Running database migrations..."
php artisan migrate --force

echo ""
echo "[5/7] Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ""
echo "[6/7] Optimizing..."
php artisan optimize

# ==========================================
# PART 2: WHATSAPP BOT (Node.js)
# ==========================================
BOT_DIR="$HOME/whatsapp-bot"

echo ""
if [ -d "$BOT_DIR" ]; then
    echo "[7/7] Installing WhatsApp Bot dependencies..."
    cd "$BOT_DIR"
    npm install --production
    echo "WhatsApp Bot dependencies installed."
    echo ""
    echo "CATATAN: Setup Node.js App di hPanel:"
    echo "  1. Website → Node.js → Create Application"
    echo "  2. Node.js Version: 18.x atau 20.x"
    echo "  3. Application Root: whatsapp-bot"
    echo "  4. Application Startup File: app.js"
    echo "  5. Klik 'Create' lalu 'Restart'"
else
    echo "[7/7] WhatsApp Bot directory not found - skipping."
    echo "Upload folder whatsapp-bot/ ke $HOME/ jika ingin deploy bot."
fi

echo ""
echo "=========================================="
echo "  DEPLOY SELESAI!"
echo "=========================================="
echo ""
echo "Cek website kamu di browser."
echo ""
