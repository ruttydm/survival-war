<?php
//killmonster admin main page, from here you can add and delete monsters
include "connect.php";
session_start();
?>
<?
if (isset($_SESSION['isadmin'])) //if there is an administrative session
  {
    $ID=$_GET['ID'];
    if(isset($ID)) //if a specific monster ID is specified
    {
      print "<center><h3>Kill Monster Admin</h3></center><br>";
      print "<center>";
      print "<table border='0' width='70%' cellspacing='20'>";
      print "<tr><td width='25%' valign='top'>";
      include 'left.php';
      print "</td>";
      print "<td valign='top' width='75%'>";
      $delmonster1="DELETE from km_monsters where ID='$ID'";
      mysql_query($delmonster1) or die("Could not delete monster");
      print "Monster deleted Successfully";     
      print "</td></tr></table>";    
      print "</center>";
    }
    else
    {
      print "<center><h3>Kill Monster Admin</h3></center><br>";
      print "<center>";
      print "<table border='0' width='70%' cellspacing='20'>";
      print "<tr><td width='25%' valign='top'>";
      include 'left.php';
      print "</td>";
      print "<td valign='top' width='75%'>";
      $selectmonster="SELECT * from km_monsters order by skill ASC";
      $selectmonster2=mysql_query($selectmonster) or die("could not select monster");
      print "<table border='1' bordercolor='white' bgcolor='#e1e1e1'>";
	  print "<tr><td>Monster name</td><td>Skill Points</td><td>Points if killed</td><td>Energy cost</td><td>Goldworth</td><td>Delete?</td></tr>";
      while($selectmonster3=mysql_fetch_array($selectmonster2))
      {
       print "<tr><td>$selectmonster3[name]</td><td>$selectmonster3[skill]</td><td>$selectmonster3[pointsifkilled]</td><td>$selectmonster3[energycost]</td><td>$selectmonster3[goldworth]</td><td><A href='deletemonster.php?ID=$selectmonster3[ID]'>Delete</a></td></tr>";

      }
      print "</table>";
      print "</td></tr></table>";    
      print "</center>";
    }
     
  }
else //if not logged in as admin
  {
    print "Sorry, not logged in as administrator, please <A href='login.php'>Login</a>";
  }

?>