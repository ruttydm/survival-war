<?php

//start session
require_once 'includes/bootstrap.php';

//include html
include 'up_html.php';

//if user is online
if(isset($_SESSION['player'])){

     //user stats
     $player=$_SESSION['player'];
     $userstats=$db->prepare("SELECT * from km_users where playername=:player");
     $userstats->execute(['player' => $player]);
     $userstats3=$userstats->fetch();

     if(!$userstats3) {
       die("Could not get user stats");
     }

	  //if there are attacks
      if($userstats3['numberattck']>0)
      {
        print "You have survived {$userstats3['numberattck']} attacks since your last login.<br><br>";
        $resets=$db->prepare("update km_users set numberattck='0' where playername=:player");
        $resets->execute(['player' => $player]);
      }

      //view table
      print "</td></tr></table><br><br>";
      $getbattlerecords=$db->query("SELECT * from km_battlerecords");
      print "<table class='maintable'>";
      print "<tr class='headline'><td colspan='4'><center>Battle records</center></td></tr>";
      print "<tr class='mainrow'><td>Attacker ID</td><td>Attacker name</td><td>Result</td><td>Land lost</td></tr>";
      while($getbattlerecords3=$getbattlerecords->fetch())
      {
         print "<tr class='mainrow'><td>{$getbattlerecords3['attid']}</td><td>{$getbattlerecords3['attname']}</td><td>{$getbattlerecords3['result']}</td><td>{$getbattlerecords3['landlost']}</td></tr>";
      }
      print "</table><br><br>";
}

//include html
include 'down_html.php'

?>