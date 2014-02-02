<?php
session_start();
include "up_html.php";

       
print "<table class='maintable'><tr class='headline'><td><center>Ranks and Rules</center></td></tr>";
print "<tr class='mainrow'><td><A href='top.php'>Players by rank</a></td></tr>";
print "<tr class='mainrow'><td><A href='playerclose.php'>Players close to you</a></td></tr>";
print "<tr class='mainrow'><td><A href='tophonor.php'>Players by Honor</a></td></tr>";
print "<tr class='mainrow'><td><A href='topland.php'>Largest Fiefdoms(land)</a></td></tr>";
print "</td></tr></table><br><br>";

include "down_html.php";

?>
