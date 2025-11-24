<?php
//killmonster amin index
include '../connect.php';
session_start();
include "../up_html.php";
?>
<center>


<link rel="stylesheet" href="../style.css" type="text/css">
<?php
if (isset($_SESSION['player']))
{
  $playername=$_SESSION['player'];
  $stmt = $pdo->prepare("SELECT * FROM km_users WHERE playername = ?");
  $stmt->execute([$playername]);
  $getuser3 = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$getuser3) {
    die("Could not get user info");
  }

  $thedate=date("U");
  $checktime=$thedate-200;
  $stmt = $pdo->prepare("UPDATE km_users SET lasttime = ? WHERE ID = ?");
  $stmt->execute([$thedate, $getuser3['ID']]);

  if($getuser3['tsgone']<$checktime)
  {
    $stmt = $pdo->prepare("UPDATE km_users SET tsgone = ?, oldtime = ? WHERE ID = ?");
    $stmt->execute([$thedate, $getuser3['tsgone'], $getuser3['ID']]);
  }
    $numtopicsperpage=15;
    $forumID=$_GET['ID'];
    $forumID = filter_var($forumID, FILTER_VALIDATE_INT);
    if (!$forumID) {
      die("Invalid forum ID");
    }
    print "<table border='0' width=90%>";
    print "<tr><td><p align='left'><A href='../index.php'>Back to main game</a>-<A href='index.php'>Back to forum index</a></p></td><td colspan='3'><p align='right'><A href='post.php?forumid=".htmlspecialchars($forumID, ENT_QUOTES, 'UTF-8')."'><b>New Thread</b></a></td></tr></table><br>";
    print "<table class='maintable'>";
    print "<tr class='headline'><td colspan='2'>Topic</td><td>Topic Starter</td><td>Replies</td><td>Last Post</td></tr>";
    if(!isset($_GET['start']))
    {
       $start=0;
     }
     else
     {
       $start = filter_var($_GET['start'], FILTER_VALIDATE_INT);
       if ($start === false || $start < 0) {
         $start = 0;
       }
     }
     $stmt = $pdo->prepare("SELECT * FROM km_messages a, km_users b WHERE b.ID=a.posterid AND a.parentid='0' AND a.forumparent = ? ORDER BY a.time DESC LIMIT ?, 20");
     $stmt->execute([$forumID, $start]);
     while($getmessages3 = $stmt->fetch(PDO::FETCH_ASSOC))
       {
         $getmessages3['subject']=str_replace("';","@",$getmessages3['subject']);
         $getmessages3['subject']=str_replace('";','@',$getmessages3['subject']);
         $getmessages3['subject']=strip_tags($getmessages3['subject']);
         $subject = htmlspecialchars($getmessages3['subject'], ENT_QUOTES, 'UTF-8');
         $playername = htmlspecialchars($getmessages3['playername'], ENT_QUOTES, 'UTF-8');
         $realtime = htmlspecialchars($getmessages3['realtime'], ENT_QUOTES, 'UTF-8');
         print "<tr class='mainrow'><td>";
         if($getmessages3['time']>$getuser3['oldtime'])
         {
           print "<img src='../images/yesnewposts.gif' border='0'>";
         }
         else
         {
           print "<img src='../images/topic.gif' border='0'>";
         }

         print "</td><td><A href='messages.php?forumID=".htmlspecialchars($forumID, ENT_QUOTES, 'UTF-8')."&ID=".htmlspecialchars($getmessages3['msgid'], ENT_QUOTES, 'UTF-8')."'>$subject</a></td><td>$playername</td><td>".htmlspecialchars($getmessages3['numreplies'], ENT_QUOTES, 'UTF-8')."</td><td>$realtime</td></tr>";
       }
       print "</table><br><br>";
       print "<table border='0' width=90%>";
       print "<tr><td class='regrow'>";
       print "<p align='right'>";
       $stmt = $pdo->prepare("SELECT COUNT(*) FROM km_messages a, km_users b WHERE b.ID=a.posterid AND a.parentid='0' AND a.forumparent = ? ORDER BY time DESC");
       $stmt->execute([$forumID]);
       $d=0;
       $f=0;
       $g=1;
       $order3 = $stmt->fetchColumn();
       $prev=$start-20;
       $next=$start+20;
       print " Page: ";
       $forumID_safe = htmlspecialchars($forumID, ENT_QUOTES, 'UTF-8');
       if($start>=20)
       {
         print "<A href='forum.php?ID=$forumID_safe'>First</a>&nbsp&nbsp;&nbsp;";
         print "<A href='forum.php?ID=$forumID_safe&start=".htmlspecialchars($prev, ENT_QUOTES, 'UTF-8')."'><<</a>&nbsp;";
       }
       while($f<$order3)
       {
         if($f%20==0)
         {
           if($f>=$start-3*20&&$f<=$start+7*20)
           {
             print "<A href='forum.php?ID=$forumID_safe&start=".htmlspecialchars($d, ENT_QUOTES, 'UTF-8')."'>$g</a> ";
             $g++;
           }
         }
         $d=$d+1;
         $f++;
       }
       if($start<=$order3-$numtopicsperpage)
       {
         print "&nbsp;<A href='index.php?ID=$forumID_safe&start=".htmlspecialchars($next, ENT_QUOTES, 'UTF-8')."'>>></a>&nbsp;&nbsp;&nbsp;";
         $last=$order3-20;
         print "<A href='index.php?ID=$forumID_safe&start=".htmlspecialchars($last, ENT_QUOTES, 'UTF-8')."'>Last</a>";
       }
       print "</p></td></tr></table>";


}
else
{ 
    print "<table class='maintable'>";
    print "<tr class='headline'><td><center>Not logged in</center></td></tr>";
    print "<tr class='mainrow'><td>You are not logged in, please <A href='../login.php'>Login</a>";
    print "</td></tr></table>";
  

}
include "../down_html.php";
?>
     