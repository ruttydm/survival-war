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
        $title=$_POST['title'];
        $description=$_POST['description'];
        if(strlen($title)<1)
        {
          print "You did not enter a title.";
        }
        else
        {
           $updateforum="Update km_forums set forumname='$title',descrip='$description' where forumID='$ID'";
           mysql_query($updateforum) or die("Could not update forum");
           print "Forum updated.";
        }

     }
     else
     {    
        $ID=$_GET['ID'];
        $getforum="SELECT * from km_forums where forumID='$ID'";
        $getforum2=mysql_query($getforum) or die("Could not get forum");
        $getforum3=mysql_fetch_array($getforum2);
        print "<form action='edit.php' method='post'>";
        print "<input type='hidden' name='ID' value='$ID'>";
        print "Title:<br>";
        print "<input type='text' name='title' size='20' value='$getforum3[forumname]'><br>";
        print "Description:<br>";
        print "<textarea name='description' rows='5' cols='40'>$getforum3[descrip]</textarea><br>";
        print "<input type='submit' name='submit' value='submit'></form>";

     }

     print "</td></tr></table>";    
     print "</center>";
     
  }
else //if not logged in as admin
  {
    print "Sorry, not logged in as administrator, please <A href='login.php'>Login</a>";
  }

?>