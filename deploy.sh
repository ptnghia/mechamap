#!/bin/bash

# Script triển khai MechaMap lên hosting thông qua Git Version Control
# Sử dụng: ./deploy.sh

# Màu sắc cho output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Hiển thị thông báo
echo -e "${GREEN}=== Bắt đầu triển khai MechaMap ===${NC}"

# 1. Kiểm tra và commit các thay đổi
echo -e "${YELLOW}Kiểm tra trạng thái Git...${NC}"
git status

read -p "Bạn có muốn commit các thay đổi không? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]
then
    read -p "Nhập commit message: " commit_message
    git add .
    git commit -m "$commit_message"
    echo -e "${GREEN}Đã commit các thay đổi.${NC}"
fi

# 2. Đẩy code lên GitHub
echo -e "${YELLOW}Đẩy code lên GitHub...${NC}"
git push origin master
if [ $? -eq 0 ]; then
    echo -e "${GREEN}Đã đẩy code lên GitHub thành công.${NC}"
else
    echo -e "${RED}Lỗi khi đẩy code lên GitHub.${NC}"
    exit 1
fi

# 3. Hiển thị hướng dẫn triển khai trên cPanel
echo -e "${GREEN}=== Hướng dẫn triển khai trên cPanel ===${NC}"
echo -e "${YELLOW}1. Đăng nhập vào cPanel của bạn${NC}"
echo -e "${YELLOW}2. Tìm và mở 'Git™ Version Control'${NC}"
echo -e "${YELLOW}3. Tìm repository 'mechamap' và nhấp vào 'Manage'${NC}"
echo -e "${YELLOW}4. Nhấp vào 'Pull or Deploy' và chọn 'Update from Remote'${NC}"
echo -e "${YELLOW}5. Sau khi pull thành công, chạy các lệnh sau trên hosting:${NC}"
echo -e "${GREEN}   cd public_html/mechamap${NC}"
echo -e "${GREEN}   /usr/local/bin/php8.3 /path/to/composer.phar install --no-dev --optimize-autoloader${NC}"
echo -e "${GREEN}   php artisan optimize:clear${NC}"
echo -e "${GREEN}   php artisan optimize${NC}"
echo -e "${GREEN}   php artisan config:cache${NC}"
echo -e "${GREEN}   php artisan route:cache${NC}"
echo -e "${GREEN}   php artisan view:cache${NC}"
echo -e "${GREEN}   php artisan storage:link${NC}"

echo -e "${GREEN}=== Triển khai hoàn tất ===${NC}"
