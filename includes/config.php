<?php
/**
 * Configuration File
 *
 * Centralized configuration management.
 * Loads environment variables from .env file if present.
 */

// Load environment variables from .env file
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE pairs
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes if present
            if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }

            // Define constant if not already defined
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }
}

// Database Configuration
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: '');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: '');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: '');

// Site Configuration
if (!defined('SITE_URL')) define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost');
if (!defined('SITE_NAME')) define('SITE_NAME', getenv('SITE_NAME') ?: 'Survival War');
if (!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'admin@example.com');

// Game Configuration
if (!defined('MAX_TURNS')) define('MAX_TURNS', 100);
if (!defined('TURNS_PER_HOUR')) define('TURNS_PER_HOUR', 10);
if (!defined('ATTACK_COOLDOWN')) define('ATTACK_COOLDOWN', 3600);
if (!defined('MAX_ARMY_PER_LAND')) define('MAX_ARMY_PER_LAND', 10);
if (!defined('ARMY_COST')) define('ARMY_COST', 75);

// Security Configuration
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 7200);
if (!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', 8);

// Email Configuration
if (!defined('MAIL_FROM')) define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@example.com');
if (!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'Survival War');

// Development Settings
if (!defined('DEBUG_MODE')) define('DEBUG_MODE', filter_var(getenv('DEBUG_MODE') ?: 'false', FILTER_VALIDATE_BOOLEAN));
if (!defined('DISPLAY_ERRORS')) define('DISPLAY_ERRORS', DEBUG_MODE);
if (!defined('LOG_ERRORS')) define('LOG_ERRORS', true);

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Version
if (!defined('VERSION')) define('VERSION', '1.0');

// Legacy compatibility - parse query string (deprecated but kept for compatibility)
if (isset($QUERY_STRING)) {
    parse_str($QUERY_STRING ?? '', $parsed);
}
