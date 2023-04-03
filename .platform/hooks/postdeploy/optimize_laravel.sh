#!/bin/bash

# Optimizing configuration loading, route loading and view loading
# https://laravel.com/docs/8.x/deployment#optimization

php artisan config:clear
php artisan config:cache

php artisan route:clear
php artisan route:cache

php artisan view:clear
php artisan view:cache
