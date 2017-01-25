<?php

require "../vendor/autoload.php";

$dotEnv = new \Dotenv\Dotenv('../config/');
$dotEnv->load();
$dotEnv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);

include "Database.php";

session_start();
