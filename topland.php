<?php //module to display the top users
require_once 'includes/bootstrap.php';
  include "up_html.php";
  if(!isset($start))
  {
    $start=0;
  }
  $order="SELECT * from km_users";
  $order2=$db->query($order);
  $d=0;
  $f=0;
  $g=1;
  print "<center>Page: ";
  while($order3=$order2->fetch())
  {
    if($f%20==0)
    {
      print "<A href='topland.php?start=$d'>$g</a> ";
      $g++;
    }
    $d=$d+1;
    $f++;
  }
  print "</center><center>Players by Rank<br>";
  print "<table class='maintable' border='1'><tr class='mainrow'><td>ID#</td><td>Player</td><td>Land</td></tr>";
  $topplayers=$db->prepare("SELECT * from km_users order by land DESC Limit :start, 20");
  $topplayers->execute(['start' => (int)$start]);
  while($topplayer3=$topplayers->fetch())
  {
    $topplayer3['playername']=strip_tags($topplayer3['playername']);
    print "<tr><td>{$topplayer3['ID']}</td><td>{$topplayer3['playername']}</td><td>{$topplayer3['land']}</td></tr>";
  }
  print "</table>";
include "down_html.php";
?>


