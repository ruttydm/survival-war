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

echo "<center><h3>Kill Monster Admin</h3></center><br>";
echo "<center>";
echo "<table border='0' width='70%' cellspacing='20'>";
echo "<tr><td width='25%' valign='top'>";
include 'left.php';
echo "</td>";
echo "<td valign='top' width='75%'>";

try {
    echo "<table border='1' bordercolor='white' bgcolor='#e1e1e1'>";
    echo "<tr><td>Forum name</td><td>Forum Description</td><td>Edit</td><td>Delete</td></tr>";

    $stmt = $db->query("SELECT * FROM km_forums ORDER BY forumname ASC");

    while ($forum = $stmt->fetch()) {
        $forumname = htmlspecialchars($forum['forumname']);
        $descrip = htmlspecialchars($forum['descrip']);
        $forumID = (int)$forum['forumID'];

        echo "<tr>";
        echo "<td>$forumname</td>";
        echo "<td>$descrip</td>";
        echo "<td><a href='edit.php?ID=$forumID'>Edit</a></td>";
        echo "<td><a href='delete.php?ID=$forumID'>Delete</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} catch (PDOException $e) {
    error_log("Error fetching forums: " . $e->getMessage());
    echo "Error loading forums. Please try again.";
}

echo "</td></tr></table>";
echo "</center>";
