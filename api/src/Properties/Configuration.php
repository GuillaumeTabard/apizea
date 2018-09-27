<?php

    namespace OAuth2ServerExamples\Properties;

    $prod = true;

    $env = $prod ? "prod" : "dev";

    $configs = [
        "prod" => [
            "host" => "mysql.hostinger.fr",
            "name" => "u855233662_zea",
            "user" => "u855233662_admin",
            "pwd" => "zeaproject2018*",
        ],
        "dev" => [
            "host" => "localhost",
            "name" => "oauth_test",
            "user" => "root",
            "pwd" => "",
        ]
    ];

    define("DB_HOST", $configs[$env]["host"]);
    define("DB_NAME", $configs[$env]["name"]);
    define("DB_USER", $configs[$env]["user"]);
    define("DB_PWD", $configs[$env]["pwd"]);


    class Configuration {
        
        const DATABASE_HOST = DB_HOST;
        const DATABASE_NAME = DB_NAME;
        const DATABASE_USER = DB_USER;
        const DATABASE_PASSWORD = DB_PWD;
    }