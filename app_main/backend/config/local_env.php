<?php

/**
 * Local dev environment defaults (versioned) to make the project runnable without .env files.
 *
 * IMPORTANT:
 * - These values are only applied when the corresponding env var is NOT already set.
 * - Adjust as needed for your local machine.
 */
return [
    'APP_ENV' => 'dev',
    'APP_DEBUG' => '1',
    // Change this for any real deployment; fine for local dev.
    'APP_SECRET' => 'dev_secret_change_me',

    // SQLite fallback for local dev (Docker/MySQL not required).
    // The "resolve:" processor in doctrine.yaml will expand %kernel.project_dir%.
    'DATABASE_URL' => 'sqlite:///%kernel.project_dir%/var/data_dev.db',
];


