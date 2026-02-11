<?php

require_once 'includes/bootstrap.php';

// Check authentication
if (!isset($_SESSION['player'])) {
    header('Location: login.php');
    exit;
}

$attackMessage = null;
$battleRecords = [];

try {
    $player_name = $_SESSION['player'];

    // Fetch user stats
    $statement = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
    $statement->execute(['player' => $player_name]);
    $user_stats = $statement->fetch();

    if (!$user_stats) {
        die("Could not get user stats");
    }

    // Handle attack notifications
    $attack_count = (int)$user_stats['numberattck'];
    if ($attack_count > 0) {
        $attackMessage = "You have survived {$attack_count} attacks since your last login.";

        // Reset attack counter
        $reset_statement = $db->prepare("UPDATE km_users SET numberattck = '0' WHERE playername = :player");
        $reset_statement->execute(['player' => $player_name]);
    }

    // Fetch battle records
    $records_query = $db->query("SELECT * FROM km_battlerecords");
    while ($record = $records_query->fetch()) {
        $battleRecords[] = [
            'attid' => $record['attid'],
            'attname' => $record['attname'],
            'result' => $record['result'],
            'landlost' => $record['landlost'],
        ];
    }
} catch (PDOException $e) {
    ErrorHandler::handleException($e);
    die("A database error occurred. Please try again later.");
} catch (Exception $e) {
    ErrorHandler::handleException($e);
    die("An error occurred. Please try again later.");
}

// Prepare template data
$templateData = [
    'attackMessage' => $attackMessage,
    'battleRecords' => $battleRecords,
];

$template = TemplateEngine::getInstance();
$template->display('pages/logs.latte', $templateData);
?>
