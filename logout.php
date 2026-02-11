<?php
/**
 * User Logout
 *
 * Destroys user session and redirects to login page
 */

require_once 'includes/bootstrap.php';

// Destroy session using Session class
Session::destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>
