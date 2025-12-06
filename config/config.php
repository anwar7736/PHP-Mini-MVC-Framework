<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    */
    "default" => env("DB_CONNECTION", "mysql"),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */
    "connections" => [

        "mysql" => [
            "driver"    => "mysql",
            "host"      => env("DB_HOST", "127.0.0.1"),
            "port"      => env("DB_PORT", "3306"),
            "database"  => env("DB_DATABASE", "php_framework"),
            "username"  => env("DB_USERNAME", "root"),
            "password"  => env("DB_PASSWORD", ""),
            "charset"   => "utf8mb4",
            "collation" => "utf8mb4_unicode_ci",
        ],

        "pgsql" => [
            "driver"   => "pgsql",
            "host"     => env("DB_HOST", "127.0.0.1"),
            "port"     => env("DB_PORT", "5432"),
            "database" => env("DB_DATABASE", "php_framework"),
            "username" => env("DB_USERNAME", "postgres"),
            "password" => env("DB_PASSWORD", ""),
        ],

        "sqlite" => [
            "driver"   => "sqlite",
            "database" => env("DB_DATABASE", __DIR__ . "/../database.sqlite"),
        ],

        "sqlsrv" => [
            "driver"   => "sqlsrv",
            "host"     => env("DB_HOST", "localhost"),
            "port"     => env("DB_PORT", "1433"),
            "database" => env("DB_DATABASE", "php_framework"),
            "username" => env("DB_USERNAME", "sa"),
            "password" => env("DB_PASSWORD", ""),
        ],
    ],

];
