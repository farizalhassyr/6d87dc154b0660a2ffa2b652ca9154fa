<?php

//Load Composer's autoloader
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('..');
$dotenv->load();

$host       =  $_ENV['DB_HOST'];
$dbname     =  $_ENV['DB_NAME'];
$dbuser     =  $_ENV['DB_USERNAME'];
$dbpass     =  $_ENV['DB_PASSWORD'];
$port       =  $_ENV['DB_PORT'];

// We use PDO (PHP Data Object) to be more maintainable
$pdo = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass);
