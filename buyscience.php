<?php
/**
 * Buy Science Points
 *
 * Allows players to purchase science/training points that provide power bonuses.
 * Each science point costs 35 gold and provides percentage-based power increase.
 */

require_once 'includes/bootstrap.php';
include "up_html.php";

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

        print "<table class='maintable'><tr class='headline'><td><center>Buy army Training Pts</center></td></tr>";
        print "<tr class='mainrow'><td>";
        print "Each science point Cost 35 gold.<br>";

        $max = $userstats3['land'] * 10;
        if ($userstats3['land'] <= 0) {
            print "Science not applicable due to lack of land.";
        } else {
            $percent = $userstats3['science'] / $max * 100;
            print "You have: {$userstats3['science']} pts ($percent % more power.)";
        }

        print "<form action='' method='post'>";
        print "<input type='text' name='scipts' size='6' class='inline_text_inp' style='width:75px;'><br>";
        print "<input type='submit' name='submit' value='submit' class='RedButton' style='width:93px'></form>";
        print "</td></tr></table>";
        print "</td>";

        if (isset($_POST['submit'])) {
            // Sanitize and validate inputs
            $scipts = Validator::sanitizeInt($_POST['scipts'] ?? 0);
            $totalcost = $scipts * 35;

            print "<br><center>";
            print "<table class='maintable'>";
            print "<tr class='headline'><td><center>Buy Science</center></td></tr>";
            print "<tr class='mainrow'><td>";

            if ($scipts < 0) {
                print "You cannot buy negative science. Back to <A href='index.php'>Main</a>";
            } else if ($userstats3['numturns'] < 1) {
                die("You must have at least 1 turn to buy Science. Go back to <A href='index.php'>Main</a>");
            } else if ($totalcost > $userstats3['gold']) {
                die("You do not have enough gold. Please go back to <A href='index.php'>Main</a>");
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
                print "You have bought $scipts science points. Back to<A href='index.php'>Main Page</a>";
            }
            print "</td></tr></table>";
        }
    } catch (PDOException $e) {
        error_log("Buy science error: " . $e->getMessage());
        die("An error occurred while purchasing science points. Please try again.");
    }
} else {
    echo "<script>
window.location.href = 'login.php';
</script>";
}

include "down_html.php";
?>

