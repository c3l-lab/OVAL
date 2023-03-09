# oval
OVAL Video Interactive Tool

This solution is built on Laravel.. Your documentation for all things Lavarel starts here:
https://laravel.com/


# My Notes:
Got lots of errors running composer to install packages related to Google API Services. Seems to need an increase in the default timeout.
Phrase that seemed to work is:
```
COMPOSER_PROCESS_TIMEOUT=2000 composer install
```

# Setting up Ubuntu..
Add driver for php and mysql
``
sudo apt-get install php7.4-mysql

``


## Setting an APP_KEY..
I had lots of troubles with this.. I needed to copy the .env.example to .env - then set a default key that matched the right encyption value set in `config/app.php` line 108 'cipher' setting i.e
APP_KEY = base64:MsUJo+qAhIVGPx52r1mbxCYn5YbWtCx8FQ7pTaHEvRo=base64:Ign7MpdXw4FMI5ai7SXXiU2vbraqhyEK1NniKPNJKGY=

Then I could run:
```
php artisan config:clear
php artisan config:cache
php artisan key:generate
```
Still dont understand how .env work. Can determine current .eve with:
```
php artisan env
```
Just dont know to set the default env..? Maybe this?
```
php artisan migrate --env=local
```


# Install database for first time.
1. Manually create db in MySQL Workbench (there must be a blank database/schema to connect to)
2. Create migrate table
```
php artisan migrate:install
```
3. run migrations
```
php artisan migrate
```

# Debugging

Laravel Logs can be found in `/storage/logs/laravel.log`


# Run App Locally
```
php artisan serve
```


# Setting up a Laravel environment
https://teamhelium.medium.com/deploying-laravel-to-elastic-beanstalk-in-2021-5a3d9cc6696d