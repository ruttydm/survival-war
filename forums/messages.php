<?php
include "../connect.php";
session_start();
include "../up_html.php";
?>

<link rel="stylesheet" href="../style.css" type="text/css">
<?php
if (isset($_SESSION['player'])) 
  {
    $player=$_SESSION['player'];
    $userstats="SELECT * from km_users where playername='$player'";
    $userstats2=mysql_query($userstats) or die("Could not get user stats");
    $userstats3=mysql_fetch_array($userstats2);
    if(isset($_GET['ID']))
    {
      $ID=$_GET['ID'];
      $getmessage="SELECT * from km_messages a, km_users b where b.ID=a.posterid and a.msgid='$ID'";
      $getmessage2=mysql_query($getmessage) or die(mysql_error());
      $getmessage3=mysql_fetch_array($getmessage2);
      $getforum="SELECT forumname from km_forums where forumID='$getmessage3[forumparent]'";
      $getforum2=mysql_query($getforum) or die("Could not get forum");
      $getforum3=mysql_fetch_array($getforum2);
      print "<p align='right'>";
      print "<center>";
      print "<table class='maintable'>";
      print "<tr class='headline'><td colspan='2'><center>Navigation</center></td></tr>";
      print "<tr class='mainrow'><td><A href='../index.php'>Kill Monster Main</a>--<A href='index.php'>Forum Main</a>--<A href='forum.php?ID=$getmessage3[forumparent]'>$getforum3[forumname]</a></td><td><p align='right'><A href='post.php?forumid=$getmessage3[forumparent]'>Post</a>--<A href='reply.php?forumid=$getmessage3[forumparent]&ID=$ID'>Reply</a></td></tr>";
      print "</table><br><br>";
      print "<table class='maintable'>";
      print "<tr class='headline'><td width=25%>Author</td><td width=75%><center>Post</center></td></tr>";
      print "<tr class='mainrow'><td width=25%><b>$getmessage3[playername]</b><br>";
      if($getmessage3[status]==3)
      {
         print "Administrator(Lord and Master)";
      }
      else
      {
         print "Member";
      }
      $getmessage3[message]=stripslashes($getmessage3[message]);
      $getmessage3[message]=strip_tags($getmessage3[message]);
      $getmessage3[message]=nl2br($getmessage3[message]);
      print "</td><td width=75%>Last replied to on $getmessage3[realtime]<br>";
      if($userstats3['ID']==$getmessage3['posterid'] || $userstats3[status]==3)
      {
        print "<A href='edit.php?ID=$getmessage3[msgid]'>Edit</a>-<A href='delete.php?ID=$getmessage3[msgid]'>Delete</a><br>";
      }
      print "<hr>$getmessage3[message]</td></tr>";
      $getreplies="SELECT * from km_messages a, km_users b where b.ID=a.posterid and a.parentid='$ID'";
      $getreplies2=mysql_query($getreplies) or die("Could not get replies");
      while ($getreplies3=mysql_fetch_array($getreplies2))
      {
         print "<tr class='mainrow'><td width=25%><b>$getreplies3[playername]</b><br>";
         if($getreplies3[status]==3)
         {
            print "Administrator(Lord and Master)";
         }
         else
         {
            print "Member";
         }
         $getreplies3[message]=stripslashes($getreplies3[message]);
         $getreplies3[message]=strip_tags($getreplies3[message]);
         $getreplies3[message]=nl2br($getreplies3[message]);
         print "</td><td width=75%>Posted on $getreplies3[realtime]<br>";
         if($userstats3['ID']==$getreplies3['posterid'] || $userstats3[status]==3)
         {
           print "<A href='edit.php?ID=$getreplies3[msgid]'>Edit</a>-<A href='delete.php?ID=$getreplies3[msgid]'>Delete</a>";
         }
         print "<hr>$getreplies3[message]</td></tr>";
       }
       print "</table>";
    }
      
   
  }
 

else
   {
     print "You are not logged in.";
   }
include "../down_html.php";
?> 