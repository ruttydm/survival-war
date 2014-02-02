<?php
//killmonster admin main page, from here you can add and delete monsters
include "connect.php";
session_start();
?>
<?

if (isset($_SESSION['isadmin'])) //if there is an administrative session
  {
     print "<center><h3>Kill Monster Admin</h3></center><br>";
     print "<center>";
     print "<table border='0' width='70%' cellspacing='20'>";
     print "<tr><td width='25%' valign='top'>";
     include 'left.php';
     print "</td>";
     print "<td valign='top' width='75%'>";
     print "<table border='1' bordercolor='white' bgcolor='#e1e1e1'>";       
     print "<tr><td>Forum name</td><td>Forum Description</td><td>Edit</td><td>Delete</td></tr>"; 
     $getforums="SELECT * from km_forums order by forumname ASC"; // select forums in ABC order
     $getforums2=mysql_query($getforums) or die("Could not get forums");
     while($getforums3=mysql_fetch_array($getforums2))
     {
       print "<tr><td>$getforums3[forumname]</td><td>$getforums3[descrip]</td><td><A href='edit.php?ID=$getforums3[forumID]'>Edit</a></td><td><A href='delete.php?ID=$getforums3[forumID]'>Delete</a></td></tr>";
     }
     print "</table>";


     print "</td></tr></table>";    
     print "</center>";
     
  }
else //if not logged in as admin
  {
    print "Sorry, not logged in as administrator, please <A href='login.php'>Login</a>";
  }

?>