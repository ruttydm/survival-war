<?php //revive player module
include "connect.php";
session_start();
if (isset($_SESSION['player'])) 
{
  if(isset($_POST['revives']))
  { 
     $ID=$_POST['ID']; 
     $rejuv="update km_users set dead='no' where ID='$ID'";
     mysql_query($rejuv) or die("Could not revive");
     print "<A href='index.php'>Go back to main page</a>";
  }

}

else
{
  print "Not logged in";
}

?>