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
  $getuser="SELECT * from km_users where playername='$playername'";
  $getuser2=mysql_query($getuser) or die("Could not get user info");
  $getuser3=mysql_fetch_array($getuser2);
  $thedate=date("U");
  $checktime=$thedate-200;
  $uprecords="Update km_users set lasttime='$thedate' where ID='$getuser3[ID]'";
  mysql_query($uprecords) or die("Could not update records");
  if($getuser3[tsgone]<$checktime)
  {
    $updatetime="Update km_users set tsgone='$thedate', oldtime='$getuser3[tsgone]' where ID='$getuser3[ID]'";
    mysql_query($updatetime) or die("Could not update time");
  }
  print "<center><A href='../index.php'>Back to Main game</a></center><br>";
  print "<center><table class='maintable'><tr class='headline'><td colspan='2' width=75%>Forum name</td><td>Topics</td><td>Posts</td><td>Last Post</td></tr>";
  $getforums="SELECT * from km_forums order by forumorder ASC";
  $getforums2=mysql_query($getforums) or die("COuld not get forums");
  while($getforums3=mysql_fetch_array($getforums2))
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

     print "</td><td><A href='forum.php?ID=$getforums3[forumID]'>$getforums3[forumname]</a><br>$getforums3[descrip]</td><td>$getforums3[numtopics]</td><td>$getforums3[numposts]</td><td>$getforums3[timelastpost]<br>by<b>$getforums3[lastposter]</b></td></tr>";
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
     