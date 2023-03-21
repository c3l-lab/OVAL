# OVAL 2.0 - Interactive Video Platform

OVAL 2.0 is an interactive video platform designed to be integrated into a learning management system (LMS) via Learning Tools Interoperability (LTI). The web application is built with Laravel 10 and PHP 8.2. This README and upgrades from original Oval v1 (from fork [Argsen/oval](https://github.com/Argsen/oval)) was written in assistance with ChatGPT. This guide will help you set up the OVAL software for a development environment using Laravel Homestead, including the requirements and database migrations.

- [OVAL 2.0 - Interactive Video Platform](#oval-20---interactive-video-platform)
  - [1. Requirements](#1-requirements)
  - [2. Installation](#2-installation)
    - [2.1 Development Environment using Laravel Homestead](#21-development-environment-using-laravel-homestead)
- [3. Adaptations for AWS and Elastic Beanstalk:](#3-adaptations-for-aws-and-elastic-beanstalk)
  - [3.1 Resolving Composer Errors](#31-resolving-composer-errors)
  - [3.2 Setting up Ubuntu..](#32-setting-up-ubuntu)
  - [3.3 Setting an APP\_KEY..](#33-setting-an-app_key)
  - [3.4 Install database for first time.](#34-install-database-for-first-time)
  - [3.5 Debugging](#35-debugging)
  - [3.6 Run App Locally](#36-run-app-locally)
- [4. Additional Resources](#4-additional-resources)
  - [4.1 Setting up a Laravel environment](#41-setting-up-a-laravel-environment)


## 1. Requirements

- PHP >= 8.2
- Composer
- Laravel 10
- Vagrant
- VirtualBox or another supported virtualization provider
- A database server (e.g., MySQL, PostgreSQL, SQLite, etc.)

## 2. Installation

### 2.1 Development Environment using Laravel Homestead

1. Clone the OVAL repository:
```
git clone https://github.com/ebbertd/oval.git
```

2. Change the directory to the OVAL project folder:
```
cd oval
```

3. Install the Laravel Homestead Vagrant box:
```
vagrant box add laravel/homestead
```

4. Install the Homestead package globally:
```
composer global require laravel/homestead
```

Make sure to place the global Composer's `bin` directory in your `PATH` so the `homestead` executable can be located by your system.

5. Run the `init` command to create the Homestead configuration file:
```
homestead init
```

6. Configure your Homestead.yaml file to match your project settings. For example:

```yaml
---
ip: "192.168.10.10"
memory: 2048
cpus: 2
provider: virtualbox

authorize: ~/.ssh/id_rsa.pub

keys:
    - ~/.ssh/id_rsa

folders:
    - map: ~/code/oval
      to: /home/vagrant/oval

sites:
    - map: oval.test
      to: /home/vagrant/oval/public

databases:
    - oval
```

Update the folders and sites sections to match your project's directory structure and desired local domain. Add an entry to your computer's hosts file to map the domain to the IP address specified in the Homestead.yaml file:
```
192.168.10.10   oval.test
```

Start the Homestead Vagrant box:
```
homestead up
```

SSH into the Homestead box:
```
homestead ssh
```

Change the directory to the OVAL project folder within the virtual machine:
```
cd oval
```

Install dependencies using Composer:
```
composer install
```

Copy the .env.example file to create a new .env file:
```
cp .env.example .env
```

Generate an application key:
```
php artisan key:generate
```

Configure your database connection by editing the .env file with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=oval
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Run the database migrations and seed the database:
```
php artisan migrate --seed
```

The OVAL application should now be accessible at http://oval.test.


# 3. Adaptations for AWS and Elastic Beanstalk:

## 3.1 Resolving Composer Errors
Got lots of errors running composer to install packages related to Google API Services. Seems to need an increase in the default timeout.
Phrase that seemed to work is:
```
COMPOSER_PROCESS_TIMEOUT=2000 composer install
```

## 3.2 Setting up Ubuntu..
Add driver for php and mysql
``
sudo apt-get install php7.4-mysql

``

## 3.3 Setting an APP_KEY..
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


## 3.4 Install database for first time.
1. Manually create db in MySQL Workbench (there must be a blank database/schema to connect to)
2. Create migrate table
```
php artisan migrate:install
```
3. Run migrations - sets up database scheme - runs scripts found in `/databases/migrations`
```
php artisan migrate
```

4. Seed the database with dummy data (First time only) - runs scripts found in `/databases/seeds`
```
php artisan db:seed
```

## 3.5 Debugging

Laravel Logs can be found in `/storage/logs/laravel.log`


## 3.6 Run App Locally
```
php artisan serve
```


# 4. Additional Resources

## 4.1 Setting up a Laravel environment
https://teamhelium.medium.com/deploying-laravel-to-elastic-beanstalk-in-2021-5a3d9cc6696d