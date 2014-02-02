<?php
//killmonster admin main page, from here you can add and delete monsters

session_start();
if (isset($_SESSION['isadmin'])) 
  {
    print "<center><h3>Kill Monster Admin</h3></center><br>";
     print "<center>";
     print "<table border='0' width='70%' cellspacing='20'>";
     print "<tr><td width='25%' valign='top'>";
	 include 'left.php'; // admin panel
     print "</td>";
     print "<td valign='top' width='75%'>";
     print "Here is the Kill Monster Admin<br><br>";
     print "Create Monster -- Lets you Create a Monster<br><br>";
     print "Delete Monster -- lets you delete a monster<br><br>";
     print "User Management--Lets you edit and delete users"; 
     print "</td></tr></table>";    
     print "</center><br><br>";
     print "<font size='1'>Script Produced by © <A href='http://www.chipmunk-scripts.com'>Chipmunk Scripts</a></font>";
  }

  
else
  {
    print "Sorry, not logged in as administrator, please <A href='login.php'>Login</a>";
  }

?>