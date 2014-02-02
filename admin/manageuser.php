<?php
//killmonster admin main page, from here you can add and delete monsters
include "connect.php";
session_start();
?>
<?
if (isset($_SESSION['isadmin'])) //if there is an administrative session
  {
     $ID=$_GET['ID'];
     if(isset($ID)) //if submit has been pushed to delete user
     {
       print "<center><h3>Kill Monster Admin</h3></center><br>";
       print "<center>";
       print "<table border='0' width='70%' bordercolor='white'>";
       print "<tr><td width='25%' valign='top'>";
       include 'left.php';
       print "</td>";
       print "<td valign='top' width='75%'>";
       $del="Delete from km_users where ID='$ID'";
       mysql_query($del) or die('Could not delete user');
       print "User Deleted";
       print "</td></tr></table>";    
       print "</center>";
 

     }
     
     else
     {

       print "<center><h3>Kill Monster Admin</h3></center><br>";
       print "<center>";
       print "<table border='0' width='70%' cellspacing='20'>";
       print "<tr><td width='25%' valign='top'>";
       include 'left.php';
       print "</td>";
       print "<td valign='top' width='75%'>";
       print "All users listed in ABC order";
       global $start;
       if(!isset($start))
       {
          $start=0;
       }
       $userselect="SELECT * from km_users order by playername ASC limit $start, 20 ";
       $userselect2=mysql_query($userselect) or die("Could not select user");
       print "<table border='1' bordercolor='white' bgcolor='#e1e1e1'>";
       print "<tr><td>Username</td><td>E-mail</td><td>Delete</td></tr>";
       while($userselect3=mysql_fetch_array($userselect2))
       {
         print "<tr><td>$userselect3[playername]</td><td>$userselect3[email]</td><td><A href='manageuser.php?ID=$userselect3[ID]'>Delete</a></td></tr>";
       }
       print "</table>";       
       print "</td></tr></table>";    
       print "</center>";
     }

  $order="SELECT * from km_users";
$order2=mysql_query($order);
$d=0;
$f=0;
$g=1;




print "Page: ";
while($order3=mysql_fetch_array($order2))
{
if($f%20==0)
  {
    

    print "<A href='manageuser.php?start=$d'>$g</a> ";
    $g++;
  }
$d=$d+1;
$f++;

}
  }
else //if not logged in as admin
  {
    print "Sorry, not logged in as administrator, please <A href='login.php'>Login</a>";
  }

?>