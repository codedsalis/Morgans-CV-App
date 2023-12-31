# Morgans CV App

This rest API is a lean implementation of the requirements stated, please kindly consider it

# Requirements

This system is implemented using the Laravel framework and requires the following:
* PHP - v8.1
* Laravel - v10
* MySQL/MariaDB - >= v5/v7

# Installation
To begin with, copy the `.env.example` file to `.env`

Install the dependencies by running

```bash
composer install
```

Once done you should run the migrations

```bash
php artisan migrate
```

To start the application, run

```bash
php artisan serve
```

To start the queue, run

```bash
php artisan queue:work
```

To check the available endpoints please run
```bash
php artisan route:list
```

To run test

```bash
php artisan test
```

Thanks.
