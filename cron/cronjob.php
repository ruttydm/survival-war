<?php
#!/usr/local/bin/php
/**
 * Cron Job - Turn Regeneration
 *
 * Regenerates player turns (energy) periodically
 */

require_once __DIR__ . '/../includes/bootstrap.php';

try {
    // Regenerate turns for players with 90 or less turns (add 10 turns)
    $stmt1 = $db->prepare("UPDATE km_users SET numturns = numturns + 10 WHERE numturns <= 90");
    $stmt1->execute();

    // Cap turns at 100 for players who would exceed the limit
    $stmt2 = $db->prepare("UPDATE km_users SET numturns = 100 WHERE numturns > 90");
    $stmt2->execute();

    echo "Turn regeneration completed successfully\n";
} catch (PDOException $e) {
    error_log("Cron job failed: " . $e->getMessage());
    die("Cron failed: " . $e->getMessage() . "\n");
}
?>