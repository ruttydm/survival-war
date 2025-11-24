<?php
//killmonster amin index

require_once 'includes/bootstrap.php';
include 'up_html.php'
?>

      <table class='maintable'>
      <tr class='headline'><td><center>Select player's land to attack</center></td></tr>
      <tr class='mainrow'><td>
		  <p>Type the ID# of the player whose land you wish to attack:</p><br>
      <form action='' method='post'>
      <input type='text' name='victimid' class='inline_text_inp' style='width:75px;'><br>
      <input type='submit' name='submit' value='Attack' class='RedButton' style='width:93px'></form>
      </td></tr></table><br><br>


<?php
if (isset($_SESSION['player']))
  {
    try {
        $player = $_SESSION['player'];

        // Get user stats
        $userstats = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
        $userstats->execute(['player' => $player]);
        $userstats3 = $userstats->fetch(PDO::FETCH_ASSOC);

        if (!$userstats3) {
            die("Could not get user stats");
        }

        $victimid = isset($_POST['victimid']) ? Validator::sanitizeInt($_POST['victimid'], 1) : null;

        if ($victimid === false) {
            $victimid = null;
        }

        $victimselect3 = null;
        $attacklimit3 = 0;

        if ($victimid) {
            // Get victim stats
            $victimselect = $db->prepare("SELECT * FROM km_users WHERE ID = :victimid");
            $victimselect->execute(['victimid' => $victimid]);
            $victimselect3 = $victimselect->fetch(PDO::FETCH_ASSOC);

            if ($victimselect3) {
                $minlim = 2 * $victimselect3['land'];
                $thetime = time();
                $timelimit = $thetime - 3600 * 1;

                // Note: Original had typo "vicitimselect3[ID]" instead of "victimselect3[ID]"
                // Fixed to use correct variable
                $attacklimit = $db->prepare("SELECT COUNT(*) as attname FROM km_battlerecords WHERE ID = :victimid");
                $attacklimit->execute(['victimid' => $victimselect3['ID']]);
                $attacklimit_result = $attacklimit->fetch(PDO::FETCH_ASSOC);
                $attacklimit3 = $attacklimit_result['attname'];
            }
        }

        if(isset($_POST['submit']))
        {
            print "<center>";
            print "<table class='maintable'>";
            print "<tr class='headline'><td><center>result</center></td></tr>";
            print "<tr class='mainrow'><td>";

            if($userstats3['numturns'] < 3)
            {
               print "It takes 3 turns to do an attack on someone else's land, please go back to <A href='index.php'>Main</a>";
            }
            else if(!$victimselect3)
            {
                print "Invalid victim ID. Please go back to <A href='index.php'>Main</a>";
            }
            else if($userstats3['land'] > $minlim)
            {
                die("You cannot attack someone with less than half your land. Go back to <A href='index.php'>Main</a>.");
            }
            else if($userstats3['lastaction'] > $timelimit)
            {
                die("You have  attacked  within the last hour, you can only take one of these actions in each 1-hour period. <A href='index.php'>Back to Main</a>.");
            }
            else if($userstats3['ID'] == $victimselect3['ID'])
            {
                die("Of Course, you cannot attack yourself, <A href='index.php'>Back to Main</a>.");
            }
            else if($attacklimit3 >= 5)
            {
                die("That person has already been attacked 5 times since his last logon,  you may not attack him. Back to <A href='index.php'>Main</a>");
            }
            else if($victimselect3['land'] <= 0)
            {
                die("You cannot attack someone that has no land, back to <A href='index.php'>Main</a>");
            }
            else if($userstats3['land'] < 1)
            {
              die("You cannot attack someone if you have no land to attack from, back to <A href='index.php'>Main</a>");
            }
            else
            {
               // Update actions
               $updateactions = $db->prepare("UPDATE km_users SET lastaction = :thetime, numturns = numturns - 4 WHERE ID = :userid");
               $updateactions->execute([
                   'thetime' => $thetime,
                   'userid' => $userstats3['ID']
               ]);

               $attscimod = $userstats3['science'] / ($userstats3['land'] * 10) + 1;
               $dffscimod = $victimselect3['science'] / ($victimselect3['land'] * 10) + 1;

               if($userstats3['honor'] > 50)
               {
                   $ahonormod = 1.5;
               }
               else if($userstats3['honor'] > 25)
               {
                   $ahonormod = 1.3;
               }
               else  if($userstats3['honor'] > 10)
               {
                   $ahonormod = 1.1;
               }
               else
               {
                   $ahonormod = 1;
               }

               if($victimselect3['honor'] > 50)
               {
                   $dhonormod = 1.5;
               }
               else if($victimselect3['honor'] > 25)
               {
                   $dhonormod = 1.3;
               }
               else if($victimselect3['honor'] > 10)
               {
                   $dhonormod = 1.1;
               }
               else
               {
                   $dhonormod = 1;
               }

               $attstrength = $userstats3['offarmy'] * 5 * $attscimod * $ahonormod;
               $dffstrength = $victimselect3['dffarmy'] * 5 * $dffscimod * $dhonormod;
               $attloss = round($dffstrength / 30);
               $dffloss = round($attstrength / 40);

               if($attloss >= $userstats3['offarmy'])
               {
                  $attloss = $userstats3['offarmy'];
               }

               if($dffloss >= $victimselect3['dffarmy'])
               {
                  $dffloss = $victimselect3['dffarmy'];
               }

               if($attstrength > $dffstrength)
               {
                  $wonland = $victimselect3['land'] / 10;
                  $wonland = round($wonland);

                  // Insert battle record
                  $battlerecord = $db->prepare("INSERT INTO km_battlerecords (victimid, attid, attname, result, landlost) VALUES (:victimid, :attid, :attname, :result, :landlost)");
                  $battlerecord->execute([
                      'victimid' => $victimselect3['ID'],
                      'attid' => $userstats3['ID'],
                      'attname' => $userstats3['playername'],
                      'result' => 'lost',
                      'landlost' => $wonland
                  ]);

                  // Update attacker stats
                  $updatestats1 = $db->prepare("UPDATE km_users SET offarmy = offarmy - :attloss, land = land + :wonland WHERE ID = :userid");
                  $updatestats1->execute([
                      'attloss' => $attloss,
                      'wonland' => $wonland,
                      'userid' => $userstats3['ID']
                  ]);

                  // Update victim stats
                  $updatestats2 = $db->prepare("UPDATE km_users SET dffarmy = dffarmy - :dffloss, land = land - :wonland WHERE ID = :victimid");
                  $updatestats2->execute([
                      'dffloss' => $dffloss,
                      'wonland' => $wonland,
                      'victimid' => $victimselect3['ID']
                  ]);

                  print "You have won the battle and killed " . Validator::escapeHtml($dffloss) . " of the enemy's troops while losing " . Validator::escapeHtml($attloss) . " troops and gained " . Validator::escapeHtml($wonland) . " acres of land. <A href='index.php'>Back to Main</a><br>.";
               }
               else
               {
                  // Update attacker stats
                  $updatestats1 = $db->prepare("UPDATE km_users SET offarmy = offarmy - :attloss WHERE ID = :userid");
                  $updatestats1->execute([
                      'attloss' => $attloss,
                      'userid' => $userstats3['ID']
                  ]);

                  // Update victim stats
                  $updatestats2 = $db->prepare("UPDATE km_users SET dffarmy = dffarmy - :dffloss WHERE ID = :victimid");
                  $updatestats2->execute([
                      'dffloss' => $dffloss,
                      'victimid' => $victimselect3['ID']
                  ]);

                  // Insert battle record
                  $battlerecord = $db->prepare("INSERT INTO km_battlerecords (victimid, attid, attname, result, landlost) VALUES (:victimid, :attid, :attname, :result, :landlost)");
                  $battlerecord->execute([
                      'victimid' => $victimselect3['ID'],
                      'attid' => $userstats3['ID'],
                      'attname' => $userstats3['playername'],
                      'result' => 'won',
                      'landlost' => 0
                  ]);

                  print "Your attack was unsuccessful and you lost " . Validator::escapeHtml($attloss) . " while killing " . Validator::escapeHtml($dffloss) . " of the enemy troops, back to <A href='index.php'>Main</a><br>";
               }

            }




            print "</td></tr></table>";



        }
    } catch (PDOException $e) {
        ErrorHandler::handleException($e);
        die("A database error occurred. Please try again later.");
    } catch (Exception $e) {
        ErrorHandler::handleException($e);
        die("An error occurred. Please try again later.");
    }
  }
else
  {
    print "Sorry, not logged in  please <A href='login.php'>Login</a><br>";

  }



include 'down_html.php';

?>

