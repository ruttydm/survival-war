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
    print "<tr class='headline'><td><center>Post a Message</center></td></tr>";
    print "<tr class='mainrow'><td>";
    if(isset($_POST['submit']))
    {
       $ID=$_POST['msgid'];      
       $themessage=$_REQUEST['themessage'];
       $upmsg="Update km_messages set message='$themessage' where msgid='$ID'";
       mysql_query($upmsg) or die("COuld not update message");
       print "Message edited, please go back to the <A href='messages.php?ID=$ID'>Thread</a>";



    }
    else
    {
       $ID=$_GET['ID'];
       $getmessage="SELECT * from km_messages where msgid='$ID'";
       $getmessage2=mysql_query($getmessage) or die("Could not get message");
       $getmessage3=mysql_fetch_array($getmessage2);
       if($userstats3['ID']==$getmessage3['posterid'] || $userstats3[status]==3)
       {
          print "<form action='edit.php' method='post'>";
          print "<input type='hidden' name='msgid' value='$ID'>";
          print "Name: $userstats3[playername]<br><br>";
          print "<textarea name='themessage' rows='5' cols='40'>$getmessage3[message]</textarea><br>";
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