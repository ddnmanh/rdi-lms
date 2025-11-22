#!/bin/bash

echo "=========================================="
echo "LMS Laravel Deployment Script"
echo "=========================================="

# Stop existing containers
echo "Stopping existing containers..."
docker-compose down

# Copy environment file
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
    echo "⚠️  Please edit .env file with your actual configuration!"
    echo "   - DB_DATABASE"
    echo "   - DB_USERNAME"
    echo "   - DB_PASSWORD"
    echo "   - JWT_SECRET"
    echo ""
    echo "⚠️  Make sure MySQL is configured on Ubuntu host:"
    echo "   - bind-address = 0.0.0.0 in /etc/mysql/mysql.conf.d/mysqld.cnf"
    echo "   - User has access from '%'"
    echo "   - Database exists"
    exit 1
fi

# Build and start containers
echo "Building Docker images..."
docker-compose build --no-cache

echo "Starting containers..."
docker-compose up -d

# Wait for containers to be ready
echo "Waiting for containers to be ready..."
sleep 5

# Test MySQL connection
echo "Testing MySQL connection..."
docker-compose exec -T app php -r "try { new PDO('mysql:host=host.docker.internal;dbname=${DB_DATABASE:-lms_database}', '${DB_USERNAME:-lms_user}', '${DB_PASSWORD}'); echo 'MySQL connection: OK\n'; } catch(Exception \$e) { echo 'MySQL connection FAILED: ' . \$e->getMessage() . '\n'; exit(1); }" || {
    echo "❌ Cannot connect to MySQL. Please check:"
    echo "   - MySQL is running: sudo systemctl status mysql"
    echo "   - Database and user exist"
    echo "   - bind-address = 0.0.0.0"
    echo "   - Credentials in .env file"
    exit 1
}

# Install dependencies and setup Laravel
echo "Installing dependencies..."
docker-compose exec -T app composer install --optimize-autoloader --no-dev

echo "Generating application key..."
docker-compose exec -T app php artisan key:generate

echo "Running migrations..."
docker-compose exec -T app php artisan migrate --force

echo "Clearing cache..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

echo "Setting permissions..."
docker-compose exec -T app chown -R www-data:www-data /home/ducmanh/rdi/storage
docker-compose exec -T app chmod -R 775 /home/ducmanh/rdi/storage
docker-compose exec -T app chmod -R 775 /home/ducmanh/rdi/bootstrap/cache

echo "=========================================="
echo "✅ Deployment completed!"
echo "=========================================="
echo "Your application should be available at: http://rdi.sotech.io.vn"
echo ""
echo "Useful commands:"
echo "  - View logs: docker-compose logs -f"
echo "  - Stop: docker-compose stop"
echo "  - Start: docker-compose start"
echo "  - Restart: docker-compose restart"
echo "=========================================="
