FROM php:8.0-fpm

# Chuyển hướng repository sang archive.debian.org
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i 's|security.debian.org|archive.debian.org/|g' /etc/apt/sources.list \
    && sed -i '/buster-updates/d' /etc/apt/sources.list

# Install system dependencies including FFmpeg
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    ffmpeg \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Configure PHP for large file uploads
RUN echo "upload_max_filesize = 256M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 10240M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 10240M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 1800" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 1800" >> /usr/local/etc/php/conf.d/uploads.ini

# Copy custom PHP-FPM configuration
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /COURCE

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /COURCE \
    && chmod -R 755 /COURCE/storage \
    && chmod -R 755 /COURCE/bootstrap/cache

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]
