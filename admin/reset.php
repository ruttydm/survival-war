<?php
/**
 * Reset Game
 *
 * Admin interface to reset the game (delete all users)
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

if (isset($_POST['submit'])) {
    // Reset game (delete all users)
    echo "<center><h3>Kill Monster Admin</h3></center><br>";
    echo "<center>";
    echo "<table border='0' width='70%' cellspacing='20'>";
    echo "<tr><td width='25%' valign='top'>";
    include 'left.php';
    echo "</td>";
    echo "<td valign='top' width='75%'>";

    try {
        $db->exec("DELETE FROM km_users");
        echo "Game reset successfully";
    } catch (PDOException $e) {
        error_log("Error resetting game: " . $e->getMessage());
        echo "Error resetting game. Please try again.";
    }

    echo "</td></tr></table>";
    echo "</center>";
} else {
    // Show confirmation form
    echo "<center><h3>Kill Monster Admin</h3></center><br>";
    echo "<center>";
    echo "<table border='0' width='70%' cellspacing='20'>";
    echo "<tr><td width='25%' valign='top'>";
    include 'left.php';
    echo "</td>";
    echo "<td valign='top' width='75%'>";
    echo "<center><form action='reset.php' method='post'>";
    echo "<input type='submit' name='submit' value='Reset'></form>";
    echo "<br><br>Warning, resetting will delete all players and skills and they will have to register again. This basically begins a new round in the game.<br>";
    echo "</td></tr></table>";
    echo "</center>";
}
