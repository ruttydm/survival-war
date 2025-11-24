<?php
/**
 * Revive Player
 *
 * Allows a dead player to respawn and continue playing.
 * Sets the player's dead status to 'no'.
 */

require_once 'includes/bootstrap.php';

if (isset($_SESSION['player'])) {
    if (isset($_POST['revives'])) {
        try {
            // Sanitize and validate inputs
            $ID = Validator::sanitizeInt($_POST['ID'] ?? 0);

            if ($ID <= 0) {
                die("Invalid player ID");
            }

            // Update player status to revive them
            $stmt = $db->prepare("UPDATE km_users SET dead = 'no' WHERE ID = :id");
            $stmt->execute(['id' => $ID]);

            print "<A href='index.php'>Go back to main page</a>";
        } catch (PDOException $e) {
            error_log("Revive error: " . $e->getMessage());
            die("Could not revive player. Please try again.");
        }
    }
} else {
    print "Not logged in";
}

?>