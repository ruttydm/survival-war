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
  print "<center><A href='../index.php'>Back to Main game</a></center><br>";
  print "<center><table class='maintable'><tr class='headline'><td colspan='2' width=75%>Forum name</td><td>Topics</td><td>Posts</td><td>Last Post</td></tr>";
  $stmt = $pdo->query("SELECT * FROM km_forums ORDER BY forumorder ASC");
  while($getforums3 = $stmt->fetch(PDO::FETCH_ASSOC))
  {
     print "<tr class='mainrow'><td width=3%>";

     if($getforums3['realtimelastpost']>$getuser3['oldtime'])
     {
       print "<img src='../images/postforum.jpg' border='0'>";
     }
     else
     {
       print "<img src='../images/postforum.gif' border='0'>";
     }

     $forumname = htmlspecialchars($getforums3['forumname'], ENT_QUOTES, 'UTF-8');
     $descrip = htmlspecialchars($getforums3['descrip'], ENT_QUOTES, 'UTF-8');
     $lastposter = htmlspecialchars($getforums3['lastposter'], ENT_QUOTES, 'UTF-8');
     $timelastpost = htmlspecialchars($getforums3['timelastpost'], ENT_QUOTES, 'UTF-8');
     print "</td><td><A href='forum.php?ID=".htmlspecialchars($getforums3['forumID'], ENT_QUOTES, 'UTF-8')."'>$forumname</a><br>$descrip</td><td>".htmlspecialchars($getforums3['numtopics'], ENT_QUOTES, 'UTF-8')."</td><td>".htmlspecialchars($getforums3['numposts'], ENT_QUOTES, 'UTF-8')."</td><td>$timelastpost<br>by<b>$lastposter</b></td></tr>";
  }
  print "</table>";


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
     