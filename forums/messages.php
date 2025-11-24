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
    $stmt = $pdo->prepare("SELECT * FROM km_users WHERE playername = ?");
    $stmt->execute([$player]);
    $userstats3 = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userstats3) {
      die("Could not get user stats");
    }

    if(isset($_GET['ID']))
    {
      $ID = filter_var($_GET['ID'], FILTER_VALIDATE_INT);
      if (!$ID) {
        die("Invalid message ID");
      }
      $stmt = $pdo->prepare("SELECT * FROM km_messages a, km_users b WHERE b.ID=a.posterid AND a.msgid = ?");
      $stmt->execute([$ID]);
      $getmessage3 = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$getmessage3) {
        die("Could not get message");
      }

      $stmt = $pdo->prepare("SELECT forumname FROM km_forums WHERE forumID = ?");
      $stmt->execute([$getmessage3['forumparent']]);
      $getforum3 = $stmt->fetch(PDO::FETCH_ASSOC);
      print "<p align='right'>";
      print "<center>";
      print "<table class='maintable'>";
      print "<tr class='headline'><td colspan='2'><center>Navigation</center></td></tr>";
      $forumparent_safe = htmlspecialchars($getmessage3['forumparent'], ENT_QUOTES, 'UTF-8');
      $forumname_safe = htmlspecialchars($getforum3['forumname'], ENT_QUOTES, 'UTF-8');
      $ID_safe = htmlspecialchars($ID, ENT_QUOTES, 'UTF-8');
      print "<tr class='mainrow'><td><A href='../index.php'>Kill Monster Main</a>--<A href='index.php'>Forum Main</a>--<A href='forum.php?ID=$forumparent_safe'>$forumname_safe</a></td><td><p align='right'><A href='post.php?forumid=$forumparent_safe'>Post</a>--<A href='reply.php?forumid=$forumparent_safe&ID=$ID_safe'>Reply</a></td></tr>";
      print "</table><br><br>";
      print "<table class='maintable'>";
      print "<tr class='headline'><td width=25%>Author</td><td width=75%><center>Post</center></td></tr>";
      $playername_safe = htmlspecialchars($getmessage3['playername'], ENT_QUOTES, 'UTF-8');
      print "<tr class='mainrow'><td width=25%><b>$playername_safe</b><br>";
      if($getmessage3['status']==3)
      {
         print "Administrator(Lord and Master)";
      }
      else
      {
         print "Member";
      }
      $getmessage3['message']=stripslashes($getmessage3['message']);
      $getmessage3['message']=strip_tags($getmessage3['message']);
      $getmessage3['message']=nl2br($getmessage3['message']);
      $message_safe = htmlspecialchars($getmessage3['message'], ENT_QUOTES, 'UTF-8');
      $realtime_safe = htmlspecialchars($getmessage3['realtime'], ENT_QUOTES, 'UTF-8');
      print "</td><td width=75%>Last replied to on $realtime_safe<br>";
      if($userstats3['ID']==$getmessage3['posterid'] || $userstats3['status']==3)
      {
        $msgid_safe = htmlspecialchars($getmessage3['msgid'], ENT_QUOTES, 'UTF-8');
        print "<A href='edit.php?ID=$msgid_safe'>Edit</a>-<A href='delete.php?ID=$msgid_safe'>Delete</a><br>";
      }
      print "<hr>$message_safe</td></tr>";
      $stmt = $pdo->prepare("SELECT * FROM km_messages a, km_users b WHERE b.ID=a.posterid AND a.parentid = ?");
      $stmt->execute([$ID]);
      while ($getreplies3 = $stmt->fetch(PDO::FETCH_ASSOC))
      {
         $replyplayername_safe = htmlspecialchars($getreplies3['playername'], ENT_QUOTES, 'UTF-8');
         print "<tr class='mainrow'><td width=25%><b>$replyplayername_safe</b><br>";
         if($getreplies3['status']==3)
         {
            print "Administrator(Lord and Master)";
         }
         else
         {
            print "Member";
         }
         $getreplies3['message']=stripslashes($getreplies3['message']);
         $getreplies3['message']=strip_tags($getreplies3['message']);
         $getreplies3['message']=nl2br($getreplies3['message']);
         $replymessage_safe = htmlspecialchars($getreplies3['message'], ENT_QUOTES, 'UTF-8');
         $replyrealtime_safe = htmlspecialchars($getreplies3['realtime'], ENT_QUOTES, 'UTF-8');
         print "</td><td width=75%>Posted on $replyrealtime_safe<br>";
         if($userstats3['ID']==$getreplies3['posterid'] || $userstats3['status']==3)
         {
           $replymsgid_safe = htmlspecialchars($getreplies3['msgid'], ENT_QUOTES, 'UTF-8');
           print "<A href='edit.php?ID=$replymsgid_safe'>Edit</a>-<A href='delete.php?ID=$replymsgid_safe'>Delete</a>";
         }
         print "<hr>$replymessage_safe</td></tr>";
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