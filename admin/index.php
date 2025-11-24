<?php
/**
 * Admin Dashboard
 *
 * Main admin panel landing page
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

$latte->render(__DIR__ . '/../templates/admin/index.latte');
