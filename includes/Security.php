<?php
/**
 * Security Class
 *
 * Handles CSRF protection and other security features
 */
class Security {
    /**
     * Generate CSRF token
     *
     * @return string
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     *
     * @param string $token
     * @return bool
     */
    public static function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get CSRF input field HTML
     *
     * @return string
     */
    public static function getCSRFInput() {
        return '<input type="hidden" name="csrf_token" value="' .
               htmlspecialchars(self::generateCSRFToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Get CSRF token value
     *
     * @return string
     */
    public static function getCSRFToken() {
        return self::generateCSRFToken();
    }

    /**
     * Set security headers
     */
    public static function setSecurityHeaders() {
        if (!headers_sent()) {
            header("X-Frame-Options: DENY");
            header("X-Content-Type-Options: nosniff");
            header("X-XSS-Protection: 1; mode=block");
            header("Referrer-Policy: strict-origin-when-cross-origin");
        }
    }
}
