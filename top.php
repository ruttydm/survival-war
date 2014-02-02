<?php //module to display the top users
  session_start();
  include "up_html.php";
?>

<?php
  if(!isset($start))
  {
    $start=0;
  }
  $order="SELECT * from km_users";
  $order2=mysql_query($order);
  $d=0;
  $f=0;
  $g=1;
  print "<center>Page: ";
  while($order3=mysql_fetch_array($order2))
  {
    if($f%20==0)
    {
      print "<A href='top.php?start=$d'>$g</a> ";
      $g++;
    }
    $d=$d+1;
    $f++;
  }
  print "</center><center>Players by Rank<br>";
  print "<table class='maintable' border='1'><tr class='mainrow'><td>ID#</td><td>Player</td><td>Skill Points</td><td>Dead?</td></tr>";
  $topplayers="SELECT * from km_users order by skillpts DESC Limit $start, 20";
  $topplayers2=mysql_query($topplayers) or die("Could not query players");
  while($topplayer3=mysql_fetch_array($topplayers2))
  {
    $topplayer3[playername]=strip_tags($topplayer3[playername]);
    print "<tr><td>$topplayer3[ID]</td><td>$topplayer3[playername]</td><td>$topplayer3[skillpts]</td><td>$topplayer3[dead]</td></tr>";
  }
  print "</table>";
include "down_html.php";
?>

