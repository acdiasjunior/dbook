#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Generate local config for CakePHP (if applicable)
if [ ! -f config/app_local.php ]; then
    echo "Generating local CakePHP configuration..."
    cp config/app_local.example.php config/app_local.php
    NEW_SALT=$(php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;")
    sed -i "s/__SALT__/$NEW_SALT/" config/app_local.php
fi

# Install Composer dependencies
echo "Installing Composer dependencies..."
composer install --prefer-dist --optimize-autoloader

echo "Waiting for MySQL to be ready..."

# Retry until MySQL is ready
while ! php -r "
    try {
        \$dbh = new PDO('mysql:host=${DB_HOST};port=${DB_PORT};', '${DB_USER}', '${DB_PASS}');
        exit(0);
    } catch (PDOException \$e) {
        exit(1);
    }
"; do
    echo "MySQL is not ready. Retrying in 2 seconds..."
    sleep 2
done

echo "MySQL is ready!"

# Run migrations
echo "Running migrations..."
bin/cake migrations migrate

# Run seeders
echo "Running seeders..."
bin/cake migrations seed

# Import Books from API
bin/cake import_books

# Start PHP-FPM in the foreground
echo "Starting PHP-FPM..."
exec php-fpm
