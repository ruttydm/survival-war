<?php
include "../connect.php";
session_start();
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

    print "<center>";
    print "<table class='maintable'>";
    print "<tr class='headline'><td><center>Delete a Message</center></td></tr>";
    print "<tr class='mainrow'><td>";
    if(isset($_POST['submit']))
    {
       $ID = filter_var($_POST['msgid'], FILTER_VALIDATE_INT);
       if (!$ID) {
         die("Invalid message ID");
       }
       $stmt = $pdo->prepare("SELECT * FROM km_messages WHERE msgid = ?");
       $stmt->execute([$ID]);
       $getmessage3 = $stmt->fetch(PDO::FETCH_ASSOC);

       if (!$getmessage3) {
         die("Could not get message");
       }

       if($getmessage3['parentid']==0)
       {
          $totalposts=$getmessage3['numreplies']+1;
          $stmt = $pdo->prepare("DELETE FROM km_messages WHERE parentid = ?");
          $stmt->execute([$ID]);
          $stmt = $pdo->prepare("DELETE FROM km_messages WHERE msgid = ?");
          $stmt->execute([$ID]);
          $stmt = $pdo->prepare("UPDATE km_forums SET numposts = numposts - ?, numtopics = numtopics - 1 WHERE forumID = ?");
          $stmt->execute([$totalposts, $getmessage3['forumparent']]);
      }
      else
      {
          $stmt = $pdo->prepare("DELETE FROM km_messages WHERE msgid = ?");
          $stmt->execute([$ID]);
          $stmt = $pdo->prepare("UPDATE km_messages SET numreplies = numreplies - 1 WHERE msgid = ?");
          $stmt->execute([$getmessage3['parentid']]);
          $stmt = $pdo->prepare("UPDATE km_forums SET numposts = numposts - 1 WHERE forumID = ?");
          $stmt->execute([$getmessage3['forumparent']]);
      }

       print "Message Deleted, please go back to the <A href='index.php'>Forum</a>";



    }
    else
    {
       $ID = filter_var($_GET['ID'], FILTER_VALIDATE_INT);
       if (!$ID) {
         die("Invalid message ID");
       }
       $stmt = $pdo->prepare("SELECT * FROM km_messages WHERE msgid = ?");
       $stmt->execute([$ID]);
       $getmessage3 = $stmt->fetch(PDO::FETCH_ASSOC);

       if (!$getmessage3) {
         die("Message not found");
       }

       if($userstats3['ID']==$getmessage3['posterid'] || $userstats3['status']==3)
       {
          print "<form action='delete.php' method='post'>";
          print "<input type='hidden' name='msgid' value='".htmlspecialchars($ID, ENT_QUOTES, 'UTF-8')."'>";
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