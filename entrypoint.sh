#!/bin/bash

# Generate keys if they do not exist
if [ ! -f key.pem ]; then
    openssl genrsa -out key.pem 2048
fi

cp .env.example .env
cp .env.example.testing .env.testing

# Install composer dependencies
composer install

# Generate application keys
exec php artisan key:generate
exec php artisan key:generate --env=testing

# Run migrations and seed database
exec php artisan migrate:refresh --seed

# Generate swagger documentation

exec app php artisan l5-swagger:generate

# Start the Laravel server
exec php artisan serve --host=0.0.0.0 --port=8000
