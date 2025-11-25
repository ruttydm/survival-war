<?php
/**
 * List Forum Categories
 *
 * Admin interface to view and manage forum categories
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

try {
    $stmt = $db->query("SELECT * FROM km_forums ORDER BY forumname ASC");
    $forums = $stmt->fetchAll();

    $latte->render(__DIR__ . '/../templates/admin/listforums.latte', [
        'forums' => $forums
    ]);
} catch (PDOException $e) {
    error_log("Error fetching forums: " . $e->getMessage());
    $latte->render(__DIR__ . '/../templates/admin/listforums.latte', [
        'error' => "Error loading forums. Please try again."
    ]);
}
