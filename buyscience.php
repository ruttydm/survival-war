<?php
/**
 * Buy Science Points
 *
 * Allows players to purchase science/training points that provide power bonuses.
 * Each science point costs 35 gold and provides percentage-based power increase.
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

    // Calculate science information
    $hasLand = $userstats3['land'] > 0;
    $currentScience = $userstats3['science'];
    $sciencePercent = 0;

    if ($hasLand) {
        $max = $userstats3['land'] * 10;
        $sciencePercent = round($userstats3['science'] / $max * 100, 2);
    }

    if (isset($_POST['submit'])) {
        // Sanitize and validate inputs
        $scipts = Validator::sanitizeInt($_POST['scipts'] ?? 0);
        $totalcost = $scipts * 35;

        if ($scipts < 0) {
            $resultMessage = "You cannot buy negative science. Back to <A href='index.php'>Main</a>";
        } else if ($userstats3['numturns'] < 1) {
            $resultMessage = "You must have at least 1 turn to buy Science. Go back to <A href='index.php'>Main</a>";
        } else if ($totalcost > $userstats3['gold']) {
            $resultMessage = "You do not have enough gold. Please go back to <A href='index.php'>Main</a>";
        } else if ($totalcost <= $userstats3['gold']) {
            // Update user stats with purchased science points
            $updateStmt = $db->prepare(
                "UPDATE km_users
                 SET science = science + :scipts,
                     gold = gold - :totalcost,
                     numturns = numturns - 1
                 WHERE ID = :id"
            );
            $updateStmt->execute([
                'scipts' => $scipts,
                'totalcost' => $totalcost,
                'id' => $userstats3['ID']
            ]);
            $resultMessage = "You have bought $scipts science points. Back to<A href='index.php'>Main Page</a>";
        }
    }
} catch (PDOException $e) {
    error_log("Buy science error: " . $e->getMessage());
    die("An error occurred while purchasing science points. Please try again.");
}

// Prepare template data
$templateData = [
    'resultMessage' => $resultMessage,
    'hasLand' => $hasLand,
    'currentScience' => $currentScience,
    'sciencePercent' => $sciencePercent,
];

$template = TemplateEngine::getInstance();
$template->display('pages/buyscience.latte', $templateData);
?>
