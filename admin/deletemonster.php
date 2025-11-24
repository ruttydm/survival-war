<?php
/**
 * Delete Monster
 *
 * Admin interface to delete monsters
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

$ID = $_GET['ID'] ?? null;

if ($ID) {
    // Delete monster
    echo "<center><h3>Kill Monster Admin</h3></center><br>";
    echo "<center>";
    echo "<table border='0' width='70%' cellspacing='20'>";
    echo "<tr><td width='25%' valign='top'>";
    include 'left.php';
    echo "</td>";
    echo "<td valign='top' width='75%'>";

    try {
        $stmt = $db->prepare("DELETE FROM km_monsters WHERE ID = :id");
        $stmt->execute(['id' => $ID]);
        echo "Monster deleted Successfully";
    } catch (PDOException $e) {
        error_log("Error deleting monster: " . $e->getMessage());
        echo "Error deleting monster. Please try again.";
    }

    echo "</td></tr></table>";
    echo "</center>";
} else {
    // List monsters
    echo "<center><h3>Kill Monster Admin</h3></center><br>";
    echo "<center>";
    echo "<table border='0' width='70%' cellspacing='20'>";
    echo "<tr><td width='25%' valign='top'>";
    include 'left.php';
    echo "</td>";
    echo "<td valign='top' width='75%'>";

    try {
        $stmt = $db->query("SELECT * FROM km_monsters ORDER BY skill ASC");

        echo "<table border='1' bordercolor='white' bgcolor='#e1e1e1'>";
        echo "<tr><td>Monster name</td><td>Skill Points</td><td>Points if killed</td><td>Energy cost</td><td>Goldworth</td><td>Delete?</td></tr>";

        while ($monster = $stmt->fetch()) {
            $name = htmlspecialchars($monster['name']);
            $skill = (int)$monster['skill'];
            $pointsifkilled = (int)$monster['pointsifkilled'];
            $energycost = (int)$monster['energycost'];
            $goldworth = (int)$monster['goldworth'];
            $monsterID = (int)$monster['ID'];

            echo "<tr>";
            echo "<td>$name</td>";
            echo "<td>$skill</td>";
            echo "<td>$pointsifkilled</td>";
            echo "<td>$energycost</td>";
            echo "<td>$goldworth</td>";
            echo "<td><a href='deletemonster.php?ID=$monsterID'>Delete</a></td>";
            echo "</tr>";
        }

        echo "</table>";
    } catch (PDOException $e) {
        error_log("Error fetching monsters: " . $e->getMessage());
        echo "Error loading monsters. Please try again.";
    }

    echo "</td></tr></table>";
    echo "</center>";
}
