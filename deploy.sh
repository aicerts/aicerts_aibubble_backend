#!/bin/bash

# Navigate to the project directory
cd /home/azureadmin/aicerts_aibubble_backend || exit

# Load environment variables from .env file if it exists
if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
fi

# Set ownership to the current user and www-data (used by the web server)
echo "Setting proper permissions for storage and cache directories..."
sudo chown -R $(whoami):www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Create necessary directories and files if they don't exist
echo "Ensuring the log file and cache directories exist..."
mkdir -p storage/logs bootstrap/cache
touch storage/logs/laravel.log

# Install composer dependencies
echo "Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Clear and cache config
echo "Clearing and caching Laravel configuration..."
php artisan config:clear
php artisan config:cache

# Generate application key if not present
echo "Generating application key..."
php artisan key:generate

# Delete the permission migration
echo "Deleting the permission migration..."
rm -f database/migrations/2022_01_12_173356_create_permission_tables.php

# Publish the Spatie permission configuration
echo "Publishing Spatie permission configuration..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"


# Run migrations and seed the database
echo "Running migrations and seeding database..."
php artisan migrate --force
php artisan db:seed --force

# Start the Laravel application using PM2
echo "Starting Laravel app with PM2..."
pm2 start php --name "laravel-app" -- artisan serve --host $PM2_HOST  --port $PM2_PORT

# Set up a cron job to run the Laravel scheduler every 5 minutes
echo "Setting up cron job for Laravel scheduler..."
(crontab -l; echo "*/5 * * * * php /home/azureadmin/aicerts_aibubble_backend/artisan schedule:run >> /dev/null 2>&1") | crontab -