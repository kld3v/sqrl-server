#!/bin/bash
php artisan route:cache
php artisan config:cache
php artisan view:cache
php artisan event:cache
php artisan schedule:work
# Ensure that the newly created cache files can all be accessed properly
chown -R webapp:webapp storage
chmod -R 776 storage