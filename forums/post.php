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
    $stmt = $pdo->prepare("SELECT * FROM km_users WHERE playername = ?");
    $stmt->execute([$player]);
    $userstats3 = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userstats3) {
      die("Could not get user stats");
    }

    $forumid = filter_var($_GET['forumid'], FILTER_VALIDATE_INT);
    if (!$forumid) {
      die("Invalid forum ID");
    }

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
        $fid = filter_var($_POST['fid'], FILTER_VALIDATE_INT);
        if (!$fid) {
          die("Invalid forum ID");
        }
        $themessage=$_POST['themessage'];
        $unixtime=date("U");
        $realtime=date("D M d, Y H:i:s");
        $subject=strip_tags($subject);
        $themessage=strip_tags($themessage);
        $stmt = $pdo->prepare("INSERT INTO km_messages (posterid, time, realtime, subject, message, forumparent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userstats3['ID'], $unixtime, $realtime, $subject, $themessage, $fid]);
        $stmt = $pdo->prepare("UPDATE km_forums SET timelastpost = ?, lastposter = ?, numposts = numposts+1, numtopics = numtopics+1, realtimelastpost = ? WHERE forumID = ?");
        $stmt->execute([$realtime, $userstats3['playername'], $unixtime, $fid]);
        print "Thanks for post, redirecting to main .... <META HTTP-EQUIV = 'Refresh' Content = '2; URL =forum.php?ID=".htmlspecialchars($fid, ENT_QUOTES, 'UTF-8')."'>";
      }

    }
    else
    {
      print "<form action='post.php' method='post'>";
      print "<input type='hidden' name='fid' value='".htmlspecialchars($forumid, ENT_QUOTES, 'UTF-8')."'>";
      print "Name: ".htmlspecialchars($userstats3['playername'], ENT_QUOTES, 'UTF-8')."<br><br>";
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