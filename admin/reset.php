<?php
//killmonster admin main page, from here you can add and delete monsters
include "connect.php";
session_start();
?>
<?php
if (isset($_SESSION['isadmin'])) //if there is an administrative session
  {
 
    if(isset($_POST['submit'])) //if submit button has been pressed
    {
      print "<center><h3>Kill Monster Admin</h3></center><br>";
      print "<center>";
      print "<table border='0' width='70%' cellspacing='20'>";
      print "<tr><td width='25%' valign='top'>";
      include 'left.php';
      print "</td>";
      print "<td valign='top' width='75%'>";
      $resetgame="Delete from km_users";
      mysql_query($resetgame) or die("Could not reset game");
      print "Game reset successfully";    
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
      print "<center><form action='reset.php' method='post'>";
      print "<input type='submit' name='submit' value='Reset'></form>";
      print "<br><br>Warning, resetting will delete all players and skills and they will have to register again. This basically begins a new round in the game.<br>";
      print "</td></tr></table>";    
      print "</center>";
    }
     
  }
else //if not logged in as admin
  {
    print "Sorry, not logged in as administrator, please <A href='login.php'>Login</a>";
  }

?>