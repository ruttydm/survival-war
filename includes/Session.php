<?php
/**
 * Session Class
 *
 * Secure session management
 */
class Session {
    /**
     * Start secure session
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure session security settings
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Strict');

            // Set session cookie parameters
            $cookieParams = [
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Strict'
            ];

            session_set_cookie_params($cookieParams);

            session_start();

            // Regenerate session ID periodically
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
                $_SESSION['last_activity'] = time();
            } else {
                // Regenerate ID every 30 minutes
                if (time() - $_SESSION['created'] > 1800) {
                    session_regenerate_id(true);
                    $_SESSION['created'] = time();
                }

                // Check session timeout (2 hours by default)
                if (defined('SESSION_LIFETIME') && (time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
                    self::destroy();
                    return false;
                }
            }

            $_SESSION['last_activity'] = time();
        }

        return true;
    }

    /**
     * Destroy session
     */
    public static function destroy() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            // Delete session cookie
            if (isset($_COOKIE[session_name()])) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();
        }
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public static function isLoggedIn() {
        return isset($_SESSION['myusername']) && isset($_SESSION['userid']);
    }

    /**
     * Check if admin is logged in
     *
     * @return bool
     */
    public static function isAdminLoggedIn() {
        return isset($_SESSION['adminname']);
    }

    /**
     * Get user ID
     *
     * @return int|null
     */
    public static function getUserId() {
        return $_SESSION['userid'] ?? null;
    }

    /**
     * Get username
     *
     * @return string|null
     */
    public static function getUsername() {
        return $_SESSION['myusername'] ?? null;
    }

    /**
     * Get admin name
     *
     * @return string|null
     */
    public static function getAdminName() {
        return $_SESSION['adminname'] ?? null;
    }

    /**
     * Set user session
     *
     * @param int $userId
     * @param string $username
     */
    public static function setUser($userId, $username) {
        // Regenerate session ID on login
        session_regenerate_id(true);

        $_SESSION['userid'] = $userId;
        $_SESSION['myusername'] = $username;
        $_SESSION['created'] = time();
        $_SESSION['last_activity'] = time();
    }

    /**
     * Set admin session
     *
     * @param string $adminName
     */
    public static function setAdmin($adminName) {
        // Regenerate session ID on login
        session_regenerate_id(true);

        $_SESSION['adminname'] = $adminName;
        $_SESSION['created'] = time();
        $_SESSION['last_activity'] = time();
    }

    /**
     * Clear user session (logout)
     */
    public static function clearUser() {
        unset($_SESSION['userid']);
        unset($_SESSION['myusername']);
    }

    /**
     * Clear admin session (logout)
     */
    public static function clearAdmin() {
        unset($_SESSION['adminname']);
    }
}
