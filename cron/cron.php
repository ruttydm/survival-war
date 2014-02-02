<?php
include "../connect.php";
$getturns="Update km_users set numturns=numturns+15";
$getturns2=mysql_query($getturns) or die("Could not get turns");
?>