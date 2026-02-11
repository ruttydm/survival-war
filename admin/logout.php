<?php
/**
 * Admin Logout
 *
 * Logs out the admin and destroys session
 */

require_once __DIR__ . '/../includes/bootstrap.php';

// Clear admin session and destroy
Session::clearAdmin();
Session::destroy();

// Redirect to login page
echo "<script>
window.location.href = 'login.php';
</script>";
exit;
