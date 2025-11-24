<?php
/**
 * Validator Class
 *
 * Input validation and sanitization utilities
 */
class Validator {
    /**
     * Sanitize string input
     *
     * @param string $input
     * @param int $maxLength
     * @return string
     */
    public static function sanitizeString($input, $maxLength = 255) {
        $input = trim($input);
        $input = strip_tags($input);
        if ($maxLength > 0) {
            $input = substr($input, 0, $maxLength);
        }
        return $input;
    }

    /**
     * Sanitize and validate integer
     *
     * @param mixed $input
     * @param int $min
     * @param int $max
     * @return int|false
     */
    public static function sanitizeInt($input, $min = null, $max = null) {
        $value = filter_var($input, FILTER_VALIDATE_INT);

        if ($value === false) {
            return false;
        }

        if ($min !== null && $value < $min) {
            return false;
        }

        if ($max !== null && $value > $max) {
            return false;
        }

        return $value;
    }

    /**
     * Sanitize email address
     *
     * @param string $input
     * @return string
     */
    public static function sanitizeEmail($input) {
        return filter_var($input, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Validate email address
     *
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Sanitize HTML for output (XSS prevention)
     *
     * @param string $input
     * @return string
     */
    public static function escapeHtml($input) {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate username format
     *
     * @param string $username
     * @param int $minLength
     * @param int $maxLength
     * @return bool
     */
    public static function validateUsername($username, $minLength = 3, $maxLength = 32) {
        $length = strlen($username);
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }

        // Allow alphanumeric, underscore, and hyphen
        return preg_match('/^[a-zA-Z0-9_-]+$/', $username) === 1;
    }

    /**
     * Validate password strength
     *
     * @param string $password
     * @param int $minLength
     * @return bool
     */
    public static function validatePassword($password, $minLength = 8) {
        return strlen($password) >= $minLength;
    }

    /**
     * Sanitize array recursively
     *
     * @param array $array
     * @return array
     */
    public static function sanitizeArray($array) {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::sanitizeArray($value);
            } else {
                $result[$key] = self::sanitizeString($value);
            }
        }
        return $result;
    }
}
