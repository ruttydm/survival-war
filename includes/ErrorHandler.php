<?php
/**
 * ErrorHandler Class
 *
 * Centralized error handling and logging
 */
class ErrorHandler {
    private static $logFile;
    private static $initialized = false;

    /**
     * Initialize error handler
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }

        self::$logFile = __DIR__ . '/../logs/error.log';

        // Ensure logs directory exists
        $logsDir = dirname(self::$logFile);
        if (!is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatal']);

        self::$initialized = true;
    }

    /**
     * Handle PHP errors
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        // Don't log errors that are suppressed with @
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $errorType = self::getErrorType($errno);
        $message = sprintf(
            "[%s] %s: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $errorType,
            $errstr,
            $errfile,
            $errline
        );

        self::logError($message);

        // Display error in debug mode
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<div style='color: red; font-family: monospace;'>$message</div>";
        }

        // Don't execute PHP's internal error handler
        return true;
    }

    /**
     * Handle uncaught exceptions
     *
     * @param Throwable $exception
     */
    public static function handleException($exception) {
        $message = sprintf(
            "[%s] Uncaught Exception: %s in %s on line %d\nStack trace:\n%s",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        self::logError($message);

        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<pre style='color: red;'>$message</pre>";
        } else {
            self::displayErrorPage();
        }

        exit(1);
    }

    /**
     * Handle fatal errors
     */
    public static function handleFatal() {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $message = sprintf(
                "[%s] Fatal Error: %s in %s on line %d",
                date('Y-m-d H:i:s'),
                $error['message'],
                $error['file'],
                $error['line']
            );

            self::logError($message);

            if (!(defined('DEBUG_MODE') && DEBUG_MODE)) {
                self::displayErrorPage();
            }
        }
    }

    /**
     * Log error message to file
     *
     * @param string $message
     */
    private static function logError($message) {
        error_log($message . PHP_EOL, 3, self::$logFile);
    }

    /**
     * Display generic error page
     */
    private static function displayErrorPage() {
        if (!headers_sent()) {
            http_response_code(500);
        }

        echo '<!DOCTYPE html>
<html>
<head>
    <title>Error - Survival War</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #d9534f; }
        p { color: #666; }
    </style>
</head>
<body>
    <h1>An Error Occurred</h1>
    <p>We\'re sorry, but something went wrong. Please try again later.</p>
    <p><a href="index.php">Return to Home</a></p>
</body>
</html>';
    }

    /**
     * Get error type name
     *
     * @param int $errno
     * @return string
     */
    private static function getErrorType($errno) {
        $errorTypes = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            // E_STRICT is deprecated in PHP 8.4
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        return $errorTypes[$errno] ?? 'UNKNOWN';
    }
}
