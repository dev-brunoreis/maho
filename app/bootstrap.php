<?php

// Lightweight web bootstrap: load .env early so MAHO_DB_* are available to the web process.
if (!defined('MAHO_ROOT_DIR')) {
    define('MAHO_ROOT_DIR', dirname(__DIR__));
}

ini_set('display_errors', '1');
error_reporting(E_ALL);


use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
if (file_exists(dirname(__DIR__).'/.env')) {
    $dotenv->load(dirname(__DIR__).'/.env');
}