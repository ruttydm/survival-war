<?php
//killmonster amin index
require_once 'includes/bootstrap.php';
include 'up_html.php';
?>

<?php
if (isset($_SESSION['player']))
{
      print "<table class='maintable'>";
      print "<tr class='headline'><td><center>Select Player to challenge</center></td></tr>";
      print "<tr class='mainrow'><td>";
      print "<p>This is how challenging other player work. If you win, you gain 1/10 of the other playerr's skillpoints(so it makes no sense
to challenge someone with less than 10 skillpoints) and the other player dies and loses 1/3 of his skill pts and has to revive.
However, if you lose, he gains 1/10 of your skill points and you die and lose 1/3 of your skill pts.
</p><br>Type the ID# of the player you wish to challenge:<br>";
      print "<form method='post' action=''>";
      print "<input type='text' name='playerID' class='inline_text_inp' style='width:75px;'><br>";
      print "<input type='submit' name='submit2' value='challenge' class='RedButton' style='width:93px'></form>";
      print "</td></tr></table><br><br>";


  if(isset($_POST['submit2']))
  {
    try {
        $player = $_SESSION['player'];
        $datenow = time();
        $datenow = $datenow % 100000;
        srand($datenow);

        // Get your stats
        $yourstats = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
        $yourstats->execute(['player' => $player]);
        $yourstats3 = $yourstats->fetch(PDO::FETCH_ASSOC);

        if (!$yourstats3) {
            die("Could not get your stats");
        }

        $playerID = isset($_POST['playerID']) ? Validator::sanitizeInt($_POST['playerID'], 1) : null;

        if ($playerID === false || $playerID === null) {
            die("Invalid player ID. Please go back to <A href='index.php'>Main page</a>.");
        }

        // Get opponent stats
        $oppstats = $db->prepare("SELECT * FROM km_users WHERE ID = :playerid");
        $oppstats->execute(['playerid' => $playerID]);
        $oppstats3 = $oppstats->fetch(PDO::FETCH_ASSOC);

        if (!$oppstats3) {
            die("Could not get opponent's stats");
        }

        $statst = 2 * $oppstats3['skillpts'];

        if($yourstats3['numturns'] < 2)
        {
           die("You need at least 2 turns to challenge someone else. Please go back to <A href='index.php'>.");
        }
        else if($yourstats3['ID'] === $oppstats3['ID'])
        {
           die("You may not challenge yourself, go back to <A href='index.php'>main page</a>.");
        }
        else if($yourstats3['skillpts'] >= $statst)
        {
           die("You may not kill opponents who have less than half your skill points. Please go back to <A href='index.php'>Main page</a>.");
        }
        else if(!$oppstats3 || $oppstats3['dead'] == 'Yes')
        {
          print "There is no such player or the player is already dead, please go back to <A href='index.php'>Main game page</a>";
        }
        else
        {
          $totalstats = $oppstats3['skillpts'] + $yourstats3['skillpts'];
          $randomnumber = rand(1, $totalstats);

          if($randomnumber <= $yourstats3['skillpts'])
          {
            $ptsgained = $oppstats3['skillpts'] / 10;
            $ptslost = $oppstats3['skillpts'] / 3;
            print "<b>Congradulations, You have slain " . Validator::escapeHtml($oppstats3['playername']) . " and have gained " . Validator::escapeHtml($ptsgained) . " skill points.<br>";

            // Update your stats
            $updateyourstats = $db->prepare("UPDATE km_users SET skillpts = skillpts + :ptsgained, honor = honor + 1, numturns = numturns - 2 WHERE playername = :player");
            $updateyourstats->execute([
                'ptsgained' => $ptsgained,
                'player' => $player
            ]);

            // Update opponent (kill them)
            $updateopp = $db->prepare("UPDATE km_users SET skillpts = skillpts - :ptslost, dead = 'Yes', killer = :killer, numberattck = '0', honor = honor - 1 WHERE ID = :playerid");
            $updateopp->execute([
                'ptslost' => $ptslost,
                'killer' => $player,
                'playerid' => $playerID
            ]);

            print "Back to <A href='index.php'>Main game page</a>.";
          }
          else if($randomnumber > $yourstats3['skillpts'])
          {
            $ptsgained = $yourstats3['skillpts'] / 10;
            $ptslost = $yourstats3['skillpts'] / 3;
            print "You have lost the battle and have been slain, you will need to go to the <A href='index.php'>Main game page</a> and revive yourself.<br>";

            // Kill you
            $killyou = $db->prepare("UPDATE km_users SET skillpts = skillpts - :ptslost, dead = 'Yes', killer = :killer, honor = honor - 1, numturns = numturns - 2 WHERE playername = :player");
            $killyou->execute([
                'ptslost' => $ptslost,
                'killer' => $oppstats3['playername'],
                'player' => $player
            ]);

            // Update foe stats
            $foestats = $db->prepare("UPDATE km_users SET skillpts = skillpts + :ptsgained, numberattck = numberattck + 1, honor = honor + 1 WHERE ID = :playerid");
            $foestats->execute([
                'ptsgained' => $ptsgained,
                'playerid' => $playerID
            ]);
          }

       }
    } catch (PDOException $e) {
        ErrorHandler::handleException($e);
        die("A database error occurred. Please try again later.");
    } catch (Exception $e) {
        ErrorHandler::handleException($e);
        die("An error occurred. Please try again later.");
    }
  }

}

else
{
	print "You are not logged in, please <A href='login.php'>Login</a>";
}
include 'down_html.php';

?>
