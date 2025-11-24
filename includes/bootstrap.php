<?php
/**
 * Bootstrap File
 *
 * Initializes the application by loading configuration,
 * starting sessions, and loading required classes.
 */

// Load Composer autoloader if available
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Load configuration
require_once __DIR__ . '/config.php';

// Load core classes
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/ErrorHandler.php';
require_once __DIR__ . '/TemplateEngine.php';
require_once __DIR__ . '/functions.php';

// Initialize error handler
ErrorHandler::init();

// Start session
Session::start();

// Set security headers
Security::setSecurityHeaders();

// Get database connection
$db = Database::getInstance()->getConnection();
