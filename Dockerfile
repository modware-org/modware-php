FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    git \
    unzip \
    && docker-php-ext-install pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "LogLevel debug" >> /etc/apache2/apache2.conf

# Configure PHP
RUN echo "display_errors = On" >> /usr/local/etc/php/php.ini-development \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini-development \
    && echo "log_errors = On" >> /usr/local/etc/php/php.ini-development \
    && echo "error_log = /var/log/php_errors.log" >> /usr/local/etc/php/php.ini-development \
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Install dependencies
RUN composer install --no-interaction --no-progress --prefer-dist

# Create required directories and set permissions
RUN mkdir -p /var/www/html/data /var/www/html/logs /var/log \
    && touch /var/log/php_errors.log \
    && chown -R www-data:www-data /var/www/html /var/log/php_errors.log \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/data /var/www/html/logs /var/log/php_errors.log

# Configure Apache virtual host
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf
