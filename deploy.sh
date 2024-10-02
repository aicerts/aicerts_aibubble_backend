#!/bin/bash

# Navigate to the project directory
# cd /aicerts_aibubble_backend

# Step 1: Set proper permissions for the Laravel storage and cache directories
echo "Setting permissions for storage and bootstrap/cache..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Step 2: Install dependencies via Composer
echo "Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Step 3: Generate Laravel application key
echo "Generating Laravel application key..."
php artisan key:generate

# Step 4: Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Step 5: Clear and cache configurations
echo "Clearing and caching config..."
php artisan config:clear
php artisan config:cache
php artisan route:cache

# Step 6: Restart or start the Laravel application using PM2
echo "Restarting PM2 process..."
pm2 delete laravel-app
pm2 start php --name "laravel-app" -- artisan serve --host 10.2.3.50 --port 7034

# Step 7: Save PM2 process
echo "Saving PM2 process list..."
pm2 save

echo "Deployment complete!"
