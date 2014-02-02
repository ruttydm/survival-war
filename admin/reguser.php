<?php
include "connect.php";
?>
<link rel="stylesheet" href="style.css" type="text/css">
<?php
print "<center>";
print "<table class='maintable'>";
print "<tr class='headline'><td><font color='white'><center>Register</center></td></tr>";
print "<tr class='mainrow'><td><center>";
$username=$_POST['username'];
$password=$_POST['password'];
$password=md5($password);
$insertadmin="INSERT into km_users (playername,password,status,validated) values('$username','$password','3','1')";
mysql_query($insertadmin) or die(mysql_error());
print "Admin registered, you should probably delete the admin register files now. You can login to your admin account <A href='login.php'>Here</a>.";
print "</td></tr></table>";
?>
