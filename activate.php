<?php
include "connect.php";
print "<link rel='stylesheet' href='style.css' type='text/css'>";
$username=$_GET['player'];
$password=$_GET['password'];
$keynode=$_GET['keynode'];
$getuserkeys="Select * from km_users where playername='$username' and password='$password' and validkey='$keynode'";
$getuserkeys2=mysql_query($getuserkeys) or die(mysql_error());
$getuserkeys3=mysql_fetch_array($getuserkeys2);
if(!$getuserkeys3)
{
  print "<table class='maintable'>";
  print "<tr class='headline'><td><center>Registering...</center></td></tr>";
  print "<tr class='forumrow'><td><center>";
  print "No such user.";
  print "</td></tr></table>";
}
else
{
  print "<table class='maintable'>";
  print "<tr class='headline'><td><center>Registering...</center></td></tr>";
  print "<tr class='forumrow'><td><center>";
  $update="Update km_users set validated='1' where playername='$username'";
  mysql_query($update) or die("Could not activate");
  print "Account activated";
  print "</center></td></tr></table>";
}
?>
  