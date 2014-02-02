<?php
include "../connect.php";
session_start();
?>
<center>
<br><br>
<link rel="stylesheet" href="../style.css" type="text/css">
<?php
if (isset($_SESSION['player'])) 
  {
    $player=$_SESSION['player'];
    $userstats="SELECT * from km_users where playername='$player'";
    $userstats2=mysql_query($userstats) or die("Could not get user stats");
    $userstats3=mysql_fetch_array($userstats2);
    $forumid=$_GET['forumid'];
    print "<center>";
    print "<table class='maintable'>";
    print "<tr class='headline'><td><center>Post a Message</center></td></tr>";
    print "<tr class='mainrow'><td>";
    if(isset($_POST['submit']))
    {
      if(!isset($_POST['themessage']))
      {
         print "You did not put a message.";
      }
      else
      {
        $subject=$_POST['subject'];
        $themessage=$_POST['themessage'];
        $subject=addslashes($subject);
        $themessage=addslashes($themessage);
        $fid=$_POST['fid'];
        $unixtime=date("U");
        $ID=$_GET['ID'];
        $realtime=date("D M d, Y H:i:s");
        $subject=strip_tags($subject);
        $themessage=strip_tags($themessage);
        $inputmessage="Insert into km_messages (posterid,time,realtime,subject,message,parentid,forumparent) values('$userstats3[ID]','$unixtime','$realtime','$subject','$themessage','$ID','$fid')";
        mysql_query($inputmessage) or die("Could not input message");
        $updatemsg="update km_messages set realtime='$realtime', lastreplied='$userstats3[playername]', numreplies=numreplies+'1',time='$unixtime' where msgid='$ID'";
        mysql_query($updatemsg) or die(mysql_error());
        $updateforum="update km_forums set timelastpost='$realtime', lastposter='$userstats3[playername]',numposts=numposts+1,realtimelastpost='$unixtime' where forumID='$fid'";
        mysql_query($updateforum) or die("Could not update forum");
        print "Thanks for post, redirecting to main .... <META HTTP-EQUIV = 'Refresh' Content = '2; URL =messages.php?forumid=$fid&ID=$ID'>";
      }

    }
    else
    {
      if(!isset($_GET['ID']))
      {
         print "You did not specify a thread to reply to.";
      }
      else  
      {
        $ID=$_GET['ID'];
        print "<form action='reply.php?ID=$ID' method='post'>";
        print "<input type='hidden' name='fid' value='$forumid'>";
        print "Name: $userstats3[playername]<br><br>";
        print "Subject:<br>";
        print "<input type='text' name='subject' size='30'><br><br>";
        print "Message:<br>";
        print "<textarea name='themessage' rows='6' cols='45'></textarea><br><br>";
        print "<input type='submit' name='submit' value='submit'></form>";
      }

    }
    
  }
 

else
   {
     print "You are not logged in.";
   }
?> 