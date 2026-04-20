FROM php:8.3-fpm

WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    vim \
    mariadb-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Laravel installer globally
RUN composer global require laravel/installer

# Add composer bin to PATH
ENV PATH="${PATH}:/root/.composer/vendor/bin"

# Create and configure temp directory
RUN mkdir -p /var/www/storage/temp && \
    chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www && \
    chmod -R 777 /var/www/storage/temp

# Configure PHP temp directory and suppress deprecation warnings
RUN echo "upload_tmp_dir=/var/www/storage/temp" >> /usr/local/etc/php/conf.d/php.ini && \
    echo "sys_temp_dir=/var/www/storage/temp" >> /usr/local/etc/php/conf.d/php.ini && \
    echo "error_reporting=8191" >> /usr/local/etc/php/conf.d/php.ini && \
    echo "display_errors=0" >> /usr/local/etc/php/conf.d/php.ini && \
    echo "log_errors=1" >> /usr/local/etc/php/conf.d/php.ini

EXPOSE 9000

CMD ["php-fpm"]
