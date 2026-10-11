#!/bin/bash
set -e

echo "🚀 Bắt đầu triển khai phiên bản mới lên VPS Production..."

# 1. Bật chế độ bảo trì tạm thời
php artisan down --render="errors::503" --secret="vpp-deploy-bypass-token" || true

# 2. Cập nhật mã nguồn mới nhất từ git
echo "📦 Đang kéo mã nguồn mới từ Git..."
git pull origin master

# 3. Cài đặt và tối ưu Composer dependencies
echo "🐘 Đang tối ưu Composer packages..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# 4. Chạy migration cơ sở dữ liệu an toàn
echo "🗄️ Đang kiểm tra và chạy database migrations..."
php artisan migrate --force

# 5. Xóa và tái tạo toàn bộ cache hiệu năng Laravel
echo "⚡ Đang tạo cache tối ưu (Config, Routes, Views)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Khởi động lại Queue Workers trên Supervisor
echo "🔄 Đang khởi động lại Queue Workers..."
php artisan queue:restart
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart vpp-worker:* || true
fi

# 7. Tắt chế độ bảo trì
php artisan up

echo "✅ Triển khai thành công! Hệ thống VPP đã sẵn sàng phục vụ."
