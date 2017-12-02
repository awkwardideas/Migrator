# Migrator: MySQL to Laravel Migration Generator

[![Latest Stable Version](https://poser.pugx.org/awkwardideas/migrator/v/stable)](https://packagist.org/packages/awkwardideas/migrator) 
[![Total Downloads](https://poser.pugx.org/awkwardideas/migrator/downloads)](https://packagist.org/packages/awkwardideas/migrator) 
[![Latest Unstable Version](https://poser.pugx.org/awkwardideas/migrator/v/unstable)](https://packagist.org/packages/awkwardideas/migrator) 
[![License](https://poser.pugx.org/awkwardideas/migrator/license)](https://packagist.org/packages/awkwardideas/migrator)

## Install Via Composer

composer require awkwardideas/migrator

## Add to Laravel App Config


    /*
     * Package Service Providers...
     */
    AwkwardIdeas\Migrator\MigratorServiceProvider::class,
    

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
