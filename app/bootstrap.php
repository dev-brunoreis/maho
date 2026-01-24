<?php

// Lightweight web bootstrap: load .env early so MAHO_DB_* are available to the web process.
if (!defined('MAHO_ROOT_DIR')) {
    define('MAHO_ROOT_DIR', dirname(__DIR__));
}

ini_set('display_errors', '1');
error_reporting(E_ALL);


use Symfony\Component\Dotenv\Dotenv;

// if (!isTestEnvironment()) {
$dotenv = new Dotenv();
if (file_exists(dirname(__DIR__) . '/.env')) {
    $dotenv->load(dirname(__DIR__) . '/.env');
}
// }

function isTestEnvironment(): bool
{
    // PHPUnit sets PHPUNIT_COMPOSER_INSTALL when invoked via Composer.
    if (defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__')) {
        return true;
    }
    // Common env flags (optional).
    $env = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? getenv('APP_ENV') ?: '';
    $mahoEnv = $_ENV['MAHO_ENV'] ?? $_SERVER['MAHO_ENV'] ?? getenv('MAHO_ENV') ?: '';
    if (is_string($env) && strtolower($env) === 'testing') {
        return true;
    }
    if (is_string($mahoEnv) && strtolower($mahoEnv) === 'testing') {
        return true;
    }
    // Heuristic: test runners usually invoke pest/phpunit binaries.
    $argv0 = $_SERVER['argv'][0] ?? '';
    if (is_string($argv0) && ($argv0 !== '') && (str_contains($argv0, 'pest') || str_contains($argv0, 'phpunit'))) {
        return true;
    }
    return false;
}
