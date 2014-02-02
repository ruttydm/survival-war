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
    $numtopicsperpage=15;
    $forumID=$_GET['ID'];
    print "<table border='0' width=90%>";
    print "<tr><td><p align='left'><A href='../index.php'>Back to main game</a>-<A href='index.php'>Back to forum index</a></p></td><td colspan='3'><p align='right'><A href='post.php?forumid=$forumID'><b>New Thread</b></a></td></tr></table><br>";
    print "<table class='maintable'>";
    print "<tr class='headline'><td colspan='2'>Topic</td><td>Topic Starter</td><td>Replies</td><td>Last Post</td></tr>";
    if(!isset($_GET['start']))
    {
       $start=0;
     }
     else
     {
       $start=$_GET['start'];
     } 
     $getmessages="SELECT * from km_messages a,km_users b where b.ID=a.posterid and a.parentid='0' and a.forumparent='$forumID' order by a.time DESC limit $start, 20";
     $getmessages2=mysql_query($getmessages) or die(mysql_error());
     while($getmessages3=mysql_fetch_array($getmessages2))
       {
         $getmessages3[subject]=str_replace("';","@",$getmessages3[subject]);
         $getmessages3[subject]=str_replace('";','@',$getmessages3[subject]);
         $getmessages3[subject]=strip_tags($getmessages3[subject]);
         print "<tr class='mainrow'><td>";
         if($getmessages3['time']>$getuser3['oldtime'])
         {
           print "<img src='../images/yesnewposts.gif' border='0'>";
         }
         else
         {
           print "<img src='../images/topic.gif' border='0'>";
         }

         print "</td><td><A href='messages.php?forumID=$forumID&ID=$getmessages3[msgid]'>$getmessages3[subject]</a></td><td>$getmessages3[playername]</td><td>$getmessages3[numreplies]</td><td>$getmessages3[realtime]</td></tr>";
       }
       print "</table><br><br>";
       print "<table border='0' width=90%>";
       print "<tr><td class='regrow'>";
       print "<p align='right'>";
       $order="SELECT COUNT(*) from km_messages a,km_users b where b.ID=a.posterid and a.parentid='0' and a.forumparent='$forumID' order by time desc";
       $order2=mysql_query($order);
       $d=0;
       $f=0;
       $g=1;
       $order3=mysql_result($order2,0);
       $prev=$start-20;
       $next=$start+20;
       print " Page: ";
       if($start>=20)
       {
         print "<A href='forum.php?ID=$forumID'>First</a>&nbsp&nbsp;&nbsp;";
         print "<A href='forum.php?ID=$forumID&start=$prev'><<</a>&nbsp;";
       }
       while($f<$order3)
       {
         if($f%20==0)
         {
           if($f>=$start-3*20&&$f<=$start+7*20)
           {
             print "<A href='forum.php?ID=$forumID&start=$d'>$g</a> ";
             $g++;
           }
         }
         $d=$d+1;
         $f++;
       }
       if($start<=$order3-$numtopicsperpage)
       {
         print "&nbsp;<A href='index.php?ID=$forumID&start=$next'>>></a>&nbsp;&nbsp;&nbsp;";
         $last=$order3-20;
         print "<A href='index.php?ID=$forumID&start=$last'>Last</a>";
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
     