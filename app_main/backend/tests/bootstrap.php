<?php

use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__).'/vendor/autoload.php';
// Force-load the Kernel with require_once to avoid rare autoload double-inclusion issues in some Docker setups.
require_once dirname(__DIR__).'/src/Kernel.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    // Don't override APP_ENV/APP_DEBUG injected by PHPUnit config (e.g. pgsql test setup).
    if (!isset($_SERVER['APP_ENV']) && !isset($_ENV['APP_ENV'])) {
        (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
    }
}

if (!empty($_SERVER['APP_DEBUG'])) {
    umask(0000);
}
