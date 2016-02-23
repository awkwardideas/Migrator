# Migrator: MySQL to Laravel Migration Generator

[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/awkwardideas/migrator)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/awkwardideas/migrator)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/awkwardideas/migrator)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/awkwardideas/migrator)

## Install Via Composer

composer require awkwardideas/migrator

## Commands via Artisan

Command line actions are done via artisan.  The host, username, password from the .env file are used for making the connection.

### php artisan migrator:clean

Removes all migrations files from the migrations folder

### php artisan migrator:truncate

Truncates the provided database.  

--database=  Database to truncate
--force  Bypass confirmations

### php artisan migrator:purge

Combination of Clean and Truncate

Options:

--database=  Database to truncate
--force  Bypass confirmations

### php artisan migrator:prepare

Create migration files using the database information in .env

Options:

--from=  Database to migrate from
--force  Bypass confirmations

### php artisan migrator:migrate

Create migration files using the database information in .env and run php artisan migrate

Options:

--from=  Database to migrate from
--to= Database to migrate to (for truncation)
--force  Bypass confirmations
