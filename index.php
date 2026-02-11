<?php
/**
 * Main Dashboard
 *
 * Displays user statistics and game options
 */

require_once 'includes/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['player'])) {
    header("Location: login.php");
    exit;
}


    $player = $_SESSION['player'];

    // Get user stats
    $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
    $stmt->execute(['player' => $player]);
    $userstats3 = $stmt->fetch();

    if (!$userstats3) {
        // User not found in database
        die("<p>Error: User account not found.</p>");
    }

    // Prepare template data
    $templateData = [
        'isDead' => ($userstats3['dead'] == 'Yes'),
        'killer' => $userstats3['killer'] ?? '',
        'userID' => $userstats3['ID'],
        'stats' => $userstats3,
    ];

    // If user is alive - reset justattacked flag
    if (!$templateData['isDead']) {
        $updateStmt = $db->prepare("UPDATE km_users SET justattacked = 0 WHERE ID = :id");
        $updateStmt->execute(['id' => $userstats3['ID']]);
    }

    // Render template
    $template = TemplateEngine::getInstance();
    $template->display('pages/index.latte', $templateData);
