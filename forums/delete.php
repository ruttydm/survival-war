<?php
include "../connect.php";
session_start();
?>
<link rel="stylesheet" href="../style.css" type="text/css">
<?php
if (isset($_SESSION['player'])) 
  {
    $player=$_SESSION['player'];
    $userstats="SELECT * from km_users where playername='$player'";
    $userstats2=mysql_query($userstats) or die("Could not get user stats");
    $userstats3=mysql_fetch_array($userstats2);
    print "<center>";
    print "<table class='maintable'>";
    print "<tr class='headline'><td><center>Delete a Message</center></td></tr>";
    print "<tr class='mainrow'><td>";
    if(isset($_POST['submit']))
    {
       $ID=$_POST['msgid'];      
       $getmessage="SELECT * from km_messages where msgid='$ID'";
       $getmessage2=mysql_query($getmessage) or die("Could not get message");
       $getmessage3=mysql_fetch_array($getmessage2);
       if($getmessage3[parentid]==0)
       {
          $totalposts=$getmessage3[numreplies]+1;
          $nukereplies="DELETE from km_messages where msgid='$getmessage3[parentid]'";
          mysql_query($nukereplies) or die("Could not delete replies");
          $nukepost="DELETE from km_messages where msgid='$ID'";
          mysql_query($nukepost) or die("Could not delete post");
          $updateforum="Update km_forums set numposts=numposts-'$totalposts' where forumID='$getmessage3[forumparent]'";
          mysql_query($updateforum) or die("Could not update forum");
      }
      else
      {
          $nukepost="DELETE from km_messages where msgid='$ID'";
          mysql_query($nukepost) or die(mysql_error());
          $updatethread="Update km_messages set numreplies=numreplies-1 where msgid='$getmessage3[parentid]'";
          mysql_query($updatethread) or die("Could not update thread");
          $updateforum="Update km_forums set numposts=numposts-1 where forumID='$getmessage3[forumparent]'";
          mysql_query($updateforum) or die("Could not update forum");      
      }

       print "Message Deleted, please go back to the <A href='index.php'>Forum</a>";



    }
    else
    {
       $ID=$_GET['ID'];
       if($userstats3['ID']==$getmessage3['posterid'] || $userstats3[status]==3)
       {
          print "<form action='delete.php' method='post'>";
          print "<input type='hidden' name='msgid' value='$ID'>";
          print "Are you sure you want to delete this message?<br>";
          print "<input type='submit' name='submit' value='submit'></form>"; 

      
       }
       else
       {
         die("You cannot edit this");
       }


    }
    
  }
 

else
   {
     print "You are not logged in.";
   }
?> 