<?php
/**
 * Add Forum Category
 *
 * Admin interface to create new forum categories
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

if (isset($_POST['submit'])) {
    // Process form submission
    echo "<center><h3>Add Forums</h3></center><br>";
    echo "<center>";
    echo "<table border='0' width='70%' cellspacing='20'>";
    echo "<tr><td width='25%' valign='top'>";
    include 'left.php';
    echo "</td>";
    echo "<td valign='top' width='75%'>";

    $forumname = Validator::sanitizeString($_POST['forumname'] ?? '', 100);
    $forumdesc = Validator::sanitizeString($_POST['forumdesc'] ?? '', 500);

    if (strlen($forumname) < 1) {
        echo "You did not enter a forum name.";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO km_forums (forumname, descrip) VALUES (:forumname, :forumdesc)");
            $stmt->execute([
                'forumname' => $forumname,
                'forumdesc' => $forumdesc
            ]);
            echo "Forum created.<br>";
        } catch (PDOException $e) {
            error_log("Error creating forum: " . $e->getMessage());
            echo "Error creating forum. Please try again.";
        }
    }

    echo "</td></tr></table>";
    echo "</center>";
} else {
    // Show form
    echo "<center><h3>Kill Monster Admin</h3></center><br>";
    echo "<center>";
    echo "<table border='0' width='70%' cellspacing='20'>";
    echo "<tr><td width='25%' valign='top'>";
    include 'left.php';
    echo "</td>";
    echo "<td valign='top' width='75%'>";
    echo "<form action='addforum.php' method='post'>";
    echo "Type name of forum to add:<br>";
    echo "<input type='text' name='forumname' size='20'><br>";
    echo "Enter a forum description:<br>";
    echo "<textarea name='forumdesc' cols='40' rows='5'></textarea><br>";
    echo "<input type='submit' name='submit' value='submit'></form>";
    echo "</td></tr></table>";
    echo "</center>";
}
