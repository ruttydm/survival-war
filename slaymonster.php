<?php

//include connect
require_once 'includes/bootstrap.php';

//include html
include "up_html.php";

if (isset($_SESSION['player']))
   {
    try {
        $player = $_SESSION['player'];

        // Get player stats
        $playerstats1 = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
        $playerstats1->execute(['player' => $player]);
        $playerstats3 = $playerstats1->fetch(PDO::FETCH_ASSOC);

        if (!$playerstats3) {
            die("Could not find player");
        }

        $monstername = isset($_POST['monstername']) ? Validator::sanitizeString($_POST['monstername'], 50) : null;

        $selmonster3 = null;
        $energy0 = 0;
        $energycost = 0;

        if ($monstername) {
            // Get selected monster
            $selmonster = $db->prepare("SELECT * FROM km_monsters WHERE name = :monstername");
            $selmonster->execute(['monstername' => $monstername]);
            $selmonster3 = $selmonster->fetch(PDO::FETCH_ASSOC);

            if ($selmonster3) {
                $energy0 = ($playerstats3['numturns'] - $selmonster3['energycost']);
                $energycost = $selmonster3['energycost'];
            }
        }

        //if sumbit was pressed
        if(isset($_POST['submit']))
        {

         //if the player's turns is empty
             if($energy0 < 0)
             {
                 echo "<div id='dialog'><h2><strong><center>You have not enough turns!</center></strong></h2></div>";
             }

             //if the player's energy isn't empty
             else
             {
             //if the animal's not exist
             if (!$selmonster3)
             {
               print "There is not a Animal of that name";
             }

             //if it exist
             else
             {
               $totalskill = $playerstats3['skillpts'] + $selmonster3['skill'];
               $randomnumber = rand(1, $totalskill);

               //if the player won
               if($randomnumber <= $playerstats3['skillpts'])
               {
                 $gained = $selmonster3['pointsifkilled'];
                 $gold = $selmonster3['goldworth'];

                 // Update player stats
                 $updateplayerstats = $db->prepare("UPDATE km_users SET skillpts = skillpts + :gained, gold = gold + :gold, numturns = numturns - :energycost WHERE playername = :player");
                 $updateplayerstats->execute([
                     'gained' => $gained,
                     'gold' => $gold,
                     'energycost' => $energycost,
                     'player' => $player
                 ]);

                 print "<div id='dialog' title='Won!'><center><img src='images/knight.gif'></center>";
                 print "You have succesfully hunted the " . Validator::escapeHtml($selmonster3['name']) . " and you have gained " . Validator::escapeHtml($gained) . " skillpts</div>";
               }

               //if the player lose
               else
               {
                 // Update player stats (lose scenario)
                 // Note: Original code has $gained variable used here but it's not set in lose condition
                 // Preserving original logic even though $gained is undefined in this branch
                 $gained = 0; // Initialize to avoid undefined variable
                 $updateplayerstats = $db->prepare("UPDATE km_users SET numturns = numturns - :energycost, skillpts = skillpts + :gained WHERE playername = :player");
                 $updateplayerstats->execute([
                     'energycost' => $energycost,
                     'gained' => $gained,
                     'player' => $player
                 ]);

                 print "<div id='dialog' title='Lose!'><center><img src='images/defeat.gif'></center>";
                 print "You failed to hunt the " . Validator::escapeHtml($selmonster3['name']) . "!<br><br></div>";
               }
             }

             }

       }

          print "<table class='maintable'>";
          print "<tr class='headline'><td><center>Select Monster to Slay</center></td></tr>";
          print "<tr class='mainrow'><td>";
          print "<form method='post' action=''>";

          // Get all monsters
          $monster1 = $db->prepare("SELECT * FROM km_monsters ORDER BY skill ASC");
          $monster1->execute();

          while ($monster3 = $monster1->fetch(PDO::FETCH_ASSOC))
          {
              $totalskill = $playerstats3['skillpts'] + $monster3['skill'];
              $chance = ($playerstats3['skillpts'] / $totalskill * 100);
              $chance2 = round($chance, 0, PHP_ROUND_HALF_UP);
              print "<img src='" . Validator::escapeHtml($monster3['image']) . "' height='100'><br><INPUT TYPE = 'radio' NAME = 'monstername' VALUE = " . Validator::escapeHtml($monster3['name']) . " CHECKED><B>" . Validator::escapeHtml($monster3['name']) . "</B><br><p>" . Validator::escapeHtml($chance2) . "% chance,
          reward:" . Validator::escapeHtml($monster3['goldworth']) . " gold and skillpoints, you lose:" . Validator::escapeHtml($monster3['energycost']) . "% energy</p><hr>";
          }

          print "<br>";
          print "<input type='submit' name='submit' value='Hunt Animal'></form>";
          print "</td></tr></table><br>";

    } catch (PDOException $e) {
        ErrorHandler::handleException($e);
        die("A database error occurred. Please try again later.");
    } catch (Exception $e) {
        ErrorHandler::handleException($e);
        die("An error occurred. Please try again later.");
    }
}

else //not logged in
   {

    print "You are not logged in, please <A href='login.php'>Login</a>";

   }


include "down_html.php";
?>
