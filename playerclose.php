<?php //lists all users close in rank to your range
//killmonster main index
require_once 'includes/bootstrap.php';
include 'up_html.php';

?>

<?php
if (isset($_SESSION['player']))
{
  $player=$_SESSION['player'];
  $getplayerpoints=$db->prepare("SELECT * from km_users where playername=:player");
  $getplayerpoints->execute(['player' => $player]);
  $getplayerpoints3=$getplayerpoints->fetch();

  if(!$getplayerpoints3) {
    die("Could not get player points");
  }

  $numrows=$db->prepare("SELECT COUNT(*) as count from km_users where skillpts>=:skillpts");
  $numrows->execute(['skillpts' => $getplayerpoints3['skillpts']]);
  $numrows3=$numrows->fetch()['count'];

  $total=$db->query("SELECT COUNT(*) as count from km_users");
  $total3=$total->fetch()['count'];

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
  $getrank=$db->prepare("SELECT * from km_users order by skillpts desc limit :offset,:limit");
  $getrank->execute(['offset' => (int)$numrows5, 'limit' => (int)$numrows4]);
  print "<table class='maintable' border='1'><tr class='mainrow'>";
  print "<td>Player ID</td><td>Playername</td><td>Skillpts</td><td>Dead?</td></tr>";
  while($getrank3=$getrank->fetch())
  {
    if($getplayerpoints3['ID']==$getrank3['ID'])
    {
      print "<tr><td><font color='red'>{$getrank3['ID']}</font></td><td><font color='red'>{$getrank3['playername']}</font></td><td><font color='red'>{$getrank3['skillpts']}</font></td><td>{$getrank3['dead']}</td></tr>";
    }
    else
    {
      print "<tr><td>{$getrank3['ID']}</td><td>{$getrank3['playername']}</td><td>{$getrank3['skillpts']}</td><td>{$getrank3['dead']}</td></tr>";
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
  
 
