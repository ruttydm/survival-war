<?php
/**
 * Buy Army Units
 *
 * Allows players to purchase offensive and defensive military units.
 * Each troop costs 75 gold and each acre of land can support max 10 troops.
 */

require_once 'includes/bootstrap.php';

// Check authentication
if (!isset($_SESSION['player'])) {
    header('Location: login.php');
    exit;
}

$resultMessage = null;

try {
    $player = $_SESSION['player'];

    // Fetch user stats
    $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
    $stmt->execute(['player' => $player]);
    $userstats3 = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userstats3) {
        die("Could not get user stats");
    }

    if (isset($_POST['submit'])) {
        // Sanitize and validate inputs
        $off = Validator::sanitizeInt($_POST['off'] ?? 0);
        $dff = Validator::sanitizeInt($_POST['dff'] ?? 0);

        $totalcost = ($off + $dff) * 75;
        $totalunits = $off + $dff + $userstats3['offarmy'] + $userstats3['dffarmy'];
        $landhold = $userstats3['land'] * 10;
        $threshold = time() - 3600 * 6;

        if ($off < 0 || $dff < 0) {
            $resultMessage = "You may not buy negative units";
        } else if ($userstats3['numturns'] < 1) {
            $resultMessage = "You must have at least 1 turn to buy units. Go back to <A href='index.php'>Main</a>";
        } else if ($totalcost > $userstats3['gold']) {
            $resultMessage = "You do not have that much gold, go back to <A href='index.php'>Main page</a>";
        } else if ($totalunits > $landhold) {
            $resultMessage = "You do not have enough land to support that many units, go back to <A href='index.php'>Main</a>";
        } else if ($userstats3['lastaction'] > $threshold) {
            $resultMessage = "You have to wait six hours after an attack to buy troops, go back to <A href='index.php'>Main</a>.";
        } else if ($totalunits <= $landhold) {
            // Update user stats with purchased army units
            $updateStmt = $db->prepare(
                "UPDATE km_users
                 SET gold = gold - :totalcost,
                     offarmy = offarmy + :off,
                     dffarmy = dffarmy + :dff,
                     numturns = numturns - 1
                 WHERE ID = :id"
            );
            $updateStmt->execute([
                'totalcost' => $totalcost,
                'off' => $off,
                'dff' => $dff,
                'id' => $userstats3['ID']
            ]);
            $resultMessage = "Troops aquired. go back to <A href='index.php'>Main</a>.";
        }
    }
} catch (PDOException $e) {
    error_log("Buy army error: " . $e->getMessage());
    die("An error occurred while purchasing army units. Please try again.");
}

// Prepare template data
$templateData = [
    'resultMessage' => $resultMessage,
];

$template = TemplateEngine::getInstance();
$template->display('pages/buyarmy.latte', $templateData);
?>
