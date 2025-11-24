<?php
/**
 * Add Monster
 *
 * Admin interface to create new monsters
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

if (isset($_POST['submit'])) {
    // Process form submission
    echo "<center><h3>Kill Monster Admin</h3></center><br>";
    echo "<center>";
    echo "<table border='0' width='70%' cellspacing='20'>";
    echo "<tr><td width='25%' valign='top'>";
    include 'left.php';
    echo "</td>";
    echo "<td valign='top' width='75%'>";

    $image = Validator::sanitizeString($_POST['image'] ?? '', 255);
    $monstername = Validator::sanitizeString($_POST['monstername'] ?? '', 100);
    $energycost = (int)($_POST['energycost'] ?? 0);
    $skillpts = (int)($_POST['skillpts'] ?? 0);
    $killpts = (int)($_POST['killpts'] ?? 0);
    $gold = (int)($_POST['goldpts'] ?? 0);

    try {
        // Check if monster already exists
        $stmt = $db->prepare("SELECT * FROM km_monsters WHERE name = :name");
        $stmt->execute(['name' => $monstername]);
        $existingMonster = $stmt->fetch();

        if ($existingMonster) {
            echo "Sorry there is already a monster of that name";
        } else {
            // Create new monster (fixed SQL syntax - added missing comma)
            $stmt = $db->prepare("INSERT INTO km_monsters (name, skill, pointsifkilled, goldworth, energycost, image)
                                  VALUES (:name, :skill, :pointsifkilled, :goldworth, :energycost, :image)");
            $stmt->execute([
                'name' => $monstername,
                'skill' => $skillpts,
                'pointsifkilled' => $killpts,
                'goldworth' => $gold,
                'energycost' => $energycost,
                'image' => $image
            ]);
            echo "Monster created successfully<br>";
        }
    } catch (PDOException $e) {
        error_log("Error creating monster: " . $e->getMessage());
        echo "Error creating monster. Please try again.";
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
    echo "In creating a monster, you will specify the monster's name, skill points if the monster has, and skill points gained if they monster is killed, only integers please, otherwise it will round down<br>";
    echo "<form action='addmonster.php' method='post'>";
    echo "Monster's name:<br>";
    echo "<input type='text' name='monstername' size='15'><br>";
    echo "Monster's skill points:<br>";
    echo "<input type='text' name='skillpts' size='6'><br>";
    echo "Image:<br>";
    echo "<input type='text' name='image'><br>";
    echo "Skill points gained by players if killed:<br>";
    echo "<input type='text' name='killpts' size='6'><br>";
    echo "Energy losed by if killed:<br>";
    echo "<input type='text' name='energycost' size='6'><br>";
    echo "Gold if killed:<br>";
    echo "<input type='text' name='goldpts' size='6'><br>";
    echo "<input type='submit' name='submit' value='Create Monster'>";
    echo "</form>";
    echo "</td></tr></table>";
    echo "</center>";
}
