#!/bin/bash

# Colors and styles
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
BOLD='\033[1m'
DIM='\033[2m'
NC='\033[0m'

# Unicode symbols
CHECK="✓"
CROSS="✗"
ARROW="→"
DOT="•"
ROCKET="🚀"
SEARCH="🔍"
WRENCH="🔧"
SHIELD="🛡️"
BOX="📦"
WARNING="⚠️"

clear

echo ""
echo -e "${BOLD}${CYAN}╔════════════════════════════════════════════════════════╗${NC}"
echo -e "${BOLD}${CYAN}║                                                        ║${NC}"
echo -e "${BOLD}${CYAN}║       ${ROCKET} ${BOLD}${MAGENTA} LMS DEPLOYMENT VERIFICATION${CYAN}             ║${NC}"
echo -e "${BOLD}${CYAN}║                                                        ║${NC}"
echo -e "${BOLD}${CYAN}╚════════════════════════════════════════════════════════╝${NC}"
echo ""

# Function to print section header
print_section() {
    echo ""
    echo -e "${BOLD}${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BOLD}${CYAN}$1${NC}"
    echo -e "${BOLD}${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

# Function to print test item
print_test() {
    echo ""
    echo -e "${DIM}${ARROW}${NC} ${BOLD}$1${NC}"
}

# Function to check status
check_status() {
    if [ $? -eq 0 ]; then
        echo -e "  ${GREEN}${CHECK} PASS${NC}"
        return 0
    else
        echo -e "  ${RED}${CROSS} FAIL${NC}"
        return 1
    fi
}

# Function to print info
print_info() {
    echo -e "  ${DIM}${DOT}${NC} $1"
}

# 1. Check Docker containers
print_section "${BOX} DOCKER CONTAINERS"
print_test "Checking container status..."
docker-compose ps | grep -q "Up"
check_status

echo ""
print_info "Container details:"
docker-compose ps | sed 's/^/    /'
echo ""

# 2. Check FFmpeg
print_section "${WRENCH} FFMPEG INSTALLATION"
print_test "Verifying FFmpeg availability..."
docker-compose exec -T app ffmpeg -version > /dev/null 2>&1
check_status

if [ $? -eq 0 ]; then
    FFMPEG_VERSION=$(docker-compose exec -T app ffmpeg -version | head -1)
    print_info "Version: ${FFMPEG_VERSION}"
fi
echo ""

# 3. Check MySQL connection
print_section "${BOX} DATABASE CONNECTION"
print_test "Testing MySQL connectivity..."
docker-compose exec -T app php artisan migrate:status > /dev/null 2>&1
check_status

if [ $? -eq 0 ]; then
    print_info "Migration status:"
    docker-compose exec -T app php artisan migrate:status | sed 's/^/    /'
fi
echo ""

# 4. Check HTTPS endpoint
print_section "${SHIELD} HTTPS ENDPOINT"
print_test "Testing HTTPS availability..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://lms.domain.vn)
echo "$HTTP_CODE" | grep -qE "200|302"
check_status

print_info "Response code: ${HTTP_CODE}"
curl -I https://lms.domain.vn 2>/dev/null | head -3 | sed 's/^/    /'
echo ""

# 5. Check storage permissions
print_section "${WRENCH} STORAGE PERMISSIONS"
print_test "Checking write permissions..."
docker-compose exec -T app test -w /COURCE/storage
check_status

print_info "Storage directory:"
docker-compose exec -T app ls -la storage/ | head -5 | sed 's/^/    /'
echo ""

# 6. Check Nginx upload limit
print_section "${WRENCH} NGINX CONFIGURATION"
print_test "Verifying upload limits..."
docker-compose exec -T nginx cat /etc/nginx/conf.d/default.conf | grep -q "client_max_body_size 256M"
check_status

UPLOAD_LIMIT=$(docker-compose exec -T nginx cat /etc/nginx/conf.d/default.conf | grep client_max_body_size | head -1 | awk '{print $2}' | tr -d ';')
print_info "Upload limit: ${UPLOAD_LIMIT}"
echo ""

# 7. Check HLS secret key consistency
print_section "${SHIELD} HLS SECURITY"
print_test "Validating secret key configuration..."
ENV_SECRET=$(grep STATIC_SOURCE_SECRET_KEY .env | cut -d'=' -f2)
NGINX_SECRET=$(docker-compose exec -T nginx cat /etc/nginx/conf.d/default.conf | grep 'set \$secure_link_secret' | sed -n 's/.*"\(.*\)".*/\1/p')

if [ -z "$ENV_SECRET" ] || [ -z "$NGINX_SECRET" ]; then
    echo -e "  ${RED}${CROSS} FAIL - Secret not found${NC}"
    print_info "${YELLOW}Check STATIC_SOURCE_SECRET_KEY in .env and secure_link_secret in nginx config${NC}"
elif [ "$ENV_SECRET" = "$NGINX_SECRET" ]; then
    echo -e "  ${GREEN}${CHECK} PASS - Secrets match${NC}"
    print_info "Secret key: ${ENV_SECRET:0:20}..."
else
    echo -e "  ${RED}${CROSS} FAIL - Secrets do not match${NC}"
    print_info ".env: ${ENV_SECRET}"
    print_info "Nginx: ${NGINX_SECRET}"
fi
echo ""

# 8. Check PHP configuration
print_section "${WRENCH} PHP CONFIGURATION"
print_test "Verifying PHP upload limits..."
UPLOAD_MAX=$(docker-compose exec -T app php -i | grep upload_max_filesize | grep -o '256M')
POST_MAX=$(docker-compose exec -T app php -i | grep post_max_size | grep -o '256M')

if [ "$UPLOAD_MAX" = "256M" ] && [ "$POST_MAX" = "256M" ]; then
    check_status
    print_info "upload_max_filesize: ${UPLOAD_MAX}"
    print_info "post_max_size: ${POST_MAX}"
else
    echo -e "  ${RED}${CROSS} FAIL${NC}"
    print_info "upload_max_filesize: ${UPLOAD_MAX:-'not 256M'}"
    print_info "post_max_size: ${POST_MAX:-'not 256M'}"
fi
echo ""

# 9. Check Laravel cache
print_section "${ROCKET} LARAVEL OPTIMIZATION"
print_test "Checking configuration cache..."
docker-compose exec -T app test -f bootstrap/cache/config.php
check_status

if [ $? -eq 0 ]; then
    print_info "Config cache: ${GREEN}Enabled${NC}"
else
    print_info "${YELLOW}Config not cached - run: php artisan config:cache${NC}"
fi
echo ""

# 10. Check environment
print_section "${WRENCH} ENVIRONMENT SETTINGS"
print_test "Validating production configuration..."
grep -q "APP_ENV=production" .env && grep -q "APP_DEBUG=false" .env
check_status

APP_ENV=$(grep "APP_ENV" .env | cut -d'=' -f2)
APP_DEBUG=$(grep "APP_DEBUG" .env | cut -d'=' -f2)
APP_URL=$(grep "APP_URL" .env | cut -d'=' -f2)

print_info "Environment: ${APP_ENV}"
print_info "Debug mode: ${APP_DEBUG}"
print_info "App URL: ${APP_URL}"
echo ""

# 11. Check symlink
print_section "${WRENCH} STORAGE SYMLINK"
print_test "Verifying public storage link..."
docker-compose exec -T app test -L /COURCE/public/storage
check_status
echo ""

# 12. Check logs
print_section "${SEARCH} APPLICATION LOGS"
print_info "Recent Laravel logs:"
LOGS=$(docker-compose exec -T app tail -5 storage/logs/laravel.log 2>/dev/null)
if [ -z "$LOGS" ]; then
    print_info "${DIM}No logs available${NC}"
else
    echo "$LOGS" | sed 's/^/    /'
fi
echo ""

# 13. Check disk space
print_section "${BOX} SYSTEM RESOURCES"
print_info "Disk space usage:"
df -h | grep -E "Filesystem|/$" | sed 's/^/    /'
echo ""

# Summary
echo ""
echo -e "${BOLD}${CYAN}╔════════════════════════════════════════════════════════╗${NC}"
echo -e "${BOLD}${CYAN}║                                                        ║${NC}"
echo -e "${BOLD}${CYAN}║           ${GREEN}${CHECK}${CYAN} VERIFICATION COMPLETE ${CHECK}${CYAN}                  ║${NC}"
echo -e "${BOLD}${CYAN}║                                                        ║${NC}"
echo -e "${BOLD}${CYAN}╚════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${BOLD}${YELLOW}${ARROW} NEXT STEPS:${NC}"
echo -e "  ${DIM}${DOT}${NC} Test API endpoints manually"
echo -e "  ${DIM}${DOT}${NC} Test file upload functionality"
echo -e "  ${DIM}${DOT}${NC} Test HLS video playback"
echo -e "  ${DIM}${DOT}${NC} Monitor logs for any errors"
echo ""

echo -e "${BOLD}${CYAN}${ARROW} USEFUL COMMANDS:${NC}"
echo -e "  ${DIM}${DOT}${NC} View logs:     ${BOLD}docker-compose logs -f${NC}"
echo -e "  ${DIM}${DOT}${NC} Restart:       ${BOLD}docker-compose restart${NC}"
echo -e "  ${DIM}${DOT}${NC} Clear cache:   ${BOLD}docker-compose exec app php artisan cache:clear${NC}"
echo -e "  ${DIM}${DOT}${NC} Config cache:  ${BOLD}docker-compose exec app php artisan config:cache${NC}"
echo ""
