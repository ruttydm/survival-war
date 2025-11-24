<?php
/**
 * Buy Land
 *
 * Allows players to purchase land acres for their territory.
 * Land costs $500 per acre and is used to support army units and resources.
 */

require_once 'includes/bootstrap.php';
include "up_html.php";

print "<table class='maintable'><tr class='headline'><td><center>Buy Land</center></td></tr>";
print "<tr class='mainrow'><td>Land Costs $500 per acre(very expensive).<br>";
print "<form action='' method='post'>";
print "<input type='text' placeholder='Arces to buy' name='landacres' class='inline_text_inp' style='width:150px;' >&nbsp;";
print "<input type='submit' name='submit' value='buy' class='RedButton' style='width:93px'></form></td></tr></table><br><br>";
print "<td valign='top'>";

if (isset($_SESSION['player'])) {
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
            print "<center>";
            print "<table class='maintable'>";
            print "<tr class='headline'><td><center>Buy Land</center></td></tr>";
            print "<tr class='mainrow'><td>";

            // Sanitize and validate inputs
            $landacres = Validator::sanitizeInt($_POST['landacres'] ?? 0);
            $total = $landacres * 500;

            if ($landacres < 0) {
                print "You cannot buy negative land. <A href='index.php'>Go back to main</a>.";
            } else if ($userstats3['numturns'] < 1) {
                die("You must have at least 1 turn to buy land. Go back to <A href='index.php'>Main</a>");
            } else if ($userstats3['gold'] < $total) {
                print "You do not have enough cash to buy that many acres. <A href='index.php'>Go back to main</a>.";
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
                print "You bought $landacres acres of land. <A href='index.php'>Go back to main</a>.";
            }
            print "</td></tr></table>";

            print "<font size='1'>Script Produced by © <A href='http://www.chipmunk-scripts.com'>Chipmunk Scripts</a></font>";
        }
    } catch (PDOException $e) {
        error_log("Buy land error: " . $e->getMessage());
        die("An error occurred while purchasing land. Please try again.");
    }
} else {
    echo "<script>
window.location.href = 'login.php';
</script>";
}

include "down_html.php";

?>

