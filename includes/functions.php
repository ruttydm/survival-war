<?php
/**
 * Utility Functions
 */

/**
 * Get current page URL
 *
 * @return string
 */
function curPageURL() {
    $pageURL = 'http';

    // Check if HTTPS is enabled
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }

    $pageURL .= "://";

    // Add server name and port
    if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }

    return str_replace(".php", "", $pageURL);
}
?>