<?php
//killmonster admin main page, from here you can add and delete monsters
include "connect.php";
session_start();
?>
<?

if (isset($_SESSION['isadmin'])) //if there is an administrative session
  {
     print "<center><h3>Kill Monster Admin</h3></center><br>";
     print "<center>";
     print "<table border='0' width='70%' cellspacing='20'>";
     print "<tr><td width='25%' valign='top'>";
     include 'left.php';
     print "</td>";
     print "<td valign='top' width='75%'>";
     if(isset($_POST['submit']))
     {
        $ID=$_POST['ID'];
        $delforum="Delete from km_forums where forumID='$ID'";
        mysql_query($delforum) or die("Could not delete forum");
        print "Forum deleted.";

     }
     else
     {    
        $ID=$_GET['ID'];
        print "<form action='delete.php' method='post'>";
        print "<input type='hidden' name='ID' value='$ID'>";
        print "Are you sure you want to delete this forum?<br>";
        print "<input type='submit' name='submit' value='Delete'></form>";

     }

     print "</td></tr></table>";    
     print "</center>";
     
  }
else //if not logged in as admin
  {
    print "Sorry, not logged in as administrator, please <A href='login.php'>Login</a>";
  }

?>