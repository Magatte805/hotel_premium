<?php

use App\Kernel;

// Load versioned local dev env defaults (no .env needed).
$localEnvFile = dirname(__DIR__).'/config/local_env.php';
if (is_file($localEnvFile)) {
    $localEnv = require $localEnvFile;
    if (is_array($localEnv)) {
        foreach ($localEnv as $key => $value) {
            if (!isset($_SERVER[$key]) && !isset($_ENV[$key])) {
                $_SERVER[$key] = $_ENV[$key] = (string) $value;
                putenv($key.'='.$value);
            }
        }
    }
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
