#!/bin/bash

# Generate keys if they do not exist
if [ ! -f key.pem ]; then
    openssl genrsa -out key.pem 2048
fi

# Install composer dependencies
composer install

# Generate application keys
php artisan key:generate
php artisan key:generate --env=testing

# Run migrations and seed database
php artisan migrate:refresh --seed

# Start the Laravel server
exec php artisan serve --host=0.0.0.0 --port=8000
