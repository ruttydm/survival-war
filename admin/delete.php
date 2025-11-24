<?php
/**
 * Delete Forum Category
 *
 * Admin interface to delete forum categories
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

echo "<center><h3>Kill Monster Admin</h3></center><br>";
echo "<center>";
echo "<table border='0' width='70%' cellspacing='20'>";
echo "<tr><td width='25%' valign='top'>";
include 'left.php';
echo "</td>";
echo "<td valign='top' width='75%'>";

if (isset($_POST['submit'])) {
    // Delete forum
    $ID = $_POST['ID'] ?? null;

    if ($ID) {
        try {
            $stmt = $db->prepare("DELETE FROM km_forums WHERE forumID = :id");
            $stmt->execute(['id' => $ID]);
            echo "Forum deleted.";
        } catch (PDOException $e) {
            error_log("Error deleting forum: " . $e->getMessage());
            echo "Error deleting forum. Please try again.";
        }
    }
} else {
    // Show confirmation form
    $ID = $_GET['ID'] ?? null;

    if ($ID) {
        echo "<form action='delete.php' method='post'>";
        echo "<input type='hidden' name='ID' value='" . htmlspecialchars($ID) . "'>";
        echo "Are you sure you want to delete this forum?<br>";
        echo "<input type='submit' name='submit' value='Delete'></form>";
    } else {
        echo "No forum ID specified.";
    }
}

echo "</td></tr></table>";
echo "</center>";
