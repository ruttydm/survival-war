<?php

//start sessionj
session_start();

//include html
include 'up_html.php';

//if user is online
if(isset($_SESSION['player'])){

     //user stats
     $player=$_SESSION['player'];
     $userstats="SELECT * from km_users where playername='$player'";
     $userstats2=mysql_query($userstats) or die("Could not get user stats");
     $userstats3=mysql_fetch_array($userstats2);
	
	  //if there are attacks
      if($userstats3[numberattck]>0)
      {
        print "You have survived $userstats3[numberattck] attacks since your last login.<br><br>";
        $resets="update km_users set numberattck='0' where playername='$player'";
        mysql_query($resets) or die("could not query");
      }
      
      //view table
      print "</td></tr></table><br><br>";
      $getbattlerecords="SELECT * from km_battlerecords";
      $getbattlerecords2=mysql_query($getbattlerecords) or die("Could not get battlerecords");
      print "<table class='maintable'>";
      print "<tr class='headline'><td colspan='4'><center>Battle records</center></td></tr>";
      print "<tr class='mainrow'><td>Attacker ID</td><td>Attacker name</td><td>Result</td><td>Land lost</td></tr>";
      while($getbattlerecords3=mysql_fetch_array($getbattlerecords2))
      {
         print "<tr class='mainrow'><td>$getbattlerecords3[attid]</td><td>$getbattlerecords3[attname]</td><td>$getbattlerecords3[result]</td><td>$getbattlerecords3[landlost]</td></tr>";
      }
      print "</table><br><br>";	
}

//include html
include 'down_html.php'

?>