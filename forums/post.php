<?php
include "../connect.php";
session_start();
?>
<center>
</center><br><br>
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
      if(strlen($_POST['subject'])<1)
      {
         print "There is not subject.";
      }
      else if(!isset($_POST['themessage']))
      {
         print "There is no message.";
      }
      else
      {
        $subject=$_POST['subject'];
        $fid=$_POST['fid'];
        $themessage=$_POST['themessage'];
        $subject=addslashes($subject);
        $themessage=addslashes($themessage);
        $unixtime=date("U");
        $realtime=date("D M d, Y H:i:s");
        $subject=strip_tags($subject);
        $themessage=strip_tags($themessage);
        $inputmessage="Insert into km_messages (posterid,time,realtime,subject,message,forumparent) values('$userstats3[ID]','$unixtime','$realtime','$subject','$themessage','$fid')";
        mysql_query($inputmessage) or die("Could not input message");
        $updateforum="update km_forums set timelastpost='$realtime', lastposter='$userstats3[playername]',numposts=numposts+1,numtopics=numtopics+1,realtimelastpost='$unixtime' where forumID='$fid'";
        mysql_query($updateforum) or die("Could not update forum");
        print "Thanks for post, redirecting to main .... <META HTTP-EQUIV = 'Refresh' Content = '2; URL =forum.php?ID=$fid'>";
      }

    }
    else
    {
      print "<form action='post.php' method='post'>";
      print "<input type='hidden' name='fid' value='$forumid'>";
      print "Name: $userstats3[playername]<br><br>";
      print "Subject:<br>";
      print "<input type='text' name='subject' size='30'><br><br>";
      print "Message:<br>";
      print "<textarea name='themessage' rows='6' cols='45'></textarea><br><br>";
      print "<input type='submit' name='submit' value='submit'></form>";

    }
    
  }
 

else
   {
     print "You are not logged in.";
   }
?> 