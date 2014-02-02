<?php
include 'connect.php';
require_once("functions.php");

$player 	= $_SESSION['player'];

$userstats 	= "SELECT
					*
				FROM
					km_users
				WHERE
					playername = '".$player."'";
$userstats2 = mysql_query($userstats) or die ("Could not get user stats");
$userstats3 = mysql_fetch_array($userstats2);
?>





<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Survival War</title>
	<meta name="description" content="Survival War online!">
	<meta name="keywords" content="browsergame, game, hunter, free, online, multyplayer, survival, apocalypse, war">
	<meta name="author" content="Ruttydm">
	<meta charset="UTF-8">
	<link href="/normalize.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style.css" />
	 <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
     <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
     <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
</head>
	
<body>
  <script>
  $(function() {
    $( "#dialog,#dialog2" ).dialog({ dialogClass: 'no-close', width: 600, height: 600});

  });


</script>
	
<header>
<h1>SURVIVAL WAR</h1><h6>V 1.0 Bèta</h6>
</header>
<div class="menu">
<ul>
<?php
if (isset($_SESSION['player'])){
echo"	  
<tr class='mainrow'><td><b>$userstats3[numturns] % energy</b></td></tr><br>
<tr class='mainrow'><td><b>skill pts: $userstats3[skillpts]</b></td></tr><br>
<tr class='mainrow'><td><b>Honor: $userstats3[honor]</b></td></tr><br>
<tr class='mainrow'><td><b>Gold: $userstats3[gold]</b></td></tr><br>
<tr class='mainrow'><td><b>Land: $userstats3[land]</b></td></tr><br>
<tr class='mainrow'><td><b>offensive army: $userstats3[offarmy]</b></td></tr><br>
<tr class='mainrow'><td><b>Defensive army: $userstats3[dffarmy]</b></td></tr><br>
<tr class='mainrow'><td><b>Science: $userstats3[science]</b></td></tr><br>

	  <li><a href='../index.php'>Home</a></li>
	  <li><a href='../attack.php'>Attack Land</a></li>
	  <li><a href='../buyarmy.php'>Buy Army</a></li>
	  <li><a href='../buyland.php'>Buy Land</a></li>
   	  <li><a href='../buyscience.php'>Buy Science</a></li>		
	  <li><a href='../challengeplayer.php'>Challenge Player</a></li>
	  <li><a href='../logs.php'>Logs</a></li>
	  <li><a href='../playerlist.php'>Playerlist</a></li>
	  <li><a href='../slaymonster.php'>Hunt Animal</a></li>
	  <li><a href='../forums/'>Forum</a></li>			
	  <li><a href='../logout.php'>Logout</a></li>";	
}		
else{
	echo"<b>Welcome to <br>Survival War!</b>";
}


		if($player == "ruttydm")
	{
			echo "<li><a href='../admin/login.php'>Admin</a></li>";
	}
?>
</ul>

</div>
<div class="content">