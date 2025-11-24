<?php
/**
 * Buy Land
 *
 * Allows players to purchase land acres for their territory.
 * Land costs $500 per acre and is used to support army units and resources.
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
        $landacres = Validator::sanitizeInt($_POST['landacres'] ?? 0);
        $total = $landacres * 500;

        if ($landacres < 0) {
            $resultMessage = "You cannot buy negative land. <A href='index.php'>Go back to main</a>.";
        } else if ($userstats3['numturns'] < 1) {
            $resultMessage = "You must have at least 1 turn to buy land. Go back to <A href='index.php'>Main</a>";
        } else if ($userstats3['gold'] < $total) {
            $resultMessage = "You do not have enough cash to buy that many acres. <A href='index.php'>Go back to main</a>.";
        } else if ($userstats3['gold'] >= $total) {
            // Update user stats with purchased land
            $updateStmt = $db->prepare(
                "UPDATE km_users
                 SET gold = gold - :total,
                     land = land + :landacres,
                     numturns = numturns - 1
                 WHERE ID = :id"
            );
            $updateStmt->execute([
                'total' => $total,
                'landacres' => $landacres,
                'id' => $userstats3['ID']
            ]);
            $resultMessage = "You bought $landacres acres of land. <A href='index.php'>Go back to main</a>.";
        }
    }
} catch (PDOException $e) {
    error_log("Buy land error: " . $e->getMessage());
    die("An error occurred while purchasing land. Please try again.");
}

// Prepare template data
$templateData = [
    'resultMessage' => $resultMessage,
];

$template = TemplateEngine::getInstance();
$template->display('pages/buyland.latte', $templateData);
?>
