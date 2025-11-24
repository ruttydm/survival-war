<?php
/**
 * Main Dashboard
 *
 * Displays user statistics and game options
 */

// Include header (which includes bootstrap)
include 'up_html.php';

// Check if user is logged in
if (isset($_SESSION['player'])) {
    try {
        $player = $_SESSION['player'];

        // Get user stats
        $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
        $stmt->execute(['player' => $player]);
        $userstats3 = $stmt->fetch();

        if ($userstats3) {
            // If user is dead
            if ($userstats3['dead'] == 'Yes') {
                ?>
                <p class='td_width_bold'>You have been slain by <?php echo Validator::escapeHtml($userstats3['killer']); ?></p>
                <form action='revive.php' method='post'>
                    <input type='hidden' name='ID' value='<?php echo Validator::escapeHtml($userstats3['ID']); ?>'>
                    <input type='submit' name='revives' value='revive' class='RedButton' style='width:80px'>
                </form>
                </div><br><br><br>
                <?php
            } else {
                // User is alive - reset justattacked flag
                $updateStmt = $db->prepare("UPDATE km_users SET justattacked = 0 WHERE ID = :id");
                $updateStmt->execute(['id' => $userstats3['ID']]);

                // Display index content
                ?>
                <p>Welcome, <?php echo Validator::escapeHtml($player); ?> into the world of survivors!</p>
                <table class='maintable'>
                    <tr class='headline'><td><center>Statistics</center></td></tr>
                    <tr class='mainrow'><td><b>Turns: <?php echo Validator::escapeHtml($userstats3['numturns']); ?></b></td></tr>
                    <tr class='mainrow'><td><b>skill pts: <?php echo Validator::escapeHtml($userstats3['skillpts']); ?></b></td></tr>
                    <tr class='mainrow'><td><b>Honor: <?php echo Validator::escapeHtml($userstats3['honor']); ?></b></td></tr>
                    <tr class='mainrow'><td><b>Gold: <?php echo Validator::escapeHtml($userstats3['gold']); ?></b></td></tr>
                    <tr class='mainrow'><td><b>Land: <?php echo Validator::escapeHtml($userstats3['land']); ?></b></td></tr>
                    <tr class='mainrow'><td><b>offensive army: <?php echo Validator::escapeHtml($userstats3['offarmy']); ?></b></td></tr>
                    <tr class='mainrow'><td><b>Defensive army: <?php echo Validator::escapeHtml($userstats3['dffarmy']); ?></b></td></tr>
                    <tr class='mainrow'><td><b>training pts: <?php echo Validator::escapeHtml($userstats3['science']); ?></b></td></tr>
                </table><br>

                <table class='maintable'>
                    <tr class='mainrow'><td><tr class='headline'><td><center>User settings:</center></td></tr>
                    <tr class="mainrow"><td><br><br><b><a href='../setpass.php'>Change password</a></b><br><br></td></tr>
                </table>
                <?php
            }
        } else {
            // User not found in database
            echo "<p>Error: User account not found.</p>";
        }
    } catch (PDOException $e) {
        error_log("Error in index.php: " . $e->getMessage());
        echo "<p>An error occurred. Please try again later.</p>";
    }
} else {
    // User not logged in - redirect to login page
    header("Location: login.php");
    exit;
}

// Include footer
include 'down_html.php';
?>
