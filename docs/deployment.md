# Deployment Guide

Oval is a typical Laravel application. It can be deployed on any server that meets the [Laravel server requirements](https://laravel.com/docs/10.x/deployment#server-requirements).

## Prerequisites

- PHP >= 8.2
- Mysql >= 8.0
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/en/)

## Clone the repository

```bash
$ git clone git@github.com:c3l-lab/OVAL.git
$ cd OVAL
```

### Create a `.env` file

```bash
$ cp .env.example .env
```

Edit the `.env` file and set the following variables:

- APP_ENV=production
- APP_DEBUG=false
- APP_URL=your-app-url
- YOUTUBE_API_KEY=your-youtube-api-key
- Database credentials

## Install dependencies

```bash
$ composer install --no-dev --optimize-autoloader
$ npm install --production
```

## Build assets

```bash
$ npm run production
```

Setup the database:

```bash
$ php artisan migrate
$ php artisan db:seed
```

Optimize the application:

```bash
$ php artisan config:cache
$ php artisan route:cache
$ php artisan view:cache
```

## Reverse proxy

Follow the [Laravel documentation](https://laravel.com/docs/10.x/deployment#nginx) to configure your reverse proxy.

## Default User

```
Email: admin@example.com
Password: password
```
