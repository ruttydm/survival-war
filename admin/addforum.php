<?php
//killmonster admin main page, from here you can add and delete monsters
include "connect.php";
session_start();
?>
<?

if (isset($_SESSION['isadmin'])) //if there is an administrative session
  {

     if(isset($_POST['submit'])) //if submit has been pushed to create a monster
     {
       print "<center><h3>Add Forums</h3></center><br>";
       print "<center>";
       print "<table border='0' width='70%' cellspacing='20'>";
       print "<tr><td width='25%' valign='top'>";
       include 'left.php';
       print "</td>";
       print "<td valign='top' width='75%'>";
       $forumname=$_POST['forumname'];
       $forumdesc=$_POST['forumdesc'];
       if(strlen($forumname)<1)
       {
          print "You did not enter a forum name.";
       }
       else
       {
          $mkforum="INSERT into km_forums(forumname,descrip) values('$forumname','$forumdesc')";
          mysql_query($mkforum) or die("Could not create forum");
          print "Forum created.<br>";
       }       


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
       print "<form action='addforum.php' method='post'>";
       print "Type name of forum to add:<br>";
       print "<input type='text' name='forumname' size='20'><br>";
       print "Enter a forum description:<br>";
       print "<textarea name='forumdesc' cols='40' rows='5'></textarea><br>";
       print "<input type='submit' name='submit' value='submit'></form>";
       print "</td></tr></table>";    
       print "</center>";
     }
  }
else //if not logged in as admin
  {
    print "Sorry, not logged in as administrator, please <A href='login.php'>Login</a>";
  }

?>