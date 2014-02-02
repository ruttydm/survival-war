<?php //lists all users close in rank to your range
//killmonster main index
session_start();
include 'up_html.php';

?>

<?php
if (isset($_SESSION['player'])) 
{
  $player=$_SESSION['player'];
  $getplayerpoints="SELECT * from km_users where playername='$player'";
  $getplayerpoints2=mysql_query($getplayerpoints) or die("Could not get player points");
  $getplayerpoints3=mysql_fetch_array($getplayerpoints2);
  $numrows="SELECT * from km_users where skillpts>='$getplayerpoints3[skillpts]'";
  $numrows2=mysql_query($numrows) or die("Could not grab rows");
  $numrows3=mysql_num_rows($numrows2);
  $total="SELECT * from km_users";
  $total2=mysql_query($total) or die("Could not get users");
  $total3=mysql_num_rows($total2);
  $numrows4=$numrows3+20;
  if($numrows4>=$total3)
  {
    $numrows4=$total3;
  }
  $numrows5=$numrows3-20;
  if($numrows5<0)
  {
    $numrows5=0;
  }
  print "<center>Players close to you in rank, your name is in red";
  $getrank="SELECT * from km_users order by skillpts desc limit $numrows5,$numrows4";
  $getrank2=mysql_query($getrank) or die("Could not fetch ranks");
  print "<table class='maintable' border='1'><tr class='mainrow'>";
  print "<td>Player ID</td><td>Playername</td><td>Skillpts</td><td>Dead?</td></tr>";
  while($getrank3=mysql_fetch_array($getrank2))
  {
    if($getplayerpoints3[ID]==$getrank3[ID])
    {
      print "<tr><td><font color='red'>$getrank3[ID]</font></td><td><font color='red'>$getrank3[playername]</font></td><td><font color='red'>$getrank3[skillpts]</font></td><td>$getrank3[dead]</td></tr>";
    }
    else
    {
      print "<tr><td>$getrank3[ID]</td><td>$getrank3[playername]</td><td>$getrank3[skillpts]</td><td>$getrank3[dead]</td></tr>";
    }
  }
  print "</table>";
}

else
{
  print "Not Logged in";
}
include "down_html.php";
?>
  
 
