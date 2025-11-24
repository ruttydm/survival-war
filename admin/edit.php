<?php
/**
 * Edit Forum Category
 *
 * Admin interface to edit forum categories
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
    // Update forum
    $ID = $_POST['ID'] ?? null;
    $title = Validator::sanitizeString($_POST['title'] ?? '', 100);
    $description = Validator::sanitizeString($_POST['description'] ?? '', 500);

    if (strlen($title) < 1) {
        echo "You did not enter a title.";
    } else {
        try {
            $stmt = $db->prepare("UPDATE km_forums SET forumname = :title, descrip = :description WHERE forumID = :id");
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'id' => $ID
            ]);
            echo "Forum updated.";
        } catch (PDOException $e) {
            error_log("Error updating forum: " . $e->getMessage());
            echo "Error updating forum. Please try again.";
        }
    }
} else {
    // Show edit form
    $ID = $_GET['ID'] ?? null;

    if ($ID) {
        try {
            $stmt = $db->prepare("SELECT * FROM km_forums WHERE forumID = :id");
            $stmt->execute(['id' => $ID]);
            $forum = $stmt->fetch();

            if ($forum) {
                $forumname = htmlspecialchars($forum['forumname']);
                $descrip = htmlspecialchars($forum['descrip']);

                echo "<form action='edit.php' method='post'>";
                echo "<input type='hidden' name='ID' value='" . htmlspecialchars($ID) . "'>";
                echo "Title:<br>";
                echo "<input type='text' name='title' size='20' value='$forumname'><br>";
                echo "Description:<br>";
                echo "<textarea name='description' rows='5' cols='40'>$descrip</textarea><br>";
                echo "<input type='submit' name='submit' value='submit'></form>";
            } else {
                echo "Forum not found.";
            }
        } catch (PDOException $e) {
            error_log("Error fetching forum: " . $e->getMessage());
            echo "Error loading forum. Please try again.";
        }
    } else {
        echo "No forum ID specified.";
    }
}

echo "</td></tr></table>";
echo "</center>";
